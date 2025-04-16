<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class HakAksesRoleController extends Controller
{

    public function storeUser(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users,username',
                'role_id' => 'required',
            ]);
            // dd($request);

            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'password' => Hash::make('12345678'),
                'deleteSts' => 0,
                'role_id' => $request->role_id
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User berhasil ditambahkan dengan password default 12345678.',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            // Optional: log error ke file log Laravel
            Log::error('Gagal tambah user: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menambahkan user.',
                'error' => $e->getMessage() // bisa dihapus saat produksi
            ], 500);
        }
    }
    public function searchUserName(Request $request)
    {
        $term = $request->get('term');

        $users = User::where('name', 'LIKE', "%{$term}%")
                    ->orWhere('username', 'LIKE', "%{$term}%")
                    ->limit(10)
                    ->get();

        $results = $users->map(function($user) {
            return [
                'id' => $user->id,
                'label' => $user->name . " (" . $user->username . ")",
                'username' => $user->username
            ];
        });

        return response()->json($results);
    }
    public function updateUserRole(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'role_id' => 'required|exists:roles,id'
            ]);

            $user = User::findOrFail($id);
            $user->name = $request->name;
            $user->role_id = $request->role_id;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Role dan nama pengguna berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal memperbarui data pengguna.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function deleteUser($id)
    {
        try {
            $user = User::findOrFail($id);

            // Soft delete (nonaktifkan user) → atau pakai $user->delete() untuk hard delete
            $user->deleteSts = 1;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'User berhasil dihapus (nonaktifkan).'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus user.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
