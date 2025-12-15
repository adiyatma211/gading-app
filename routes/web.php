<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ProdukNewController;
use App\Http\Controllers\HakAksesRoleController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\Pengaturan\RolesController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\AttendanceSettingController;
use App\Http\Controllers\ApprovalReviewController;

Route::get('/', function () {
    return view('auth.login');
});
Route::post('/logout', function (Request $request) {
    $request->session()->regenerateToken(); // Regenerasi token CSRF
    $request->session()->invalidate();      // Hapus semua session
    Auth::logout();                       // Logout user

    return redirect('/login')->withHeaders([
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache',
        'Expires' => '0'
    ]);
})->name('logout');
Route::get('/nota/{filename}', function ($filename) {
    $path = public_path('nota/' . $filename);

    if (!file_exists($path)) {
        abort(404, 'File not found.');
    }

    return response()->file($path);
});

// Route untuk mengakses legacy PDF dari public/nota directory
Route::get('/legacy-pdf/{filename}', function ($filename) {
    // Security check - hanya izinkan filename dengan format tertentu
    if (!preg_match('/^[a-zA-Z0-9_\-\.]+\.(pdf|PDF)$/', $filename)) {
        \Log::error('Legacy PDF Access Denied - Invalid Filename Format', [
            'filename' => $filename,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
        abort(403, 'Access denied - Invalid filename format.');
    }

    $path = public_path('nota/' . $filename);

    // Check if file exists
    if (!file_exists($path)) {
        \Log::error('Legacy PDF File Not Found', [
            'filename' => $filename,
            'path' => $path,
            'ip' => request()->ip()
        ]);
        abort(404, 'PDF file not found.');
    }

    // Check if file is actually a PDF
    $mimeType = mime_content_type($path);
    if ($mimeType !== 'application/pdf') {
        \Log::error('Legacy PDF Access Denied - Not a PDF File', [
            'filename' => $filename,
            'mime_type' => $mimeType,
            'ip' => request()->ip()
        ]);
        abort(403, 'Access denied - File is not a PDF.');
    }

    \Log::info('Legacy PDF Access Success', [
        'filename' => $filename,
        'file_size' => filesize($path),
        'ip' => request()->ip()
    ]);

    return response()->file($path, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="' . $filename . '"',
        'Cache-Control' => 'public, max-age=86400', // Cache for 1 day
    ]);
})->where('filename', '.*');

// Route untuk mengakses PDF dari storage (hanya untuk user yang terautentikasi)
Route::middleware(['auth'])->group(function () {
    Route::get('/pdf-storage/{path}', function ($path) {
        // DEBUG: Log incoming request
        \Log::info('PDF Storage Access Attempt', [
            'original_path' => $path,
            'decoded_path' => urldecode($path),
            'user_authenticated' => auth()->check(),
            'user_id' => auth()->id(),
            'timestamp' => now()
        ]);

        // Decode path yang di-encode
        $path = urldecode($path);

        // Remove trailing slash if present
        $path = rtrim($path, '/');

        // DEBUG: Log processed path
        \Log::info('PDF Storage Processed Path', [
            'processed_path' => $path,
            'regex_pattern' => '/^[0-9]{4}\/[0-9]{2}\/[0-9]{2}\/(thermal|invoice|THERMAL|INVOICE)\/[^\/]+\.pdf$/',
            'regex_match' => preg_match('/^[0-9]{4}\/[0-9]{2}\/[0-9]{2}\/(thermal|invoice|THERMAL|INVOICE)\/[^\/]+\.pdf$/', $path)
        ]);

        // Security check - hanya izinkan path dengan format tertentu
        if (!preg_match('/^[0-9]{4}\/[0-9]{2}\/[0-9]{2}\/(thermal|invoice|THERMAL|INVOICE)\/[^\/]+\.pdf$/', $path)) {
            \Log::error('PDF Storage Access Denied - Invalid Path Format', [
                'path' => $path,
                'user_id' => auth()->id()
            ]);
            abort(403, 'Access denied - Invalid path format.');
        }

        $storageService = app(\App\Services\PDFStorageService::class);

        // DEBUG: Check file existence
        $fileExists = $storageService->fileExists($path);
        \Log::info('PDF Storage File Check', [
            'path' => $path,
            'file_exists' => $fileExists,
            'storage_disk' => 'pdf_storage'
        ]);

        if (!$fileExists) {
            \Log::error('PDF Storage File Not Found', [
                'path' => $path,
                'user_id' => auth()->id()
            ]);
            abort(404, 'PDF file not found.');
        }

        $fileContent = $storageService->getPDF($path);
        if (!$fileContent) {
            \Log::error('PDF Storage File Cannot Be Read', [
                'path' => $path,
                'user_id' => auth()->id()
            ]);
            abort(404, 'PDF file not found or cannot be read.');
        }

        \Log::info('PDF Storage Access Success', [
            'path' => $path,
            'file_size' => strlen($fileContent),
            'user_id' => auth()->id()
        ]);

        return response($fileContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . basename($path) . '"');
    })->where('path', '.*');
});

Route::middleware(['auth', 'check.role:SuperAdmin,Owner,Super Admin,kasir'])->group(function () {
    Route::get('/dashboard', [PagesController::class, 'dash'])->name('dash');
    Route::get('/transaksiTaabel', [PagesController::class, 'transaksiTaabel'])->name('transaki.tabel');

});


// Semua route yang bisa diakses oleh Super Admin dan Owner
Route::middleware(['auth', 'check.role:SuperAdmin,Owner,Super Admin'])->group(function () {

    Route::get('/aksesRole', [PagesController::class, 'aksesRole'])->name('aksesRole');


    // Produk
// Route::get('/produk', [ProdukNewController::class, 'index'])->name('produk.index');
Route::post('/produk', [ProdukNewController::class, 'store'])->name('produk.store');
Route::get('/produk/{id}', [ProdukNewController::class, 'edit'])->name('produk.edit');
Route::put('/produk/{id}/update', [ProdukNewController::class, 'update'])->name('produk.update');
Route::delete('/produk/{id}', [ProdukNewController::class, 'destroy'])->name('produk.destroy');

// Harga
Route::put('/harga-produk/{id}', [ProdukNewController::class, 'updateHarga'])->name('harga.update');
Route::delete('/harga-produk/{id}', [ProdukNewController::class, 'destroyHarga'])->name('harga.delete');
    Route::get('/produk', [PagesController::class, 'produk'])->name('produk');
    Route::post('/bahan/{bahanId}/update', [ProdukController::class, 'updateBahan']);
    Route::delete('/bahan/{id}', [ProdukController::class, 'hapusBahan']);
    Route::get('/roles', [PagesController::class, 'roles'])->name('roles');
    Route::post('/roles/store', [RolesController::class, 'store'])->name('roles.store');
    Route::put('/roles/{id}', [RolesController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{id}', [RolesController::class, 'destroy'])->name('roles.destroy');
    Route::get('/aksesRole/user', [HakAksesRoleController::class, 'searchUserName'])->name('user.search.name');
    Route::post('/aksesRole/store-user', [HakAksesRoleController::class, 'storeUser'])->name('user.insert');
    Route::post('/aksesRole/store', [HakAksesRoleController::class, 'updateUserRole'])->name('user.update');
    Route::post('/aksesRole/update-role/{id}', [HakAksesRoleController::class, 'updateUserRole'])->name('user.update-role');
    Route::delete('/aksesRole/delete-user/{id}', [HakAksesRoleController::class, 'deleteUser'])->name('user.delete');
    // Report
    Route::get('/report', [PagesController::class, 'transaksiReport'])->name('report');
    Route::post('/laporan/transaksi/data', [PagesController::class, 'getDataTransaksi'])->name('laporan.transaksi.data');
    Route::get('/export-transaksi', [PagesController::class, 'exportExcel'])->name('export.transaksi');

    // Admin: Presensi (Stores, Settings, Holidays, Approvals)
    Route::get('/stores', [StoreController::class, 'index']);
    Route::post('/stores', [StoreController::class, 'store']);
    Route::put('/stores/{store}', [StoreController::class, 'update']);

    Route::get('/stores/{store}/attendance-settings', [AttendanceSettingController::class, 'show']);
    Route::post('/stores/{store}/attendance-settings', [AttendanceSettingController::class, 'upsert']);

    Route::get('/holidays', [HolidayController::class, 'index']);
    Route::post('/holidays', [HolidayController::class, 'store']);
    Route::delete('/holidays/{holiday}', [HolidayController::class, 'destroy']);

    Route::get('/attendance/approvals/pending', [ApprovalReviewController::class, 'pending']);
    Route::post('/attendance/approvals/{id}/approve', [ApprovalReviewController::class, 'approve']);
    Route::post('/attendance/approvals/{id}/reject', [ApprovalReviewController::class, 'reject']);

    // Admin Presensi UI
    Route::get('/presensi/admin', [PagesController::class, 'presensiAdmin'])->name('presensi.admin');
});

// Route khusus untuk Kasir
Route::middleware(['auth', 'check.role:kasir,Owner,superadmin,SuperAdmin,Super Admin'])->group(function () {
    Route::get('/transaksi', [PagesController::class, 'transaksi'])->name('transaksi');
    Route::get('/transaksi/cetak/{id}', [PagesController::class, 'cetakNota'])->name('transaksi.cetak');
    Route::get('/transaksi/detail/{id}', [TransactionsController::class, 'detailTransaksi'])->name('transaksi.detail');
    Route::post('/transaksi/store', [TransactionsController::class, 'store'])->name('transaksi.store');
    Route::post('/transaksi/updateTransaksi', [TransactionsController::class, 'updateTransaksi'])->name('transaksi.updateTransaksi');
    Route::post('/transaksi/print-thermal/{id}', [TransactionsController::class, 'printThermal'])->name('transaksi.printThermal');
    Route::post('/transaksi/print-thermal-test', [TransactionsController::class, 'testThermal'])->name('transaksi.printThermal.test');

    // Staff: Presensi
    Route::get('/attendance/me', [AttendanceController::class, 'myAttendances']);
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut']);
    Route::post('/attendance/{attendanceId}/request-approval', [AttendanceController::class, 'requestApproval']);

    // Staff Presensi UI
    Route::get('/presensi', [PagesController::class, 'presensiAbsen'])->name('presensi.absen');
});




Auth::routes();
