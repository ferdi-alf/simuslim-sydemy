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
       Schema::create('jadwal_kajian_ustadz', function (Blueprint $table) {
    $table->id();
    $table->foreignId('jadwal_kajian_id')
        ->nullable()
        ->constrained('jadwal_kajians')
        ->nullOnDelete();
    $table->foreignId('ustadz_id')
        ->nullable()
        ->constrained('ustadzs')
        ->nullOnDelete();
    $table->timestamps();

    $table->unique(['jadwal_kajian_id', 'ustadz_id']); // optional, biar gak ada duplikasi
});


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
