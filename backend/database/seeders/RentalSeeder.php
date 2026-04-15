<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Product;
use App\Models\Rental;
use App\Models\RentalItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RentalSeeder extends Seeder
{
    public function run(): void
    {
        // ── Create customer users ───────────────────────────────
        $customers = [
            [
                'name'         => 'Budi Santoso',
                'email'        => 'budi.santoso@gmail.com',
                'role'         => 'customer',
                'phone_number' => '081234567890',
                'address'      => 'Jl. Merdeka No. 45, Bandung',
                'password'     => Hash::make('password123'),
            ],
            [
                'name'         => 'Siti Nurhaliza',
                'email'        => 'siti.nurhaliza@gmail.com',
                'role'         => 'customer',
                'phone_number' => '082345678901',
                'address'      => 'Jl. Braga No. 12, Bandung',
                'password'     => Hash::make('password123'),
            ],
            [
                'name'         => 'Ahmad Rizki',
                'email'        => 'ahmad.rizki@gmail.com',
                'role'         => 'customer',
                'phone_number' => '083456789012',
                'address'      => 'Jl. Dago No. 78, Bandung',
                'password'     => Hash::make('password123'),
            ],
            [
                'name'         => 'Dewi Lestari',
                'email'        => 'dewi.lestari@gmail.com',
                'role'         => 'customer',
                'phone_number' => '084567890123',
                'address'      => 'Jl. Setiabudi No. 33, Bandung',
                'password'     => Hash::make('password123'),
            ],
        ];

        $userModels = [];
        foreach ($customers as $data) {
            $userModels[] = User::firstOrCreate(
                ['email' => $data['email']],
                $data
            );
        }

        [$budi, $siti, $ahmad, $dewi] = $userModels;

        // ── Get admin user ──────────────────────────────────────
        $admin = User::where('role', 'admin')->first();
        $adminId = $admin ? $admin->id : null;

        // ── Product references (by ID from ProductSeeder) ───────
        $products = Product::all()->keyBy('id');

        // ── Helper: create rental ───────────────────────────────
        $createRental = function (array $rentalData, array $items, ?array $paymentData) use ($products) {
            $rental = Rental::create($rentalData);

            foreach ($items as $item) {
                RentalItem::create([
                    'rental_id'      => $rental->id,
                    'product_id'     => $item['product_id'],
                    'quantity'       => $item['quantity'],
                    'price_at_rental' => $products[$item['product_id']]->price_24h,
                ]);
            }

            if ($paymentData) {
                Payment::create(array_merge(['rental_id' => $rental->id], $paymentData));
            }

            return $rental;
        };

        // ═══════════════════════════════════════════════════════
        // RENTAL 1: Budi — RETURNED (sudah dikembalikan)
        // Eiger Carrier 45L (1) + Sleeping Bag (1) + Headlamp (1)
        // ═══════════════════════════════════════════════════════
        $createRental(
            [
                'user_id'        => $budi->id,
                'admin_id'       => $adminId,
                'invoice_no'     => 'INV-20260401-001',
                'start_date'     => Carbon::parse('2026-04-01 08:00:00'),
                'end_date'       => Carbon::parse('2026-04-02 08:00:00'),
                'return_date'    => Carbon::parse('2026-04-02 07:30:00'),
                'total_price'    => 110000, // 65000 + 35000 + 10000
                'fine_amount'    => 0,
                'status'         => 'returned',
                'guarantee_info' => 'KTP - 3201150405980001',
            ],
            [
                ['product_id' => 1, 'quantity' => 1], // Eiger Carrier 45L
                ['product_id' => 6, 'quantity' => 1], // Sleeping Bag
                ['product_id' => 8, 'quantity' => 1], // Headlamp
            ],
            [
                'amount'         => 110000,
                'payment_method' => 'qris',
                'status'         => 'verified',
            ]
        );

        // ═══════════════════════════════════════════════════════
        // RENTAL 2: Siti — RETURNED
        // Evolite Tent (1) + Matras Foam (1) + Cooking Set (1)
        // ═══════════════════════════════════════════════════════
        $createRental(
            [
                'user_id'        => $siti->id,
                'admin_id'       => $adminId,
                'invoice_no'     => 'INV-20260403-002',
                'start_date'     => Carbon::parse('2026-04-03 09:00:00'),
                'end_date'       => Carbon::parse('2026-04-04 09:00:00'),
                'return_date'    => Carbon::parse('2026-04-04 08:45:00'),
                'total_price'    => 120000, // 85000 + 15000 + 20000
                'fine_amount'    => 0,
                'status'         => 'returned',
                'guarantee_info' => 'KTP - 3201154507990002',
            ],
            [
                ['product_id' => 2, 'quantity' => 1], // Evolite Tent
                ['product_id' => 7, 'quantity' => 1], // Matras Foam
                ['product_id' => 4, 'quantity' => 1], // Cooking Set
            ],
            [
                'amount'         => 120000,
                'payment_method' => 'cash',
                'status'         => 'verified',
            ]
        );

        // ═══════════════════════════════════════════════════════
        // RENTAL 3: Ahmad — ACTIVE (sedang disewa)
        // Gore-Tex Jacket (1) + Trekking Pole (1) + Hiking Boots (1)
        // Stock harus dikurangi
        // ═══════════════════════════════════════════════════════
        $createRental(
            [
                'user_id'        => $ahmad->id,
                'admin_id'       => $adminId,
                'invoice_no'     => 'INV-20260405-003',
                'start_date'     => Carbon::parse('2026-04-05 10:00:00'),
                'end_date'       => Carbon::parse('2026-04-14 10:00:00'),
                'return_date'    => null,
                'total_price'    => 990000, // (45000 + 25000 + 40000) × 9 days
                'fine_amount'    => 0,
                'status'         => 'active',
                'guarantee_info' => 'KTP - 3201151203000003',
            ],
            [
                ['product_id' => 3, 'quantity' => 1], // Gore-Tex Jacket
                ['product_id' => 5, 'quantity' => 1], // Trekking Pole
                ['product_id' => 9, 'quantity' => 1], // Hiking Boots
            ],
            [
                'amount'         => 990000,
                'payment_method' => 'qris',
                'status'         => 'verified',
            ]
        );

        // Kurangi stock untuk rental active
        Product::find(3)->decrement('stock_available', 1);
        Product::find(5)->decrement('stock_available', 1);
        Product::find(9)->decrement('stock_available', 1);

        // ═══════════════════════════════════════════════════════
        // RENTAL 4: Dewi — ACTIVE
        // Carrier Deuter 60L (1) + Sleeping Bag (1) + Poncho (1)
        // ═══════════════════════════════════════════════════════
        $createRental(
            [
                'user_id'        => $dewi->id,
                'admin_id'       => $adminId,
                'invoice_no'     => 'INV-20260407-004',
                'start_date'     => Carbon::parse('2026-04-07 08:00:00'),
                'end_date'       => Carbon::parse('2026-04-15 08:00:00'),
                'return_date'    => null,
                'total_price'    => 1056000, // (85000 + 35000 + 12000) × 8 days
                'fine_amount'    => 0,
                'status'         => 'active',
                'guarantee_info' => 'KTP - 3201156708010004',
            ],
            [
                ['product_id' => 10, 'quantity' => 1], // Carrier Deuter 60L
                ['product_id' => 6,  'quantity' => 1], // Sleeping Bag
                ['product_id' => 12, 'quantity' => 1], // Poncho
            ],
            [
                'amount'         => 1056000,
                'payment_method' => 'cash',
                'status'         => 'verified',
            ]
        );

        Product::find(10)->decrement('stock_available', 1);
        Product::find(6)->decrement('stock_available', 1);
        Product::find(12)->decrement('stock_available', 1);

        // ═══════════════════════════════════════════════════════
        // RENTAL 5: Budi — BOOKED (menunggu pickup)
        // Evolite Tent (1) + Kompor Gas (1) + Cooking Set (1)
        // ═══════════════════════════════════════════════════════
        $createRental(
            [
                'user_id'        => $budi->id,
                'admin_id'       => null,
                'invoice_no'     => 'INV-20260410-005',
                'start_date'     => Carbon::parse('2026-04-15 08:00:00'),
                'end_date'       => Carbon::parse('2026-04-17 08:00:00'),
                'return_date'    => null,
                'total_price'    => 240000, // (85000 + 15000 + 20000) × 2 days
                'fine_amount'    => 0,
                'status'         => 'booked',
                'guarantee_info' => 'KTP - 3201150405980001',
            ],
            [
                ['product_id' => 2,  'quantity' => 1], // Evolite Tent
                ['product_id' => 11, 'quantity' => 1], // Kompor Gas
                ['product_id' => 4,  'quantity' => 1], // Cooking Set
            ],
            [
                'amount'         => 240000,
                'payment_method' => 'qris',
                'status'         => 'pending',
            ]
        );

        Product::find(2)->decrement('stock_available', 1);
        Product::find(11)->decrement('stock_available', 1);
        Product::find(4)->decrement('stock_available', 1);

        // ═══════════════════════════════════════════════════════
        // RENTAL 6: Ahmad — CANCELED (dibatalkan, stock dikembalikan)
        // Eiger Carrier 45L (1) + Matras Foam (1)
        // ═══════════════════════════════════════════════════════
        $createRental(
            [
                'user_id'        => $ahmad->id,
                'admin_id'       => null,
                'invoice_no'     => 'INV-20260411-006',
                'start_date'     => Carbon::parse('2026-04-12 08:00:00'),
                'end_date'       => Carbon::parse('2026-04-13 08:00:00'),
                'return_date'    => null,
                'total_price'    => 80000, // 65000 + 15000
                'fine_amount'    => 0,
                'status'         => 'canceled',
                'guarantee_info' => 'KTP - 3201151203000003',
            ],
            [
                ['product_id' => 1, 'quantity' => 1], // Eiger Carrier 45L
                ['product_id' => 7, 'quantity' => 1], // Matras Foam
            ],
            null // Tidak ada payment untuk yang canceled
        );

        // ═══════════════════════════════════════════════════════
        // RENTAL 7: Siti — BOOKED
        // Headlamp (2) + Trekking Pole (1) + Poncho (1)
        // ═══════════════════════════════════════════════════════
        $createRental(
            [
                'user_id'        => $siti->id,
                'admin_id'       => null,
                'invoice_no'     => 'INV-20260412-007',
                'start_date'     => Carbon::parse('2026-04-16 08:00:00'),
                'end_date'       => Carbon::parse('2026-04-18 08:00:00'),
                'return_date'    => null,
                'total_price'    => 114000, // (10000×2 + 25000 + 12000) × 2 days
                'fine_amount'    => 0,
                'status'         => 'booked',
                'guarantee_info' => 'KTP - 3201154507990002',
            ],
            [
                ['product_id' => 8,  'quantity' => 2], // Headlamp ×2
                ['product_id' => 5,  'quantity' => 1], // Trekking Pole
                ['product_id' => 12, 'quantity' => 1], // Poncho
            ],
            [
                'amount'         => 114000,
                'payment_method' => 'qris',
                'status'         => 'pending',
            ]
        );

        Product::find(8)->decrement('stock_available', 2);
        Product::find(5)->decrement('stock_available', 1);
        Product::find(12)->decrement('stock_available', 1);

        // ═══════════════════════════════════════════════════════
        // RENTAL 8: Dewi — OVERDUE (telat 2 hari)
        // Gore-Tex Jacket (1) + Hiking Boots (1)
        // Denda: 2 hari × 10% × 85000 = 17000
        // ═══════════════════════════════════════════════════════
        $createRental(
            [
                'user_id'        => $dewi->id,
                'admin_id'       => $adminId,
                'invoice_no'     => 'INV-20260408-008',
                'start_date'     => Carbon::parse('2026-04-08 08:00:00'),
                'end_date'       => Carbon::parse('2026-04-11 08:00:00'),
                'return_date'    => null,
                'total_price'    => 255000, // (45000 + 40000) × 3 days
                'fine_amount'    => 25500, // 2 days late × 10% × 255000 / 2 (prorated)
                'status'         => 'overdue',
                'guarantee_info' => 'KTP - 3201156708010004',
            ],
            [
                ['product_id' => 3, 'quantity' => 1], // Gore-Tex Jacket
                ['product_id' => 9, 'quantity' => 1], // Hiking Boots
            ],
            [
                'amount'         => 255000,
                'payment_method' => 'qris',
                'status'         => 'verified',
            ]
        );

        Product::find(3)->decrement('stock_available', 1);
        Product::find(9)->decrement('stock_available', 1);
    }
}
