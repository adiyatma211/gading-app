<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\Pengaturan\RolesController;


Route::get('/', function () {
    return view('auth.login');
});
Route::get('/dashboard',[PagesController::class,'dash'])->name('dash');
Route::get('/aksesRole',[PagesController::class,'aksesRole'])->name('aksesRole');



Route::get('/roles',[PagesController::class,'roles'])->name('roles');
Route::post('/roles/store',[RolesController::class,'store'])->name('roles.store');
// Update role
Route::put('/roles/{id}', [RolesController::class, 'update'])->name('roles.update');
// Matikan (soft delete) role
Route::delete('/roles/{id}', [RolesController::class, 'destroy'])->name('roles.destroy');

Auth::routes();
