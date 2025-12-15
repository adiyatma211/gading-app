<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalReviewController extends Controller
{
    public function pending()
    {
        $list = AttendanceApproval::with(['attendance.user', 'requester'])
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->get();
        return response()->json($list);
    }

    public function approve(Request $request, int $id)
    {
        $data = $request->validate(['note' => 'nullable|string|max:1000']);
        $approval = AttendanceApproval::with('attendance')->findOrFail($id);

        $approval->update([
            'status' => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'review_note' => $data['note'] ?? null,
        ]);

        $approval->attendance->update([
            'approval_status' => 'approved',
            'flagged_mangkir' => false,
        ]);

        return response()->json(['message' => 'Approval disetujui.']);
    }

    public function reject(Request $request, int $id)
    {
        $data = $request->validate(['note' => 'nullable|string|max:1000']);
        $approval = AttendanceApproval::with('attendance')->findOrFail($id);

        $approval->update([
            'status' => 'rejected',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'review_note' => $data['note'] ?? null,
        ]);

        $approval->attendance->update([
            'approval_status' => 'rejected',
            'flagged_mangkir' => true,
        ]);

        return response()->json(['message' => 'Approval ditolak.']);
    }
}

