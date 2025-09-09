<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Ubah ENUM status jadi versi terbaru dengan tambahan 'Diliburkan'
        DB::statement("
            ALTER TABLE jadwal_kajians 
            MODIFY status ENUM('belum dimulai', 'Sedang berjalan', 'selesai', 'Diliburkan') 
            NOT NULL DEFAULT 'belum dimulai'
        ");
    }

    public function down()
    {
        // Kembalikan ke versi enum lama tanpa 'Diliburkan'
        DB::statement("
            ALTER TABLE jadwal_kajians 
            MODIFY status ENUM('belum dimulai', 'Sedang berjalan', 'selesai') 
            NOT NULL DEFAULT 'belum dimulai'
        ");
    }
};
