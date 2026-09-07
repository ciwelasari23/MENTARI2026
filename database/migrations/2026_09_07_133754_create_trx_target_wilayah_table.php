<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trx_target_wilayah', function (Blueprint $table) {
            $table->id('id_target_wilayah');
            $table->string('id_wilayah', 16);
            $table->unsignedInteger('id_proses');
            $table->integer('target_daerah');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trx_target_wilayah');
    }
};
