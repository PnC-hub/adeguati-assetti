<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdeguatiAssettiAuthController;
use App\Http\Controllers\Api\AdeguatiAssettiStandaloneController;

/*
|--------------------------------------------------------------------------
| API Routes - Adeguati Assetti
|--------------------------------------------------------------------------
*/

// Authentication routes - will be accessible at /api/auth/*
Route::prefix('auth')->group(function () {
    Route::post('/register', [AdeguatiAssettiAuthController::class, 'register']);
    Route::post('/login', [AdeguatiAssettiAuthController::class, 'login']);
    Route::post('/logout', [AdeguatiAssettiAuthController::class, 'logout']);
    Route::get('/me', [AdeguatiAssettiAuthController::class, 'me']);
    Route::post('/forgot-password', [AdeguatiAssettiAuthController::class, 'forgotPassword']);
});

// API routes - will be accessible at /api/*
Route::get('/dashboard', [AdeguatiAssettiStandaloneController::class, 'dashboard']);
Route::get('/dati-economici/{anno}/{mese}', [AdeguatiAssettiStandaloneController::class, 'getDatiEconomici']);
Route::post('/dati-economici', [AdeguatiAssettiStandaloneController::class, 'salvaDatiEconomici']);
Route::post('/calcola', [AdeguatiAssettiStandaloneController::class, 'calcolaKpi']);
Route::get('/alert', [AdeguatiAssettiStandaloneController::class, 'listaAlert']);
Route::get('/export', [AdeguatiAssettiStandaloneController::class, 'exportDati']);
