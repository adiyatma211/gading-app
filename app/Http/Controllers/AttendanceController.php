<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceApproval;
use App\Models\AttendanceSetting;
use App\Models\Holiday;
use App\Models\Store;
use App\Services\GeoService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function myAttendances(Request $request)
    {
        $user = Auth::user();
        $att = Attendance::with('store', 'approval')
            ->where('user_id', $user->id)
            ->orderByDesc('work_date')
            ->limit(60)
            ->get();
        return response()->json($att);
    }

    public function checkIn(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $today = Carbon::today();

        // Holiday check
        if (Holiday::whereDate('date', $today)->exists()) {
            return response()->json(['message' => 'Hari ini adalah hari libur.'], 422);
        }

        // Settings and store
        /** @var Store $store */
        $store = Store::with('settings')->findOrFail($data['store_id']);
        $settings = $store->settings;
        if (!$settings) {
            return response()->json(['message' => 'Pengaturan absensi belum diset untuk toko.'], 422);
        }

        // Geofence check
        $distance = GeoService::distanceMeters((float)$data['lat'], (float)$data['lng'], (float)$store->latitude, (float)$store->longitude);
        if ($distance > (int)$store->radius_meters) {
            return response()->json(['message' => 'Di luar area toko ('.(int)$distance.' m).'], 422);
        }

        // Prevent double check-in same date
        $existing = Attendance::where('user_id', $user->id)->whereDate('work_date', $today)->first();
        if ($existing && $existing->check_in_at) {
            return response()->json(['message' => 'Sudah absen masuk hari ini.'], 422);
        }

        $now = Carbon::now();
        $onTimeUntil = Carbon::createFromTimeString($settings->check_in_on_time_until);
        $lastAllowed = Carbon::createFromTimeString($settings->check_in_last_allowed);

        if ($now->gt($lastAllowed)) {
            return response()->json(['message' => 'Batas absen masuk sudah lewat.'], 422);
        }

        $isLate = $now->gt($onTimeUntil);

        $attendance = Attendance::updateOrCreate(
            ['user_id' => $user->id, 'work_date' => $today],
            [
                'store_id' => $store->id,
                'check_in_at' => $now,
                'check_in_lat' => $data['lat'],
                'check_in_lng' => $data['lng'],
                'is_late' => $isLate,
                'flagged_mangkir' => $isLate, // flagged until approved
                'approval_status' => $isLate ? 'pending' : null,
            ]
        );

        return response()->json([
            'message' => $isLate ? 'Check-in terlambat, perlu approval.' : 'Check-in berhasil.',
            'data' => $attendance,
        ]);
    }

    public function checkOut(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $today = Carbon::today();

        /** @var Store $store */
        $store = Store::with('settings')->findOrFail($data['store_id']);
        $settings = $store->settings;
        if (!$settings) {
            return response()->json(['message' => 'Pengaturan absensi belum diset untuk toko.'], 422);
        }

        $attendance = Attendance::where('user_id', $user->id)->whereDate('work_date', $today)->first();
        if (!$attendance || !$attendance->check_in_at) {
            return response()->json(['message' => 'Belum ada absen masuk hari ini.'], 422);
        }
        if ($attendance->check_out_at) {
            return response()->json(['message' => 'Sudah absen keluar.'], 422);
        }

        // Geofence check again
        $distance = GeoService::distanceMeters((float)$data['lat'], (float)$data['lng'], (float)$store->latitude, (float)$store->longitude);
        if ($distance > (int)$store->radius_meters) {
            return response()->json(['message' => 'Di luar area toko ('.(int)$distance.' m).'], 422);
        }

        $now = Carbon::now();
        $earliest = Carbon::createFromTimeString($settings->check_out_earliest);
        $latest = Carbon::createFromTimeString($settings->check_out_latest);

        if ($now->lt($earliest)) {
            return response()->json(['message' => 'Belum masuk waktu absen keluar.'], 422);
        }
        if ($now->gt($latest)) {
            return response()->json(['message' => 'Lewat dari batas waktu absen keluar.'], 422);
        }

        $attendance->update([
            'check_out_at' => $now,
            'check_out_lat' => $data['lat'],
            'check_out_lng' => $data['lng'],
        ]);

        return response()->json(['message' => 'Check-out berhasil.', 'data' => $attendance]);
    }

    public function requestApproval(Request $request, int $attendanceId)
    {
        $user = Auth::user();
        $data = $request->validate([
            'reason' => 'nullable|string|max:1000',
        ]);

        $attendance = Attendance::where('id', $attendanceId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if (!$attendance->is_late) {
            return response()->json(['message' => 'Presensi tidak terlambat, tidak perlu approval.'], 422);
        }

        $approval = AttendanceApproval::updateOrCreate(
            ['attendance_id' => $attendance->id],
            [
                'user_id' => $user->id,
                'reason' => $data['reason'] ?? null,
                'status' => 'pending',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'review_note' => null,
            ]
        );

        $attendance->update([
            'approval_status' => 'pending',
            'flagged_mangkir' => true,
        ]);

        return response()->json(['message' => 'Permintaan approval dikirim.', 'data' => $approval]);
    }
}

