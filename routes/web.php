<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PagesController;

Route::get('/', function () {
    return view('auth.login');
});
Route::get('/dashboard',[PagesController::class,'dash'])->name('dash');


Auth::routes();