<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSetting;
use App\Models\Store;
use Illuminate\Http\Request;

class AttendanceSettingController extends Controller
{
    public function show(Store $store)
    {
        return response()->json($store->settings);
    }

    public function upsert(Request $request, Store $store)
    {
        $data = $request->validate([
            'check_in_on_time_until' => 'required|date_format:H:i',
            'check_in_last_allowed' => 'required|date_format:H:i|after_or_equal:check_in_on_time_until',
            'check_out_earliest' => 'required|date_format:H:i',
            'check_out_latest' => 'required|date_format:H:i|after_or_equal:check_out_earliest',
            'enable_weekends' => 'sometimes|boolean',
        ]);

        // Normalize to H:i:s
        foreach (['check_in_on_time_until', 'check_in_last_allowed', 'check_out_earliest', 'check_out_latest'] as $k) {
            if (isset($data[$k])) {
                $data[$k] = $data[$k] . ':00';
            }
        }

        $settings = AttendanceSetting::updateOrCreate(
            ['store_id' => $store->id],
            $data
        );

        return response()->json(['message' => 'Pengaturan absensi disimpan.', 'data' => $settings]);
    }
}

