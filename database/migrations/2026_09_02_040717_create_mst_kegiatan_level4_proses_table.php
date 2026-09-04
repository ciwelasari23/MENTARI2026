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
        Schema::create('mst_kegiatan_level4_proses', function (Blueprint $table) {
            $table->integer('id_proses')->autoIncrement();
            $table->integer('id_keg_detail'); // FK ke level 3
            $table->string('nama_proses', 255);
            $table->string('satuan_target', 50);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('target_total_provinsi');
            $table->timestamps();

            $table->foreign('id_keg_detail')->references('id_keg_detail')->on('mst_kegiatan_level3_detail')->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_kegiatan_level4_proses');
    }
};
