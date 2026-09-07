<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MstRole;

class RoleController extends Controller
{
    public function index()
    {
        $roles = MstRole::all();
        return view('admin.master.role', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama_role' => 'required|string|max:50']);
        MstRole::create(['nama_role' => $request->nama_role]);
        return redirect()->route('admin.role.index')->with('success', 'Role ditambahkan.');
    }

    // Penambahan tipe data int|string untuk menghilangkan peringatan Intelephense
    public function update(Request $request, int|string $id)
    {
        $request->validate(['nama_role' => 'required|string|max:50']);
        MstRole::findOrFail($id)->update(['nama_role' => $request->nama_role]);
        return redirect()->route('admin.role.index')->with('success', 'Role diperbarui.');
    }

    // Penambahan tipe data int|string untuk menghilangkan peringatan Intelephense
    public function destroy(int|string $id)
    {
        MstRole::findOrFail($id)->delete();
        return redirect()->route('admin.role.index')->with('success', 'Role dihapus.');
    }
}