<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bacaan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bacaan_id')->constrained()->onDelete('cascade');
            $table->text('arab')->nullable();
            $table->text('latin')->nullable();
            $table->text('terjemahan')->nullable();
            $table->string('sumber')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bacaan_details');
    }
};