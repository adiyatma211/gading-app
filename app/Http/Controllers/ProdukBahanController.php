<?php

namespace App\Http\Controllers;

use App\Models\produk_bahan;
use App\Http\Requests\Storeproduk_bahanRequest;
use App\Http\Requests\Updateproduk_bahanRequest;

class ProdukBahanController extends Controller
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
    public function store(Storeproduk_bahanRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(produk_bahan $produk_bahan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(produk_bahan $produk_bahan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Updateproduk_bahanRequest $request, produk_bahan $produk_bahan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(produk_bahan $produk_bahan)
    {
        //
    }
}
