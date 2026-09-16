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
            $table->integer('id_proses'); // Tipe disamakan dengan mst_kegiatan_level4_proses
            $table->integer('target_daerah');
            
            // Ubah ke integer biasa agar kompatibel dengan mst_user.id_user (jika id_user bertipe integer)
            $table->integer('created_by')->nullable(); 
            
            $table->timestamps();

            // Definisi Foreign Keys
            $table->foreign('id_wilayah')
                  ->references('id_wilayah')
                  ->on('mst_wilayah')
                  ->onDelete('cascade');

            $table->foreign('id_proses')
                  ->references('id_proses')
                  ->on('mst_kegiatan_level4_proses')
                  ->onDelete('cascade');

            $table->foreign('created_by')
                  ->references('id_user')
                  ->on('mst_user')
                  ->onDelete('set null');
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