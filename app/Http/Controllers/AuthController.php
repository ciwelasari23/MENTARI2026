<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

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

        // Secara default, pendaftar baru diset sebagai Pelapor (Role 3)
        $roleId = 3; 

        User::create([
            'nip_nik' => $request->nip_nik,
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password), 
            'kategori_user' => ucfirst($request->kategori_user), 
            'id_role' => $roleId,
        ]);

        // Kembalikan ke halaman login dengan pesan sukses (meminjam error bag sementara)
        return back()->withErrors(['Berhasil mendaftar! Silakan login menggunakan akun baru Anda.']);
    }
}