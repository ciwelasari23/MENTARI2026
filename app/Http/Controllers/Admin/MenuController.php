<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MstMenu;
use App\Models\Permission;

class MenuController extends Controller
{
    public function index()
    {
        // Ambil semua menu beserta parent-nya
        $menus = MstMenu::with('parent')->orderBy('created_at', 'desc')->get();
        // Ambil menu utama (untuk dropdown pilihan parent)
        $parentMenus = MstMenu::whereNull('parent_id')->orderBy('nama_menu')->get();
        
        return view('admin.master.menu', compact('menus', 'parentMenus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_menu' => 'required|string|max:100|unique:mst_menu,nama_menu',
            'url' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:mst_menu,id_menu'
        ]);

        DB::beginTransaction();
        try {
            $menu = MstMenu::create([
                'parent_id' => $request->parent_id,
                'nama_menu' => $request->nama_menu,
                'url' => $request->url ?? '#'
            ]);

            Permission::create([
                'display_name' => $request->nama_menu,
                'name' => strtolower(str_replace(' ', '_', $request->nama_menu))
            ]);

            DB::commit();
            return redirect()->route('admin.menu.index')->with('success', 'Menu dan Hak Akses berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, int|string $id)
    {
        $request->validate([
            'nama_menu' => 'required|string|max:100',
            'url' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:mst_menu,id_menu'
        ]);

        DB::beginTransaction();
        try {
            $menu = MstMenu::findOrFail($id);
            $oldName = $menu->nama_menu;

            $menu->update([
                'parent_id' => $request->parent_id,
                'nama_menu' => $request->nama_menu,
                'url' => $request->url ?? '#'
            ]);

            $permission = Permission::where('display_name', $oldName)->first();
            if ($permission) {
                $permission->update([
                    'display_name' => $request->nama_menu,
                    'name' => strtolower(str_replace(' ', '_', $request->nama_menu))
                ]);
            }

            DB::commit();
            return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['Terjadi kesalahan saat mengupdate.']);
        }
    }

    public function destroy(int|string $id)
    {
        DB::beginTransaction();
        try {
            $menu = MstMenu::findOrFail($id);
            Permission::where('display_name', $menu->nama_menu)->delete();
            $menu->delete();

            DB::commit();
            return redirect()->route('admin.menu.index')->with('success', 'Menu dan Hak Akses berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['Gagal menghapus data. Pastikan menu tidak memiliki submenu.']);
        }
    }
}