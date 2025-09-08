<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kajian_posters', function (Blueprint $table) {
            $table->string('link')->nullable()->after('poster'); 
            // after('poster') biar rapi posisinya setelah kolom poster
        });
    }

    public function down(): void
    {
        Schema::table('kajian_posters', function (Blueprint $table) {
            $table->dropColumn('link');
        });
    }
};
