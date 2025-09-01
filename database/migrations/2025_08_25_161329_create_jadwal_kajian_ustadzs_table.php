<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('kajian_rekamans', function (Blueprint $table) {
            if (Schema::hasColumn('kajian_rekamans', 'ustadz_id')) {
                // Cari dulu nama foreign key constraint
                $foreignKeys = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_NAME = 'kajian_rekamans' 
                      AND COLUMN_NAME = 'ustadz_id' 
                      AND CONSTRAINT_SCHEMA = DATABASE()
                ");

                if (!empty($foreignKeys)) {
                    $fkName = $foreignKeys[0]->CONSTRAINT_NAME;
                    DB::statement("ALTER TABLE kajian_rekamans DROP FOREIGN KEY $fkName");
                }

                // Drop kolom ustadz_id
                $table->dropColumn('ustadz_id');
            }
        });

        Schema::create('kajian_rekaman_ustadz', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kajian_rekaman_id')->constrained('kajian_rekamans')->onDelete('cascade');
            $table->foreignId('ustadz_id')->constrained('ustadzs')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['kajian_rekaman_id', 'ustadz_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('kajian_rekaman_ustadz');

        Schema::table('kajian_rekamans', function (Blueprint $table) {
            if (!Schema::hasColumn('kajian_rekamans', 'ustadz_id')) {
                $table->foreignId('ustadz_id')->nullable()->constrained('ustadzs')->onDelete('set null');
            }
        });
    }
};
