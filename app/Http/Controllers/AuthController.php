<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // <-- Tambahkan baris ini
use App\Models\User;

class AuthController extends Controller
{
    // 1. Menampilkan Halaman Form Login / Register
    public function index()
    {
        return view('auth.login');
    }

    // 2. Proses Validasi & Masuk (Login)
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 1. Cari user secara manual berdasarkan email
        $user = User::where('email', $credentials['email'])->first();

        // 2. Jika user ditemukan DAN password yang diketik cocok dengan password_hash di database
        if ($user && Hash::check($credentials['password'], $user->password_hash)) {
            
            // 3. Masukkan user ke dalam sistem (Login Manual)
            Auth::login($user); 
            $request->session()->regenerate();
            
            return redirect()->intended('dashboard');
        }

        // Jika email tidak ada atau password salah
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    // 3. Proses Keluar (Logout)
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    // 4. Proses Pendaftaran Akun Baru (Register)
    public function register(Request $request)
    {
        $request->validate([
            'nip_nik' => ['required', 'string', 'unique:mst_user,nip_nik'],
            'nama_lengkap' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'unique:mst_user,email'],
            'password' => ['required', 'min:6'],
            'kategori_user' => ['required', 'in:pegawai,mitra'],
        ]);

        $roleId = 3; 

        User::create([
            'nip_nik' => $request->nip_nik,
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            // HAPUS Hash::make() karena model sudah melakukan casting otomatis 'hashed'
            'password_hash' => $request->password, 
            'kategori_user' => ucfirst($request->kategori_user), 
            'id_role' => $roleId,
        ]);

        return back()->withErrors(['Berhasil mendaftar! Silakan login menggunakan akun baru Anda.']);
    }
}