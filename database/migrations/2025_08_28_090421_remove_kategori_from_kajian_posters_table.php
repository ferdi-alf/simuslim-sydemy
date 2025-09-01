<?php
// Jalankan command: php artisan make:migration remove_kategori_from_kajian_posters_table --table=kajian_posters

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('kajian_posters', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
    }

    public function down()
    {
        Schema::table('kajian_posters', function (Blueprint $table) {
            $table->string('kategori')->after('judul');
        });
    }
};