<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonasiController;
use App\Http\Controllers\KajianController;
use App\Http\Controllers\MasjidController;
use App\Http\Controllers\UstadzController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::middleware('web', 'guest')->group(function() {
    Route::controller(AuthenticationController::class)->prefix('')->name('login.')->group(function () {
        Route::get('/', function () {
            return view('welcome');
        })->name('index');
    
        Route::post('/', [AuthenticationController::class, 'store'])->name('store');
    });
});

Route::middleware(['auth'])->group(function() {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

     Route::controller(MasjidController::class)->prefix('masjid')->name('masjid.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });
    
     Route::controller(UstadzController::class)->prefix('ustadz')->name('ustadz.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

     Route::controller(DonasiController::class)->prefix('donasi')->name('donasi.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

     Route::controller(BannerController::class)->prefix('banner')->name('banner.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

     Route::controller(KajianController::class)->prefix('kajian')->name('kajian.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
        
        Route::post('/jadwal', 'storeJadwal')->name('store-jadwal');
        Route::put('/jadwal/{id}', 'updateJadwal')->name('update-jadwal');
        Route::delete('/jadwal/{id}', 'destroyJadwal')->name('destroy-jadwal');
        Route::get('/search-ustadz', 'searchUstadz')->name('search-ustadz');
    });
    

    Route::get('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});
