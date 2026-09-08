<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mst_menu', function (Blueprint $table) {
            $table->id('id_menu');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('nama_menu', 100)->unique();
            $table->string('url', 255)->nullable()->default('#');
            $table->timestamps();

            $table->foreign('parent_id')->references('id_menu')->on('mst_menu')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mst_menu');
    }
};