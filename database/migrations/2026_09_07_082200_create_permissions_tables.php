<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel daftar izin / hak akses
        Schema::create('permissions', function (Blueprint $table) {
            $table->id('id_permission'); // Menghasilkan tipe unsignedBigInteger
            $table->string('name')->unique(); 
            $table->string('display_name');  
            $table->timestamps();
        });

        // Tabel pivot penghubung antara Role dan Permission (Many-to-Many)
        Schema::create('role_permissions', function (Blueprint $table) {
            
            // 1. UBAH DI SINI: Gunakan integer agar sama dengan tabel mst_role
            $table->integer('id_role'); 
            
            // 2. Biarkan id_permission tetap unsignedBigInteger karena menyesuaikan $table->id() di atas
            $table->unsignedBigInteger('id_permission');
            
            // Foreign Key
            $table->foreign('id_role')->references('id_role')->on('mst_role')->onDelete('cascade');
            $table->foreign('id_permission')->references('id_permission')->on('permissions')->onDelete('cascade');
            
            $table->primary(['id_role', 'id_permission']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
    }
};