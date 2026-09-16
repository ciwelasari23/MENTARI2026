<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trx_laporan_progres', function (Blueprint $table) {
            $table->id('id_laporan');
            $table->unsignedBigInteger('id_target_wilayah');
            
            // Ubah ke integer biasa agar kompatibel dengan mst_user.id_user
            $table->integer('id_user_pelapor');
            $table->integer('id_user_verifikator')->nullable();
            
            $table->integer('realisasi_saat_ini');
            $table->text('catatan_over_target')->nullable();
            $table->boolean('is_selesai')->default(0);
            $table->string('status_laporan', 20)->default('pending');
            $table->string('path_bukti_dukung', 255)->nullable();
            $table->text('catatan_verifikasi')->nullable();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('id_target_wilayah')
                  ->references('id_target_wilayah')
                  ->on('trx_target_wilayah')
                  ->onDelete('cascade');

            $table->foreign('id_user_pelapor')
                  ->references('id_user')
                  ->on('mst_user')
                  ->onDelete('cascade');

            $table->foreign('id_user_verifikator')
                  ->references('id_user')
                  ->on('mst_user')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trx_laporan_progres');
    }
};