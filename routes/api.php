<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\DonasiController;
use App\Http\Controllers\MasjidController;
use App\Http\Controllers\UstadzController;
use App\Http\Controllers\KajianController;
use App\Http\Controllers\KajianRekamanController;
use App\Http\Controllers\SymuslimController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API Version 1 - Mobile App
Route::prefix('v1/mobile')->group(function () {
    
    // Banner endpoints
    Route::get('/banners', [BannerController::class, 'getAllBanners']);
    
    // Donasi endpoints
    Route::get('/donasi', [DonasiController::class, 'getAllDonasi']);
    
    // Masjid endpoints
    Route::get('/masjid', [MasjidController::class, 'getAllMasjid']);
    
    // Ustadz endpoints
    Route::get('/ustadz', [UstadzController::class, 'getAllUstadz']);
    
    // Kajian endpoints
    Route::get('/kajian-posters', [KajianController::class, 'getAllKajianPosters']);
    Route::get('/jadwal-kajian', [KajianController::class, 'getAllJadwalKajian']);
    
    // Kajian Rekaman endpoints
    Route::get('/kajian-rekaman', [KajianRekamanController::class, 'getAllKajianRekaman']);
    
    // Bacaan (Symuslim) endpoints
    Route::get('/bacaan', [SymuslimController::class, 'getAllBacaan']);
    Route::get('/bacaan-details', [SymuslimController::class, 'getAllBacaanDetails']);
    
});


// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'API is running properly',
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
});