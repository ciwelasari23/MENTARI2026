<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mst_wilayah', function (Blueprint $table) {
            $table->string('id_wilayah', 50)->primary();
            $table->integer('level_wilayah')->nullable(); // <-- Tambahkan kolom ini
            $table->string('kode_wilayah', 50)->nullable();
            $table->string('nama_provinsi', 100)->nullable();
            $table->string('kode_nama_kabkota', 150)->nullable();
            $table->string('kode_nama_kecamatan', 150)->nullable();
            $table->string('kode_nama_desa', 150)->nullable();
            $table->string('kode_nama_sls', 150)->nullable();
            $table->string('kode_nama_sub_sls', 150)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst_wilayah');
    }
};