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
        Schema::create('mst_kegiatan_level1_output', function (Blueprint $table) {
            $table->integer('id_output')->autoIncrement();
            $table->integer('id_team')->nullable(); // FK ke mst_team
            $table->string('nama_output', 255);
            $table->integer('tahun');
            $table->timestamps();

            $table->foreign('id_team')->references('id_team')->on('mst_team')->onDelete('set null');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_kegiatan_level1_outputs');
    }
};
