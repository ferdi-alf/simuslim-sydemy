<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kajian_posters', function (Blueprint $table) {
        $table->integer('position')->default(0)->after('alamat_manual');
        $table->index('position'); // cukup pakai position saja
        });

        // kalau kamu mau kasih urutan awal global (berdasarkan id)
        DB::statement("
            UPDATE kajian_posters 
            SET position = (
                SELECT COUNT(*) 
                FROM (
                    SELECT id 
                    FROM kajian_posters kp2 
                    WHERE kp2.id <= kajian_posters.id
                ) as temp
            )
        ");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kajian_posters', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
};
