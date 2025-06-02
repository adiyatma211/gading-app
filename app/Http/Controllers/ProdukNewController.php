<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use App\Models\HargaProdukNew;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ProdukNewController extends Controller
{
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama_produk' => 'required|string|max:255',
            'tipe_produk' => ['required', Rule::in(['per_meter', 'tiered', 'flat', 'custom'])],
            'harga.*.harga' => 'required|numeric|min:0',
            'harga.*.diskon' => 'nullable|numeric|min:0',
            'harga.*.min_qty' => 'nullable|integer',
            'harga.*.max_qty' => 'nullable|integer',
            'harga.*.sisi' => 'nullable|in:1,2',
            'harga.*.laminasi' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Simpan produk
            $produk = Produk::create([
                'nama_produk'   => $request->nama_produk,
                'tipe_produk'   => $request->tipe_produk,
                'status'        => '1'
            ]);

            // Simpan harga
            foreach ($request->harga as $hargaData) {
                HargaProdukNew::create([
                    'produk_id' => $produk->id,
                    'harga' => $hargaData['harga'] ?? 0,
                    'min_qty' => $hargaData['min_qty'] ?? null,
                    'max_qty' => $hargaData['max_qty'] ?? null,
                    'sisi' => $hargaData['sisi'] ?? null,
                    'diskon' => $hargaData['diskon'] ?? null,
                    'laminasi' => $hargaData['laminasi'] ?? false,
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Produk berhasil ditambahkan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 🔹 Edit Produk
    public function edit($id)
    {
        $produk = Produk::with('hargas')->find($id);

        if (!$produk) {
            return response()->json([
                'status' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $produk
        ]);
    }

    // 🔹 Update Produk
    public function update(Request $request, $id)
    {
        $produk = Produk::find($id);
        if (!$produk) {
            return response()->json([
                'status' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama_produk' => 'required|string|max:255',
            'tipe_produk' => ['required', Rule::in(['per_meter', 'tiered', 'flat', 'custom'])],
            'harga.*.harga' => 'required|numeric|min:0',
            'harga.*.diskon' => 'nullable|numeric|min:0',
            'harga.*.min_qty' => 'nullable|integer',
            'harga.*.max_qty' => 'nullable|integer',
            'harga.*.sisi' => 'nullable|in:1,2',
            'harga.*.laminasi' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Update produk
            $produk->update([
                'nama_produk' => $request->nama_produk,
                'tipe_produk' => $request->tipe_produk,
            ]);

            // Hapus semua harga lama
            $produk->hargas()->delete();

            // Simpan harga baru
            foreach ($request->harga as $hargaData) {
                HargaProdukNew::create([
                    'produk_id' => $produk->id,
                    'harga' => $hargaData['harga'] ?? 0,
                    'min_qty' => $hargaData['min_qty'] ?? null,
                    'max_qty' => $hargaData['max_qty'] ?? null,
                    'sisi' => $hargaData['sisi'] ?? null,
                    'diskon' => $hargaData['diskon'] ?? null,
                    'laminasi' => $hargaData['laminasi'] ?? false,
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Produk berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal memperbarui produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 🔹 Hapus Harga
    public function destroyHarga($id)
    {
        $harga = HargaProdukNew::find($id);
        if (!$harga) {
            return response()->json([
                'status' => false,
                'message' => 'Harga tidak ditemukan'
            ], 404);
        }

        try {
            $harga->delete();
            return response()->json([
                'status' => true,
                'message' => 'Harga berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus harga',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 🔹 Hapus Produk
    public function destroy($id)
    {
        $produk = Produk::find($id);
        if (!$produk) {
            return response()->json([
                'status' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        try {
            $produk->hargas()->delete(); // Hapus semua harga terkait
            $produk->delete();

            return response()->json([
                'status' => true,
                'message' => 'Produk berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 🔹 Update Harga (misalnya dari modal edit)
    public function updateHarga(Request $request, $id)
    {
        $harga = HargaProdukNew::find($id);
        if (!$harga) {
            return response()->json([
                'status' => false,
                'message' => 'Harga tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'harga' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $harga->update([
                'harga' => $request->input('harga')
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Harga berhasil diubah'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengubah harga',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
