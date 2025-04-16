<?php

namespace App\Http\Controllers;

use App\Models\Produk;

use App\Models\ProdukBahan;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProdukRequest;
use App\Http\Requests\UpdateProdukRequest;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama_produk' => 'required|string',
                'bahan.*.nama_bahan' => 'required|string',
                'bahan.*.harga_per_meter' => 'required|numeric',
            ]);

            $produk = Produk::create([
                'nama_produk' => $request->input('nama_produk'),
                'status' => 1
            ]);

            foreach ($request->input('bahan') as $bahan) {
                $produk->bahan()->create($bahan);
            }

            return response()->json([
                'status' => true,
                'message' => 'Produk berhasil ditambahkan',
                'data' => $produk->load('bahan')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menambahkan produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Produk $produk)
    {
        //
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

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_produk' => 'required|string',
            'bahan.*.nama_bahan' => 'required|string',
            'bahan.*.harga_per_meter' => 'required|numeric',
        ]);

        $produk = Produk::findOrFail($id);
        $produk->update(['nama_produk' => $request->input('nama_produk')]);

        // Ambil semua ID bahan yang dikirim dari form (yang masih dipertahankan atau diubah)
        $submittedIds = collect($request->input('bahan'))->pluck('id')->filter()->all();

        // Hapus bahan yang tidak disertakan dalam input (berarti dihapus manual di UI)
        $produk->bahan()->whereNotIn('id', $submittedIds)->delete();

        // Update atau insert bahan
        foreach ($request->input('bahan', []) as $bahan) {
            if (!empty($bahan['id'])) {
                // Update bahan yang sudah ada
                $produk->bahan()->where('id', $bahan['id'])->update([
                    'nama_bahan' => $bahan['nama_bahan'],
                    'harga_per_meter' => $bahan['harga_per_meter'],
                ]);
            } else {
                // Tambah bahan baru
                $produk->bahan()->create([
                    'nama_bahan' => $bahan['nama_bahan'],
                    'harga_per_meter' => $bahan['harga_per_meter'],
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Produk berhasil diperbarui',
            'data' => $produk->load('bahan')
        ]);
    }



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
