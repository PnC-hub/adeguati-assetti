<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdeguatiAssettiAuthController;
use App\Http\Controllers\Api\AdeguatiAssettiStandaloneController;
use App\Http\Controllers\Api\StudioController;
use App\Http\Controllers\Api\AziendeClienteController;
use App\Http\Controllers\Api\InvitiController;

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

// Plan & Account routes
Route::get('/piani', [AdeguatiAssettiStandaloneController::class, 'listaPiani']);
Route::get('/account', [AdeguatiAssettiStandaloneController::class, 'account']);
Route::post('/upgrade', [AdeguatiAssettiStandaloneController::class, 'upgrade']);
Route::post('/billing-portal', [AdeguatiAssettiStandaloneController::class, 'billingPortal']);
Route::post('/webhook/stripe', [AdeguatiAssettiStandaloneController::class, 'stripeWebhook']);

// API routes - Imprenditore (will be accessible at /api/*)
Route::get('/dashboard', [AdeguatiAssettiStandaloneController::class, 'dashboard']);
Route::get('/dati-economici/{anno}/{mese}', [AdeguatiAssettiStandaloneController::class, 'getDatiEconomici']);
Route::post('/dati-economici', [AdeguatiAssettiStandaloneController::class, 'salvaDatiEconomici']);
Route::post('/calcola', [AdeguatiAssettiStandaloneController::class, 'calcolaKpi']);
Route::get('/alert', [AdeguatiAssettiStandaloneController::class, 'listaAlert']);
Route::get('/export', [AdeguatiAssettiStandaloneController::class, 'exportDati']);
Route::get('/aziende', [AdeguatiAssettiStandaloneController::class, 'listaAziende']);
Route::put('/aziende/{id}', [AdeguatiAssettiStandaloneController::class, 'aggiornaAzienda']);

// Ticket segnalazioni
Route::post('/ticket', [AdeguatiAssettiStandaloneController::class, 'submitTicket']);

/*
|--------------------------------------------------------------------------
| API Routes - Consulente (Studio)
|--------------------------------------------------------------------------
*/

// Studio routes (consulente only)
Route::prefix('studio')->group(function () {
    Route::get('/', [StudioController::class, 'show']);
    Route::put('/', [StudioController::class, 'update']);
    Route::get('/stats', [StudioController::class, 'stats']);
    Route::post('/logo', [StudioController::class, 'uploadLogo']);
    Route::post('/regenerate-api-key', [StudioController::class, 'regenerateApiKey']);
    Route::delete('/delete-all-data', [StudioController::class, 'deleteAllData']);
});

// Aziende Cliente routes (consulente manages client companies)
Route::prefix('aziende-cliente')->group(function () {
    Route::get('/', [AziendeClienteController::class, 'index']);
    Route::post('/', [AziendeClienteController::class, 'store']);
    Route::get('/dashboard-aggregata', [AziendeClienteController::class, 'dashboardAggregata']);
    Route::get('/{id}', [AziendeClienteController::class, 'show']);
    Route::put('/{id}', [AziendeClienteController::class, 'update']);
    Route::delete('/{id}', [AziendeClienteController::class, 'destroy']);
    Route::get('/{id}/dati', [AziendeClienteController::class, 'getDati']);
    Route::post('/{id}/dati', [AziendeClienteController::class, 'storeDati']);
    Route::get('/{id}/kpi', [AziendeClienteController::class, 'getKpi']);
});

// Inviti Cliente routes (consulente invites clients for readonly access)
Route::prefix('inviti')->group(function () {
    Route::get('/', [InvitiController::class, 'index']);
    Route::post('/{aziendaId}', [InvitiController::class, 'store']);
    Route::delete('/{id}', [InvitiController::class, 'destroy']);
});

// Public invite routes (no auth required)
Route::get('/invite/{token}', [InvitiController::class, 'info']);
Route::post('/invite/{token}/accept', [InvitiController::class, 'accept']);
