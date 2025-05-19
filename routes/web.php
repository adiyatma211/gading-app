<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\HakAksesRoleController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\Pengaturan\RolesController;

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

    Route::get('/produk', [PagesController::class, 'produk'])->name('produk');
    Route::get('/produk/create', [ProdukController::class, 'create'])->name('produk.create');
    Route::post('/produk/store', [ProdukController::class, 'store'])->name('produk.store');
    Route::get('/produk/{id}/edit', [ProdukController::class, 'edit'])->name('produk.edit');
    Route::post('/produk/{id}/update', [ProdukController::class, 'update'])->name('produk.update');
    Route::delete('/produk/{id}', [ProdukController::class, 'destroy'])->name('produk.destroy');
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
});

// Route khusus untuk Kasir
Route::middleware(['auth', 'check.role:kasir,Owner,superadmin,SuperAdmin,Super Admin'])->group(function () {
    Route::get('/transaksi', [PagesController::class, 'transaksi'])->name('transaksi');
    Route::get('/transaksi/cetak/{id}', [PagesController::class, 'cetakNota'])->name('transaksi.cetak');
    Route::get('/transaksi/detail/{id}', [TransactionsController::class, 'detailTransaksi'])->name('transaksi.detail');
    Route::post('/transaksi/store', [TransactionsController::class, 'store'])->name('transaksi.store');
    Route::post('/transaksi/updateTransaksi', [TransactionsController::class, 'updateTransaksi'])->name('transaksi.updateTransaksi');
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
