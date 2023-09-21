<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\TrackerController;


Route::get('/', [LoginController::class, 'index'])->name('index');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/getauthuserstxt', [LoginController::class, 'login'])->name('getauthuserstxt');
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [TrackerController::class, 'dashboard'])->name('dashboard');
    Route::get('/info', [TrackerController::class, 'info'])->name('info');
});
