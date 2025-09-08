<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;
use App\Models\JadwalKajian;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('debug:kajian', function () {
    $now = Carbon::now();
    $this->info("Current time: " . $now->format('Y-m-d H:i:s'));
    
    $jadwals = JadwalKajian::whereDate('tanggal', $now->toDateString())
        ->get();
    
    $this->info("Found " . $jadwals->count() . " kajian for today");
    
    foreach ($jadwals as $jadwal) {
        $jadwalTime = Carbon::parse($jadwal->tanggal . ' ' . $jadwal->jam_mulai);
        $diffMinutes = $now->diffInMinutes($jadwalTime, false);
        
        $this->info("ID: {$jadwal->id}, Time: {$jadwalTime->format('H:i')}, Status: {$jadwal->status}, Diff: {$diffMinutes} minutes");
        
        if ($diffMinutes <= 15 && $diffMinutes >= 0) {
            $this->info("  -> Should update to 'berjalan'");
        }
    }
})->purpose('Debug kajian schedule');

Schedule::command('app:update-kajian-status')->everyMinute();

Schedule::call(function () {
    Log::info("Scheduler test: " . now());
})->everyMinute();