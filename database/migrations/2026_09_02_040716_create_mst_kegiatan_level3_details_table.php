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
        Schema::create('mst_kegiatan_level3_detail', function (Blueprint $table) {
            $table->integer('id_keg_detail')->autoIncrement();
            $table->integer('id_kegiatan'); // FK ke level 2
            $table->string('nama_keg_detail', 255);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('target_total');
            $table->string('satuan_target', 50);
            $table->timestamps();

            $table->foreign('id_kegiatan')->references('id_kegiatan')->on('mst_kegiatan_level2_kegiatan')->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_kegiatan_level3_details');
    }
};
