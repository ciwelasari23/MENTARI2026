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
        Schema::create('mst_kegiatan_level2_kegiatan', function (Blueprint $table) {
            $table->integer('id_kegiatan')->autoIncrement();
            $table->integer('id_output'); // FK ke level 1
            $table->string('nama_kegiatan', 255);
            $table->timestamps();

            $table->foreign('id_output')->references('id_output')->on('mst_kegiatan_level1_output')->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_kegiatan_level2_kegiatans');
    }
};
