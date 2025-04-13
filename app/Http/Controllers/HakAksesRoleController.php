<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class HakAksesRoleController extends Controller
{
    public function searchUserName(Request $request)
    {
        $term = $request->get('term');  // Ambil input term pencarian

        // Cari pengguna berdasarkan nama
        $users = User::where('name', 'LIKE', "%{$term}%")
            ->limit(10)  // Batasi jumlah hasil yang dikembalikan (misalnya 10 hasil)
            ->get();

        // Format hasil pencarian
        $results = $users->map(function($user) {
            return [
                'id' => $user->id,  // ID pengguna, yang akan digunakan untuk memilih pengguna
                'label' => $user->name,  // Nama pengguna yang akan ditampilkan di dropdown
                'username' => $user->username  // Username pengguna yang akan ditampilkan
            ];
        });

        // Kembalikan data dalam format JSON
        return response()->json($results);
    }
}
