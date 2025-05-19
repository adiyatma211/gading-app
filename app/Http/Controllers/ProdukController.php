<?php

namespace App\Http\Controllers;

use App\Models\Produk;

use App\Models\ProdukBahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreProdukRequest;
use App\Http\Requests\UpdateProdukRequest;

class ProdukController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validasi data
            $request->validate([
                'nama_produk' => 'required|string',
                'bahan.*.nama_bahan' => 'required|string',
                'bahan.*.harga_per_meter' => 'required|numeric|min:0',
                'bahan.*.diskon' => 'nullable|numeric|min:0',
                'bahan.*.total_harga' => 'nullable|numeric|min:0',
            ]);

            DB::beginTransaction();

            // Simpan Produk
            $produk = Produk::create([
                'nama_produk' => $request->input('nama_produk'),
                'status' => 1
            ]);

            // Simpan Bahan terkait
            foreach ($request->input('bahan') as $bahan) {
                $produk->bahan()->create([
                    'nama_bahan' => $bahan['nama_bahan'],
                    'harga_per_meter' => $bahan['harga_per_meter'],
                    'diskon' => $bahan['diskon'] ?? 0,
                    'total_harga' => $bahan['total_harga'] ?? 0
                ]);
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Produk berhasil ditambahkan',
                'data' => $produk->load('bahan')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Gagal menambahkan produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $produk = Produk::with('bahan')->findOrFail($id);
        return response()->json([
            'status' => true,
            'message' => 'Produk ditemukan',
            'data' => $produk
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_produk' => 'required|string',
            'bahan.*.nama_bahan' => 'required|string',
            'bahan.*.harga_per_meter' => 'required|numeric|min:0',
            'bahan.*.diskon' => 'nullable|numeric|min:0',
            'bahan.*.total_harga' => 'nullable|numeric|min:0',
        ]);

        $produk = Produk::findOrFail($id);
        $produk->update(['nama_produk' => $request->input('nama_produk')]);

        // Ambil semua ID bahan yang dikirim dari form (yang masih dipertahankan atau diubah)
        $submittedIds = collect($request->input('bahan'))->pluck('id')->filter()->all();
        $produk->bahan()->whereNotIn('id', $submittedIds)->delete();

        // Update atau insert bahan
        foreach ($request->input('bahan', []) as $bahan) {
            if (!empty($bahan['id'])) {
                $produk->bahan()->where('id', $bahan['id'])->update([
                    'nama_bahan' => $bahan['nama_bahan'],
                    'harga_per_meter' => $bahan['harga_per_meter'],
                    'diskon' => $bahan['diskon'] ?? 0,
                    'total_harga' => $bahan['total_harga'] ?? 0
                ]);
            } else {
                $produk->bahan()->create([
                    'nama_bahan' => $bahan['nama_bahan'],
                    'harga_per_meter' => $bahan['harga_per_meter'],
                    'diskon' => $bahan['diskon'] ?? 0,
                    'total_harga' => $bahan['total_harga'] ?? 0
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Produk berhasil diperbarui',
            'data' => $produk->load('bahan')
        ]);
    }

    /**
     * Update a specific bahan.
     */
    public function updateBahan(Request $request, $id)
    {
        try {
            $request->validate([
                'nama_bahan' => 'required|string',
                'harga_per_meter' => 'required|numeric|min:0',
                'diskon' => 'nullable|numeric|min:0',
                'total_harga' => 'nullable|numeric|min:0',
            ]);

            $bahan = ProdukBahan::findOrFail($id);
            $bahan->update([
                'nama_bahan' => $request->nama_bahan,
                'harga_per_meter' => $request->harga_per_meter,
                'diskon' => $request->diskon ?? 0,
                'total_harga' => $request->total_harga ?? max($request->harga_per_meter - ($request->diskon ?? 0), 0)
            ]);

            return response()->json(['status' => true, 'message' => 'Bahan berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal memperbarui bahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $produk = Produk::findOrFail($id);
            $produk->bahan()->delete();
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

    /**
     * Hapus bahan secara individual.
     */
    public function hapusBahan($id)
    {
        $bahan = ProdukBahan::find($id);
        if (!$bahan) {
            return response()->json(['status' => false, 'message' => 'Bahan tidak ditemukan']);
        }

        $bahan->delete();
        return response()->json(['status' => true, 'message' => 'Bahan berhasil dihapus']);
    }
}
