<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: update semua status yang nggak sesuai ENUM baru
        DB::table('jadwal_kajians')
            ->whereNotIn('status', ['belum dimulai', 'Sedang berjalan', 'selesai', 'Diliburkan'])
            ->update(['status' => 'belum dimulai']); // ganti sesuai kebutuhan

        // Step 2: modify kolom ENUM
        Schema::table('jadwal_kajians', function (Blueprint $table) {
            $table->enum('status', ['belum dimulai', 'Sedang berjalan', 'selesai', 'Diliburkan'])
                ->default('belum dimulai')
                ->change();
        });
    }

    public function down(): void
    {
        // Jika rollback, bisa kembalikan ke ENUM lama
        Schema::table('jadwal_kajians', function (Blueprint $table) {
            $table->enum('status', ['belum dimulai', 'Sedang berjalan', 'selesai'])
                ->default('belum dimulai')
                ->change();
        });
    }
};
