<?php

namespace App\Http\Controllers\Pengaturan;

use App\Models\Roles;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateRolesRequest;

class RolesController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $role = Roles::create([
                    'roles' => $request->input('role'),
                    'keterangan' => $request->input('keterangan'),
                    'deleteSts' => '0',
                ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Role berhasil ditambahkan',
                'data' => $role
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $role = Roles::findOrFail($id);

            $role->update([
                'roles' => $request->input('role'),
                'keterangan' => $request->input('keterangan'),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Role berhasil diperbarui',
                'data' => $role
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui role: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $role = Roles::findOrFail($id);

            $role->update([
                'deleteSts' => '1',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Role berhasil dimatikan'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mematikan role: ' . $e->getMessage()
            ], 500);
        }
    }
}
