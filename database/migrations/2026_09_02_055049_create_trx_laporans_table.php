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
        Schema::create('trx_laporan', function (Blueprint $table) {
            $table->integer('id_laporan')->autoIncrement();
            $table->integer('id_target'); // FK ke trx_target
            $table->integer('id_user'); // FK ke mst_user (Petugas/Mitra)
            $table->date('tanggal_lapor');
            $table->integer('realisasi_kuantiti');
            $table->string('link_bukti', 255)->nullable(); // Tautan GDrive/Dokumen
            $table->enum('status_laporan', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('catatan_verifikator')->nullable();
            $table->timestamps();

            $table->foreign('id_target')->references('id_target')->on('trx_target')->onDelete('cascade');
            // Sesuaikan 'id_user' dan 'mst_user' jika tabel autentikasi Anda bernama 'users' dan ber-PK 'id'
            $table->foreign('id_user')->references('id_user')->on('mst_user')->onDelete('cascade'); 
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trx_laporans');
    }
};
