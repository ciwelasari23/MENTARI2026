<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MstRole;
use App\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        // Gunakan eager loading 'with' agar relasi terbaca di Blade
        $roles = MstRole::with('permissions')->get();
        
        // Ambil semua data permissions untuk ditampilkan di modal
        $permissions = Permission::all(); 
        
        return view('admin.master.role', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_role' => 'required|string|max:50',
            'permissions' => 'array' // Validasi tambahan opsional
        ]);

        // Buat role baru
        $role = MstRole::create([
            'nama_role' => $request->nama_role
        ]);

        // Simpan hak akses yang dicentang jika ada
        if ($request->has('permissions')) {
            $role->permissions()->sync($request->input('permissions'));
        }

        return redirect()->route('admin.role.index')->with('success', 'Role dan hak akses berhasil ditambahkan.');
    }

    public function update(Request $request, int|string $id)
    {
        $request->validate([
            'nama_role' => 'required|string|max:255',
            'permissions' => 'nullable|array' // Tambahkan validasi permissions
        ]);

        $role = MstRole::findOrFail($id);
        $role->update([
            'nama_role' => $request->nama_role
        ]);

        // Sinkronisasi hak akses
        if ($request->has('permissions')) {
            // Sync akan menghapus yang tidak dicentang dan menambah yang dicentang
            $role->permissions()->sync($request->permissions);
        } else {
            // Jika tidak ada yang dicentang, hapus semua akses
            $role->permissions()->detach();
        }

        return redirect()->route('admin.role.index')->with('success', 'Role dan hak akses berhasil diperbarui.');
    }

    public function destroy(int|string $id)
    {
        MstRole::findOrFail($id)->delete();
        return redirect()->route('admin.role.index')->with('success', 'Role dihapus.');
    }
}