<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bacaans', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->enum('type', ['doa', 'hadits', 'dzikir'])->default('doa');
            $table->text('deskripsi')->nullable(); // Opsional, untuk keterangan tambahan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bacaans');
    }
};