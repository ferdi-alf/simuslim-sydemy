<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\DonasiController;
use App\Http\Controllers\MasjidController;
use App\Http\Controllers\UstadzController;
use App\Http\Controllers\KajianController;
use App\Http\Controllers\KajianRekamanController;
use App\Http\Controllers\PosterDakwahController;
use App\Http\Controllers\SymuslimController;

Route::prefix('v1/mobile/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    });
});

Route::prefix('v1/mobile')->middleware('mobile.api.security')->group(function () {
    
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

    // Poster Dakwah endpoints
    Route::get('/posters', [PosterDakwahController::class, 'getAllPosters']);
    
    // Bacaan (Symuslim) endpoints
    Route::get('/bacaan', [SymuslimController::class, 'getAllBacaan']);
    Route::get('/bacaan-details', [SymuslimController::class, 'getAllBacaanDetails']);
});


Route::get('/v1/test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'API is working',
        'data' => [
            'app_status' => env('APP_STATUS', 'NOT_OPEN'),
            'timestamp' => now()
        ]
    ]);
});