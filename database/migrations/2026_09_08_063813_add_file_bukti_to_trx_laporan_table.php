<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trx_laporan_progres', function (Blueprint $table) {
            // Hapus ->after('link_bukti') karena kolom tersebut tidak ada
            $table->string('file_bukti', 255)->nullable(); 
        });
    }

    public function down(): void
    {
        Schema::table('trx_laporan_progres', function (Blueprint $table) {
            $table->dropColumn('file_bukti');
        });
    }
};