<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    /**
     * GET /api/admin/users
     *
     * Query params:
     *   - search   : search by name, email, or phone
     *   - role     : filter by role (admin, customer)
     *   - per_page : items per page (default 15)
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        $query->orderByDesc('created_at');
        $perPage = min((int) $request->input('per_page', 15), 50);

        $users = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data'   => UserResource::collection($users),
            'meta'   => [
                'current_page' => $users->currentPage(),
                'last_page'    => $users->lastPage(),
                'per_page'     => $users->perPage(),
                'total'        => $users->total(),
            ],
        ]);
    }

    /**
     * GET /api/admin/users/{id}
     *
     * Show user details with rental history.
     */
    public function show(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $rentals = $user->rentals()
            ->with(['items.product', 'payment'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => [
                'user'    => new UserResource($user),
                'rentals' => \App\Http\Resources\RentalResource::collection($rentals),
                'stats'   => [
                    'total_rentals'  => $rentals->count(),
                    'total_spent'    => (float) $rentals->where('status', '!=', 'canceled')->sum('total_price'),
                    'active_rentals' => $rentals->whereIn('status', ['active', 'overdue'])->count(),
                ],
            ],
        ]);
    }

    /**
     * POST /api/admin/users
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'unique:users,email'],
            'password'     => ['required', 'string', 'min:6'],
            'role'         => ['sometimes', 'in:admin,customer'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'address'      => ['nullable', 'string'],
        ]);

        $user = User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'role'         => $request->role ?? 'customer',
            'phone_number' => $request->phone_number,
            'address'      => $request->address,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'User berhasil ditambahkan.',
            'data'    => new UserResource($user),
        ], 201);
    }

    /**
     * PUT /api/admin/users/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'         => ['sometimes', 'string', 'max:255'],
            'email'        => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
            'password'     => ['sometimes', 'string', 'min:6'],
            'role'         => ['sometimes', 'in:admin,customer'],
            'phone_number' => ['sometimes', 'nullable', 'string', 'max:20'],
            'address'      => ['sometimes', 'nullable', 'string'],
        ]);

        $data = $request->only(['name', 'email', 'role', 'phone_number', 'address']);

        if ($request->has('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'User berhasil diperbarui.',
            'data'    => new UserResource($user),
        ]);
    }

    /**
     * DELETE /api/admin/users/{id}
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        // Prevent deleting self
        if ($user->id === $request->user()->id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tidak bisa menghapus akun sendiri.',
            ], 422);
        }

        // Check for active rentals
        $activeRentals = $user->rentals()->whereIn('status', ['active', 'booked', 'overdue'])->count();
        if ($activeRentals > 0) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tidak bisa menghapus user yang memiliki rental aktif.',
            ], 422);
        }

        $user->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'User berhasil dihapus.',
        ]);
    }
}
