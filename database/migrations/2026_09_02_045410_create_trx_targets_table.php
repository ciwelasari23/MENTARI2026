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
        Schema::create('trx_target', function (Blueprint $table) {
            $table->integer('id_target')->autoIncrement();
            $table->integer('id_proses'); // FK ke mst_kegiatan_level4_proses
            $table->string('id_wilayah', 16); // FK ke mst_wilayah
            $table->integer('target_kuantiti');
            $table->timestamps();

            $table->foreign('id_proses')->references('id_proses')->on('mst_kegiatan_level4_proses')->onDelete('cascade');
            $table->foreign('id_wilayah')->references('id_wilayah')->on('mst_wilayah')->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trx_targets');
    }
};
