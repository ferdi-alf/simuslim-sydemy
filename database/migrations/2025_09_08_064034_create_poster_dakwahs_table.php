<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('poster_dakwahs', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('poster'); // simpan nama file / path gambar
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poster_dakwahs');
    }
};
