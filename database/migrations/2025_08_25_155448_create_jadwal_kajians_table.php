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
        Schema::create('jadwal_kajians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kajian_id')->constrained('kajian_posters')->onDelete('cascade');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->date('tanggal');
            $table->string('hari');
            $table->enum('status', ['belum dimulai', 'berjalan', 'selesai', 'liburkan']);
            $table->enum('diperuntukan', ['semua kaum muslim', 'ikhwan', 'akhwat']);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_kajians');
    }
};
