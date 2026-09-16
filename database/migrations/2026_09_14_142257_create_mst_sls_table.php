<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mst_sls', function (Blueprint $table) {
            $table->id();
            $table->string('semester', 10)->nullable();
            $table->string('idsubsls', 30)->unique();
            $table->string('nmsls', 100);
            $table->string('nama_ket', 100)->nullable();
            $table->string('jenis', 50)->nullable();
            $table->string('kdprov', 5);
            $table->string('nmprov', 50);
            $table->string('kdkab', 10);
            $table->string('nmkab', 50);
            $table->string('kdkec', 15);
            $table->string('nmkec', 50);
            $table->string('kddesa', 20);
            $table->string('nmdesa', 50);
            $table->string('kdsls', 10);
            $table->string('klas', 10)->nullable();
            $table->integer('jumlah_kk')->default(0);
            $table->integer('jumlah_bstt')->default(0);
            $table->integer('jumlah_bsbtt')->default(0);
            $table->integer('jumlah_bsttk')->default(0);
            $table->integer('jumlah_bku')->default(0);
            $table->integer('jumlah_usaha')->default(0);
            $table->integer('jumlah_muatan')->default(0);
            $table->string('dominan', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst_sls');
    }
};