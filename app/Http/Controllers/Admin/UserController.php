<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule; 

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = User::query();

        if ($search) {
            $query->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nip_nik', 'like', "%{$search}%"); // Ubah ke nip_nik
        }

        $users = $query->get();
        return view('admin.master.user', compact('users', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique(User::class, 'email')],
            // Validasi menunjuk ke kolom nip_nik di database
            'nip' => ['nullable', 'string', 'max:50', Rule::unique(User::class, 'nip_nik')],
            'password' => 'required|min:6',
            'role' => 'required|string',
        ]);

        // Sesuaikan key array dengan nama kolom di database Anda
        User::create([
            'nama_lengkap'  => $request->nama_lengkap,
            'email'         => $request->email,
            'nip_nik'       => $request->nip,
            'password_hash' => Hash::make($request->password),
            'kategori_user' => $request->role,
        ]);

        return redirect()->route('admin.user.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, int|string $id)
    {
        $user = User::findOrFail($id);
        $pk = $user->getKeyName();

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique(User::class, 'email')->ignore($user->{$pk}, $pk)],
            // Validasi pengecualian menunjuk ke nip_nik
            'nip' => ['nullable', 'string', 'max:50', Rule::unique(User::class, 'nip_nik')->ignore($user->{$pk}, $pk)],
            'role' => 'required|string',
        ]);

        // Sesuaikan key array dengan nama kolom di database Anda
        $data = [
            'nama_lengkap'  => $request->nama_lengkap,
            'email'         => $request->email,
            'nip_nik'       => $request->nip,
            'kategori_user' => $request->role,
        ];

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6']);
            $data['password_hash'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.user.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(int|string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.user.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        
        $pk = (new User)->getKeyName(); 
        User::whereIn($pk, $request->ids)->delete(); 

        return redirect()->route('admin.user.index')->with('success', count($request->ids) . ' pengguna berhasil dihapus secara massal.');
    }
}