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
        Schema::table('jadwal_kajians', function (Blueprint $table) {
            $table->integer('position')->default(0)->after('link');
            $table->index(['kajian_id', 'position']);
        });

        DB::statement("
            UPDATE jadwal_kajians 
            SET position = (
                SELECT COUNT(*) 
                FROM (SELECT id FROM jadwal_kajians jk2 WHERE jk2.kajian_id = jadwal_kajians.kajian_id AND jk2.id <= jadwal_kajians.id) as temp
            )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_kajians', function (Blueprint $table) {
            $table->dropIndex(['kajian_id', 'position']);
            $table->dropColumn('position');
        });
    }
};