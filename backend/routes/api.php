<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdeguatiAssettiAuthController;
use App\Http\Controllers\Api\AdeguatiAssettiStandaloneController;

/*
|--------------------------------------------------------------------------
| API Routes - Adeguati Assetti
|--------------------------------------------------------------------------
*/

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AdeguatiAssettiAuthController::class, 'register']);
    Route::post('/login', [AdeguatiAssettiAuthController::class, 'login']);
    Route::post('/logout', [AdeguatiAssettiAuthController::class, 'logout']);
    Route::get('/me', [AdeguatiAssettiAuthController::class, 'me']);
    Route::post('/forgot-password', [AdeguatiAssettiAuthController::class, 'forgotPassword']);
});

// Protected API routes
Route::prefix('api')->group(function () {
    Route::get('/dashboard', [AdeguatiAssettiStandaloneController::class, 'dashboard']);
    Route::post('/dati-economici', [AdeguatiAssettiStandaloneController::class, 'salvaDatiEconomici']);
    Route::post('/calcola', [AdeguatiAssettiStandaloneController::class, 'calcolaKpi']);
    Route::get('/alert', [AdeguatiAssettiStandaloneController::class, 'listaAlert']);
    Route::get('/export', [AdeguatiAssettiStandaloneController::class, 'exportDati']);
});
