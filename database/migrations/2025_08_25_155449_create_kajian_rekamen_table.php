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
       Schema::create('kajian_rekamans', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('kitab')->nullable();
            $table->foreignId('ustadz_id')->nullable()->constrained('ustadzs')->onDelete('set null');
            $table->enum('kategori', ['video', 'audio']);
            $table->string('link'); 
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kajian_rekamen');
    }
};
