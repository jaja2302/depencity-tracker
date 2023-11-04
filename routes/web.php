<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\TrackerController;
use App\Http\Controllers\ValidateController;

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
    Route::get('/validateMaps', [ValidateController::class, 'mainMaps'])->name('mainMaps');
    Route::get('/getOptValidateEst/{id}', [ValidateController::class, 'getOptValidateEst'])->name('getOptValidateEst');
    Route::get('/getOptValidateAfd/{id}', [ValidateController::class, 'getOptValidateAfd'])->name('getOptValidateAfd');
    Route::get('/getCoordinatesValidate/{est}', [ValidateController::class, 'getCoordinatesValidate'])->name('getCoordinatesValidate');
    Route::post('/processValidate', [ValidateController::class, 'processValidate'])->name('processValidate');

    Route::get('/sinkronMaps', [ValidateController::class, 'sinkronMaps'])->name('sinkronMaps');
    Route::post('/processSynchronize', [ValidateController::class, 'processSynchronize'])->name('processSynchronize');
});
