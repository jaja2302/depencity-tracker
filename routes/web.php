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
    Route::get('/getBlok', [TrackerController::class, 'getBlok'])->name('getBlok');
    Route::get('/drawMaps', [TrackerController::class, 'drawMaps'])->name('drawMaps');
    Route::post('/updateUserqc', [TrackerController::class, 'updateUserqc'])->name('updateUserqc');
    Route::get('/getData', [TrackerController::class, 'getData'])->name('getData');
});
