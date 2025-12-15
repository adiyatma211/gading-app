<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function index()
    {
        return response()->json(Holiday::orderBy('date')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date|unique:holidays,date',
            'name' => 'required|string|max:255',
            'is_national' => 'sometimes|boolean',
        ]);

        $holiday = Holiday::create($data);
        return response()->json(['message' => 'Hari libur ditambahkan.', 'data' => $holiday]);
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();
        return response()->json(['message' => 'Hari libur dihapus.']);
    }
}

