<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kajian_rekamans', function (Blueprint $table) {

            
            // Tambah foreign key baru ke jadwal_kajians
            $table->foreignId('jadwal_kajian_id')->nullable()->constrained('jadwal_kajians')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kajian_rekamans', function (Blueprint $table) {
            // Hapus foreign key baru
            $table->dropForeign(['jadwal_kajian_id']);
            $table->dropColumn('jadwal_kajian_id');
            
        });
    }
};