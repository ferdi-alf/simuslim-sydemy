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
        Schema::create('kajian_posters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('masjid_id')->nullable()->constrained()->nullOnDelete(); 
            $table->string('judul');
            $table->string('kategori'); 
            $table->enum('jenis', ['rutin', 'akbar/dauroh']);
            $table->string('poster');
            $table->string('penyelenggara'); 
            $table->string('alamat_manual')->nullable(); // jika tidak di masjid
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kajian_posters');
    }
};
