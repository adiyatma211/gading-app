<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\HakAksesRoleController;
use App\Http\Controllers\Pengaturan\RolesController;


Route::get('/', function () {
    return view('auth.login');
});
Route::get('/dashboard',[PagesController::class,'dash'])->name('dash');
Route::get('/transaksi',[PagesController::class,'transaksi'])->name('transaksi');
Route::get('/transaksiTaabel',[PagesController::class,'transaksiTaabel'])->name('transaki.tabel');
Route::get('/aksesRole',[PagesController::class,'aksesRole'])->name('aksesRole');




Route::get('/produk',[PagesController::class,'produk'])->name('produk');
Route::get('/produk/create', [ProdukController::class, 'create'])->name('produk.create');
Route::post('/produk/store', [ProdukController::class, 'store'])->name('produk.store');
Route::get('/produk/{id}/edit', [ProdukController::class, 'edit'])->name('produk.edit');
Route::post('/produk/{id}/update', [ProdukController::class, 'update'])->name('produk.update');
Route::delete('/produk/{id}', [ProdukController::class, 'destroy'])->name('produk.destroy');
Route::post('/bahan/{bahanId}/update', [ProdukController::class, 'updateBahan']);
Route::delete('/bahan/{id}', [ProdukController::class, 'hapusBahan']);



Route::get('/roles',[PagesController::class,'roles'])->name('roles');
Route::post('/roles/store',[RolesController::class,'store'])->name('roles.store');
// Update role
Route::put('/roles/{id}', [RolesController::class, 'update'])->name('roles.update');
// Matikan (soft delete) role
Route::delete('/roles/{id}', [RolesController::class, 'destroy'])->name('roles.destroy');


Route::get('/aksesRole/user',[HakAksesRoleController::class,'searchUserName'])->name('user.search.name');
Route::post('/aksesRole/store-user',[HakAksesRoleController::class,'storeUser'])->name('user.insert');
Route::post('/aksesRole/store',[HakAksesRoleController::class,'updateUserRole'])->name('user.update');
Route::post('/aksesRole/update-role/{id}', [HakAksesRoleController::class, 'updateUserRole'])->name('user.update-role');
Route::delete('/aksesRole/delete-user/{id}', [HakAksesRoleController::class, 'deleteUser'])->name('user.delete');



Auth::routes();
