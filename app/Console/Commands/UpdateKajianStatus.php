<?php

namespace App\Console\Commands;

use App\Models\JadwalKajian;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateKajianStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-kajian-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update kajian status based on schedule';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        Log::info("Scheduler running at: " . $now->format('Y-m-d H:i:s'));

        // Cari kajian yang akan segera dimulai (0–15 menit sebelum jam mulai)
        $berjalan = JadwalKajian::whereDate('tanggal', $now->toDateString())
            ->where(function ($query) use ($now) {
                $query->whereRaw("TIMESTAMPDIFF(MINUTE, ?, CONCAT(tanggal, ' ', jam_mulai)) <= 15", [$now])
                      ->whereRaw("TIMESTAMPDIFF(MINUTE, ?, CONCAT(tanggal, ' ', jam_mulai)) >= 0", [$now]);
            })
            ->where('status', '!=', JadwalKajian::STATUS_BERJALAN)
            ->where('status', '!=', JadwalKajian::STATUS_SELESAI)
            ->get();

        Log::info("Found " . $berjalan->count() . " kajian to update to '" . JadwalKajian::STATUS_BERJALAN . "'");

        foreach ($berjalan as $jadwal) {
            $jadwalTime = Carbon::parse($jadwal->tanggal . ' ' . $jadwal->jam_mulai);
            $diffMinutes = $now->diffInMinutes($jadwalTime, false);

            Log::info("Jadwal ID {$jadwal->id}: {$jadwalTime->format('Y-m-d H:i:s')}, diff: {$diffMinutes} minutes");

            if ($diffMinutes <= 15 && $diffMinutes >= 0) {
                $jadwal->update(['status' => JadwalKajian::STATUS_BERJALAN]);
                Log::info("Updated kajian ID {$jadwal->id} to '" . JadwalKajian::STATUS_BERJALAN . "'");
            }
        }

        // Update status kajian yang sudah selesai
        $selesai = JadwalKajian::whereDate('tanggal', $now->toDateString())
            ->whereRaw("CONCAT(tanggal, ' ', jam_selesai) <= ?", [$now])
            ->where('status', '!=', JadwalKajian::STATUS_SELESAI)
            ->update(['status' => JadwalKajian::STATUS_SELESAI]);

        if ($selesai > 0) {
            Log::info("[$now] $selesai kajian diupdate menjadi '" . JadwalKajian::STATUS_SELESAI . "'");
        }

        $this->info("Scheduler dijalankan pada $now");
        $this->info("Updated {$berjalan->count()} kajian to '" . JadwalKajian::STATUS_BERJALAN . "' and $selesai kajian to '" . JadwalKajian::STATUS_SELESAI . "'");
    }
}
