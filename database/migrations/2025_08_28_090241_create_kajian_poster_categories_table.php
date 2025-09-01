<?php
// database/migrations/xxxx_xx_xx_create_kajian_poster_categories_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kajian_poster_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kajian_poster_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Pastikan tidak ada duplikasi relasi
            $table->unique(['kajian_poster_id', 'category_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('kajian_poster_categories');
    }
};