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
        Schema::table('kajian_rekamans', function (Blueprint $table) {
            $table->dropForeign(['ustadz_id']);
            $table->dropColumn('ustadz_id');
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
            $table->foreignId('ustadz_id')->nullable()->constrained('ustadzs')->onDelete('set null');
        });
    }
};
