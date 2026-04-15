<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name'            => 'Eiger Rhinos Carrier 45L',
                'category'        => 'Carrier',
                'gender'          => 'unisex',
                'description'     => "Carrier berkualitas tinggi dengan kapasitas 45 liter. Dirancang untuk pendakian gunung menengah hingga berat dengan sistem suspensi ergonomis yang mengurangi beban di pundak.\n\nFitur utama:\n- Bahan Cordura 500D tahan air\n- Rain cover built-in\n- Kompartemen sleeping bag terpisah\n- Tali webbing untuk trekking pole\n- Hydration bladder compatible",
                'image'           => null,
                'price_24h'       => 65000,
                'stock_total'     => 10,
                'stock_available' => 8,
                'sizes'           => [
                    ['size' => 'M', 'stock' => 4],
                    ['size' => 'L', 'stock' => 4],
                ],
            ],
            [
                'name'            => 'Evolite 2-Person Tent',
                'category'        => 'Tenda',
                'gender'          => 'unisex',
                'description'     => "Tenda kapasitas 2 orang yang ringan dan mudah dipasang. Cocok untuk camping di hutan maupun pegunungan dengan ketinggian hingga 3.500 mdpl.\n\nSpesifikasi:\n- Berat: 2.5 kg\n- Waterproof rating: 3000mm\n- Frame aluminium alloy\n- Ventilasi ganda anti embun\n- Setup time: 5 menit",
                'image'           => null,
                'price_24h'       => 85000,
                'stock_total'     => 6,
                'stock_available' => 4,
                'sizes'           => [],
            ],
            [
                'name'            => 'Gore-Tex Windbreaker Jacket',
                'category'        => 'Apparel',
                'gender'          => 'pria',
                'description'     => "Jaket windbreaker ringan dengan teknologi Gore-Tex yang tahan angin dan hujan ringan. Ideal untuk hiking, camping, dan aktivitas outdoor lainnya.\n\nFitur:\n- Bahan Gore-Tex Paclite\n- Packing ringkas\n- Sealed seams\n- Hood adjustable\n- Zip pocket",
                'image'           => null,
                'price_24h'       => 45000,
                'stock_total'     => 15,
                'stock_available' => 12,
                'sizes'           => [
                    ['size' => 'S', 'stock' => 3],
                    ['size' => 'M', 'stock' => 4],
                    ['size' => 'L', 'stock' => 3],
                    ['size' => 'XL', 'stock' => 2],
                ],
            ],
            [
                'name'            => 'Portable Cooking Set Aluminium',
                'category'        => 'Cooking',
                'gender'          => 'unisex',
                'description'     => "Set peralatan masak portable dari aluminium ringan. 1 set terdiri dari panci, wajan, cangkir, dan sendok-garpu lipat.\n\nCocok untuk:\n- Solo camping\n- Pendakian ringan\n- Traveling",
                'image'           => null,
                'price_24h'       => 20000,
                'stock_total'     => 20,
                'stock_available' => 18,
                'sizes'           => [],
            ],
            [
                'name'            => 'Carbon Trekking Pole Pair',
                'category'        => 'Accessories',
                'gender'          => 'unisex',
                'description'     => "Sepasang trekking pole dari bahan carbon fiber. Sangat ringan namun kokoh, dengan sistem quick-lock untuk penyesuaian tinggi.\n\nSpesifikasi:\n- Berat: 210g per pole\n- Panjang: 65-135cm (adjustable)\n- Grip: Cork & EVA foam\n- Tip: Tungsten carbide",
                'image'           => null,
                'price_24h'       => 25000,
                'stock_total'     => 12,
                'stock_available' => 10,
                'sizes'           => [],
            ],
            [
                'name'            => 'Sleeping Bag Mummy -5°C',
                'category'        => 'Sleeping',
                'gender'          => 'unisex',
                'description'     => "Sleeping bag tipe mummy yang nyaman hingga suhu -5°C. Dilengkapi dengan hood dan zipper dua arah.\n\nSpesifikasi:\n- Isi: Synthetic hollow fiber\n- Berat: 1.4 kg\n- Comfort temp: 0°C\n- Extreme temp: -5°C",
                'image'           => null,
                'price_24h'       => 35000,
                'stock_total'     => 8,
                'stock_available' => 6,
                'sizes'           => [],
            ],
            [
                'name'            => 'Matras Foam Self-Inflating',
                'category'        => 'Sleeping',
                'gender'          => 'unisex',
                'description'     => "Matras self-inflating dengan ketebalan 5cm. Nyaman dan hangat untuk tidur di alam terbuka.\n\nFitur:\n- Self-inflating valve\n- R-value: 3.5\n- Anti-slip bottom\n- Compact roll size",
                'image'           => null,
                'price_24h'       => 15000,
                'stock_total'     => 15,
                'stock_available' => 13,
                'sizes'           => [],
            ],
            [
                'name'            => 'Headlamp LED 300 Lumens',
                'category'        => 'Accessories',
                'gender'          => 'unisex',
                'description'     => "Headlamp LED 300 lumens dengan 3 mode pencahayaan (high, low, strobe). Tahan air IPX4, baterai rechargeable via USB-C.\n\nSpesifikasi:\n- Lumens: 300\n- Runtime: 8 jam (low), 4 jam (high)\n- Charging: USB-C\n- Berat: 85g",
                'image'           => null,
                'price_24h'       => 10000,
                'stock_total'     => 25,
                'stock_available' => 22,
                'sizes'           => [],
            ],
            [
                'name'            => 'Hiking Boots Waterproof',
                'category'        => 'Footwear',
                'gender'          => 'pria',
                'description'     => "Sepatu hiking waterproof dengan ankle support tinggi. Outsole Vibram untuk traksi maksimal di berbagai medan.\n\nFitur:\n- Upper: Nubuck leather + mesh\n- Membrane waterproof\n- Outsole: Vibram\n- Midsole: EVA + TPU shank",
                'image'           => null,
                'price_24h'       => 40000,
                'stock_total'     => 10,
                'stock_available' => 3,
                'sizes'           => [
                    ['size' => '40', 'stock' => 1],
                    ['size' => '41', 'stock' => 0],
                    ['size' => '42', 'stock' => 1],
                    ['size' => '43', 'stock' => 1],
                ],
            ],
            [
                'name'            => 'Carrier Deuter 60L Pro',
                'category'        => 'Carrier',
                'gender'          => 'unisex',
                'description'     => "Carrier premium 60 liter untuk ekspedisi panjang. Sistem Aircontact back system menjaga sirkulasi udara di punggung.\n\nFitur:\n- Kapasitas: 60+10L\n- Berat: 2.8 kg\n- Aircontact back system\n- SOS label\n- Rain cover included",
                'image'           => null,
                'price_24h'       => 85000,
                'stock_total'     => 5,
                'stock_available' => 2,
                'sizes'           => [
                    ['size' => 'M', 'stock' => 1],
                    ['size' => 'L', 'stock' => 1],
                ],
            ],
            [
                'name'            => 'Kompor Gas Portable Windproof',
                'category'        => 'Cooking',
                'gender'          => 'unisex',
                'description'     => "Kompor gas portable windproof dengan piezo ignition. Cocok untuk memasak di kondisi berangin.\n\nSpesifikasi:\n- Output: 3500W\n- Berat: 350g\n- Gas: Butane/Propane\n- Windscreen built-in",
                'image'           => null,
                'price_24h'       => 15000,
                'stock_total'     => 18,
                'stock_available' => 15,
                'sizes'           => [],
            ],
            [
                'name'            => 'Rain Poncho Military Grade',
                'category'        => 'Apparel',
                'gender'          => 'unisex',
                'description'     => "Poncho hujan serbaguna yang bisa diubah menjadi tarp shelter darurat. Bahan ripstop nylon dengan coating silikon.\n\nFungsi:\n- Jas hujan\n- Ground sheet\n- Emergency shelter\n- Picnic mat",
                'image'           => null,
                'price_24h'       => 12000,
                'stock_total'     => 20,
                'stock_available' => 18,
                'sizes'           => [],
            ],
        ];

        foreach ($products as $data) {
            $sizes = $data['sizes'];
            unset($data['sizes']);

            $product = Product::create($data);

            foreach ($sizes as $size) {
                ProductSize::create([
                    'product_id' => $product->id,
                    'size'       => $size['size'],
                    'stock'      => $size['stock'],
                ]);
            }
        }
    }
}
