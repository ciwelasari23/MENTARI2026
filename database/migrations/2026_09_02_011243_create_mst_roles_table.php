<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mst_role', function (Blueprint $table) {
            $table->integer('id_role')->autoIncrement();
            $table->string('nama_role', 50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Ubah dari 'mst_roles' menjadi 'mst_role' agar sesuai dengan saat pembuatan
        Schema::dropIfExists('mst_role');
    }
};