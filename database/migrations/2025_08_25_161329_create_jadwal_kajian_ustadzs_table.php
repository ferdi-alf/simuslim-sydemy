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
            $table->foreignId('jadwal_kajian_id')->constrained('jadwal_kajians')->onDelete('cascade');
            $table->foreignId('ustadz_id')->constrained('ustadzs')->onDelete('cascade');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_kajian_ustadzs');
    }
};
