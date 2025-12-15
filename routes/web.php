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
    auth()->logout();                       // Logout user

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
    // Route::get('/produk/create', [ProdukController::class, 'create'])->name('produk.create');
    // Route::post('/produk/store', [ProdukController::class, 'store'])->name('produk.store');
    // Route::get('/produk/{id}/edit', [ProdukController::class, 'edit'])->name('produk.edit');
    // Route::post('/produk/{id}/update', [ProdukController::class, 'update'])->name('produk.update');
    // Route::delete('/produk/{id}', [ProdukController::class, 'destroy'])->name('produk.destroy');
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


// Route::get('/dashboard',[PagesController::class,'dash'])->name('dash');
// Route::get('/transaksiTaabel',[PagesController::class,'transaksiTaabel'])->name('transaki.tabel');
// Route::get('/aksesRole',[PagesController::class,'aksesRole'])->name('aksesRole');


// Route::get('/transaksi',[PagesController::class,'transaksi'])->name('transaksi');
// Route::get('/transaksi/cetak/{id}', [PagesController::class, 'cetakNota'])->name('transaksi.cetak');
// Route::get('/transaksi/detail/{id}',[TransactionsController::class,'detailTransaksi'])->name('transaksi.detail');
// // Route::get('/transaksi/nota',[PagesController::class,'cetakNota'])->name('transaksi.nota');
// Route::post('/transaksi/store',[TransactionsController::class,'store'])->name('transaksi.store');
// Route::post('/transaksi/updateTransaksi',[TransactionsController::class,'updateTransaksi'])->name('transaksi.updateTransaksi');





// Route::get('/produk',[PagesController::class,'produk'])->name('produk');
// Route::get('/produk/create', [ProdukController::class, 'create'])->name('produk.create');
// Route::post('/produk/store', [ProdukController::class, 'store'])->name('produk.store');
// Route::get('/produk/{id}/edit', [ProdukController::class, 'edit'])->name('produk.edit');
// Route::post('/produk/{id}/update', [ProdukController::class, 'update'])->name('produk.update');
// Route::delete('/produk/{id}', [ProdukController::class, 'destroy'])->name('produk.destroy');
// Route::post('/bahan/{bahanId}/update', [ProdukController::class, 'updateBahan']);
// Route::delete('/bahan/{id}', [ProdukController::class, 'hapusBahan']);



// Route::get('/roles',[PagesController::class,'roles'])->name('roles');
// Route::post('/roles/store',[RolesController::class,'store'])->name('roles.store');
// // Update role
// Route::put('/roles/{id}', [RolesController::class, 'update'])->name('roles.update');
// // Matikan (soft delete) role
// Route::delete('/roles/{id}', [RolesController::class, 'destroy'])->name('roles.destroy');


// Route::get('/aksesRole/user',[HakAksesRoleController::class,'searchUserName'])->name('user.search.name');
// Route::post('/aksesRole/store-user',[HakAksesRoleController::class,'storeUser'])->name('user.insert');
// Route::post('/aksesRole/store',[HakAksesRoleController::class,'updateUserRole'])->name('user.update');
// Route::post('/aksesRole/update-role/{id}', [HakAksesRoleController::class, 'updateUserRole'])->name('user.update-role');
// Route::delete('/aksesRole/delete-user/{id}', [HakAksesRoleController::class, 'deleteUser'])->name('user.delete');



Auth::routes();
