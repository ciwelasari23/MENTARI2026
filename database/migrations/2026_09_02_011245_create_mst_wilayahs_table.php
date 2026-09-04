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
        Schema::create('mst_wilayah', function (Blueprint $table) {
            // Karena Primary Key berupa VARCHAR(16)
            $table->string('id_wilayah', 16)->primary();
            $table->string('nama_wilayah', 100);
            $table->integer('level_wilayah');
            $table->string('id_parent_wilayah', 16)->nullable();
            $table->timestamps();

            // Self-referencing Foreign Key
            $table->foreign('id_parent_wilayah')
                ->references('id_wilayah')
                ->on('mst_wilayah')
                ->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_wilayahs');
    }
};
