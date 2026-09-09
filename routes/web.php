<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\KegiatanLevel1Controller;
use App\Http\Controllers\Admin\KegiatanLevel2Controller;
use App\Http\Controllers\Admin\KegiatanLevel3Controller;
use App\Http\Controllers\Admin\KegiatanLevel4Controller;
use App\Http\Controllers\Admin\TargetWilayahController;
use App\Http\Controllers\PelaporanController;
use App\Http\Controllers\Admin\VerifikasiLaporanController;
use App\Http\Controllers\Admin\WilayahController; 
use App\Http\Controllers\Admin\TimKerjaController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EvaluasiController;

Route::get('/', function () {
    return view('welcome');
});

// Autentikasi & Portal (Publik)
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute Google Login
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// Grup Rute dengan Middleware Auth (Umum)
Route::middleware(['auth'])->group(function () {
    
    // Halaman Dashboard (Sebelumnya Visualisasi Data)
    // Halaman Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rute Pelaporan
    Route::get('/pelaporan', [PelaporanController::class, 'index'])->name('pelaporan.index');
    Route::post('/pelaporan', [PelaporanController::class, 'store'])->name('pelaporan.store');

    // Rute Evaluasi Kegiatan
    Route::get('/evaluasi', [EvaluasiController::class, 'index'])->name('evaluasi.index');

});

// Grup Rute Admin BPS Riau
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    
    // Level 1: Output Kegiatan
    Route::get('/kegiatan/level1', [KegiatanLevel1Controller::class, 'index'])->name('level1.index');
    Route::post('/kegiatan/level1', [KegiatanLevel1Controller::class, 'store'])->name('level1.store');
    Route::put('/kegiatan/level1/{id}', [KegiatanLevel1Controller::class, 'update'])->name('level1.update');
    Route::delete('/kegiatan/level1/{id}', [KegiatanLevel1Controller::class, 'destroy'])->name('level1.destroy');
    Route::delete('/kegiatan/level1-bulk', [KegiatanLevel1Controller::class, 'bulkDestroy'])->name('level1.bulkDestroy');

    // Level 2: Kegiatan
    Route::get('/kegiatan/level2', [KegiatanLevel2Controller::class, 'index'])->name('level2.index');
    Route::post('/kegiatan/level2', [KegiatanLevel2Controller::class, 'store'])->name('level2.store');
    Route::put('/kegiatan/level2/{id}', [KegiatanLevel2Controller::class, 'update'])->name('level2.update');
    Route::delete('/kegiatan/level2/{id}', [KegiatanLevel2Controller::class, 'destroy'])->name('level2.destroy');
    Route::delete('/kegiatan/level2-bulk', [KegiatanLevel2Controller::class, 'bulkDestroy'])->name('level2.bulkDestroy');

    // Level 3: Detail Kegiatan
    Route::get('/kegiatan/level3', [KegiatanLevel3Controller::class, 'index'])->name('level3.index');
    Route::post('/kegiatan/level3', [KegiatanLevel3Controller::class, 'store'])->name('level3.store');
    Route::put('/kegiatan/level3/{id}', [KegiatanLevel3Controller::class, 'update'])->name('level3.update');
    Route::delete('/kegiatan/level3/{id}', [KegiatanLevel3Controller::class, 'destroy'])->name('level3.destroy');
    Route::delete('/kegiatan/level3-bulk', [KegiatanLevel3Controller::class, 'bulkDestroy'])->name('level3.bulkDestroy');

    // Level 4: Proses Kegiatan
    Route::get('/kegiatan/level4', [KegiatanLevel4Controller::class, 'index'])->name('level4.index');
    Route::post('/kegiatan/level4', [KegiatanLevel4Controller::class, 'store'])->name('level4.store');
    Route::put('/kegiatan/level4/{id}', [KegiatanLevel4Controller::class, 'update'])->name('level4.update');
    Route::delete('/kegiatan/level4/{id}', [KegiatanLevel4Controller::class, 'destroy'])->name('level4.destroy');  
    Route::delete('/kegiatan/level4-bulk', [KegiatanLevel4Controller::class, 'bulkDestroy'])->name('level4.bulkDestroy');
    
    // Target Wilayah (Dilindungi validasi hak akses)
    Route::get('/target', [TargetWilayahController::class, 'index'])->name('target.index');
    Route::post('/target', [TargetWilayahController::class, 'store'])->name('target.store');
    Route::put('/target/{id}', [TargetWilayahController::class, 'update'])->name('target.update');
    Route::delete('/target/{id}', [TargetWilayahController::class, 'destroy'])->name('target.destroy');

    // Verifikasi Laporan
    Route::get('/verifikasi', [VerifikasiLaporanController::class, 'index'])->name('verifikasi.index');
    Route::put('/verifikasi/{id}', [VerifikasiLaporanController::class, 'update'])->name('verifikasi.update');

    // Master Data: Wilayah
    Route::get('/master/wilayah', [WilayahController::class, 'index'])->name('wilayah.index');
    Route::post('/master/wilayah', [WilayahController::class, 'store'])->name('wilayah.store');
    Route::put('/master/wilayah/{id}', [WilayahController::class, 'update'])->name('wilayah.update');
    Route::delete('/master/wilayah/{id}', [WilayahController::class, 'destroy'])->name('wilayah.destroy');
    Route::delete('/master/wilayah-bulk', [WilayahController::class, 'bulkDestroy'])->name('wilayah.bulkDestroy');

    // Master Data: Tim Kerja
    Route::get('/master/timkerja', [TimKerjaController::class, 'index'])->name('timkerja.index');
    Route::post('/master/timkerja', [TimKerjaController::class, 'store'])->name('timkerja.store');
    Route::put('/master/timkerja/{id}', [TimKerjaController::class, 'update'])->name('timkerja.update');
    Route::delete('/master/timkerja/{id}', [TimKerjaController::class, 'destroy'])->name('timkerja.destroy');
    Route::delete('/master/timkerja-bulk', [TimKerjaController::class, 'bulkDestroy'])->name('timkerja.bulkDestroy');
    
    // Master Data: Pengguna
    Route::get('/master/user', [UserController::class, 'index'])->name('user.index');
    Route::post('/master/user', [UserController::class, 'store'])->name('user.store');
    Route::put('/master/user/{id}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/master/user/{id}', [UserController::class, 'destroy'])->name('user.destroy');
    Route::delete('/master/user-bulk', [UserController::class, 'bulkDestroy'])->name('user.bulkDestroy');

    // Master Data: Role
    Route::resource('/master/role', \App\Http\Controllers\Admin\RoleController::class);

    // Master Data: Menu (Otomatis terhubung ke Hak Akses)
    Route::resource('/master/menu', \App\Http\Controllers\Admin\MenuController::class);
    
});