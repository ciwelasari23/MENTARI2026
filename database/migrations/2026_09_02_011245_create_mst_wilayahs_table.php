<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mst_wilayah', function (Blueprint $table) {
            // Primary Key berupa VARCHAR(16)
            $table->string('id_wilayah', 16)->primary();
            
            // Tambahkan kolom kode_wilayah yang diminta oleh Seeder
            $table->string('kode_wilayah', 20)->nullable();
            
            $table->string('nama_wilayah', 100);
            
            // Buat nullable agar tidak error jika Seeder tidak mengirimkan level
            $table->integer('level_wilayah')->nullable();
            
            $table->string('id_parent_wilayah', 16)->nullable();
            $table->timestamps();

            // Self-referencing Foreign Key
            $table->foreign('id_parent_wilayah')
                ->references('id_wilayah')
                ->on('mst_wilayah')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Hapus huruf 's' di belakang agar nama tabel sesuai
        Schema::dropIfExists('mst_wilayah');
    }
};