<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index()
    {
        return response()->json(Store::orderBy('name')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius_meters' => 'required|integer|min:10',
        ]);

        $store = Store::create($data);
        return response()->json(['message' => 'Toko dibuat.', 'data' => $store]);
    }

    public function update(Request $request, Store $store)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'latitude' => 'sometimes|numeric',
            'longitude' => 'sometimes|numeric',
            'radius_meters' => 'sometimes|integer|min:10',
            'active' => 'sometimes|boolean',
        ]);

        $store->update($data);
        return response()->json(['message' => 'Toko diperbarui.', 'data' => $store]);
    }
}

