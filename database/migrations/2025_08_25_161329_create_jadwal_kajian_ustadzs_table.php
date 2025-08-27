<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Drop foreign key constraint dari kajian_rekamans table
        Schema::table('kajian_rekamans', function (Blueprint $table) {
            $table->dropForeign(['ustadz_id']);
            $table->dropColumn('ustadz_id');
        });

        // Buat table pivot untuk kajian_rekaman dan ustadz
        Schema::create('kajian_rekaman_ustadz', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kajian_rekaman_id')->constrained('kajian_rekamans')->onDelete('cascade');
            $table->foreignId('ustadz_id')->constrained('ustadzs')->onDelete('cascade');
            $table->timestamps();
            
            // Mencegah duplikasi relasi yang sama
            $table->unique(['kajian_rekaman_id', 'ustadz_id']);
        });
    }

    public function down()
    {
        // Drop table pivot
        Schema::dropIfExists('kajian_rekaman_ustadz');

        // Tambahkan kembali kolom ustadz_id ke kajian_rekamans
        Schema::table('kajian_rekamans', function (Blueprint $table) {
            $table->foreignId('ustadz_id')->nullable()->constrained('ustadzs')->onDelete('set null');
        });
    }
};
