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
        Schema::create('mst_user', function (Blueprint $table) {
            $table->integer('id_user')->autoIncrement();
            $table->string('id_wilayah', 16)->nullable();
            $table->integer('id_role')->nullable();
            $table->integer('id_team')->nullable();
            
            $table->string('nip_nik', 50)->unique();
            $table->string('nama_lengkap', 150);
            $table->string('email', 255)->unique();
            $table->string('kategori_user', 20); // contoh: Pegawai, Mitra
            $table->string('password_hash', 255); // Sesuai ERD
            
            $table->timestamps();

            // Deklarasi Foreign Key
            $table->foreign('id_wilayah')->references('id_wilayah')->on('mst_wilayah')->onDelete('set null');
            $table->foreign('id_role')->references('id_role')->on('mst_role')->onDelete('set null');
            $table->foreign('id_team')->references('id_team')->on('mst_team')->onDelete('set null');
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_user');
        Schema::dropIfExists('sessions');
    }
};
