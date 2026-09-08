<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreWilayahRequest;
use App\Http\Requests\UpdateWilayahRequest;
use App\Models\MstWilayah;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WilayahController extends Controller
{
    /**
     * Menampilkan daftar wilayah dengan fitur pencarian.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $query = MstWilayah::query();

        if ($search) {
            $query->where('nama_wilayah', 'like', "%{$search}%")
                  ->orWhere('id_wilayah', 'like', "%{$search}%");
        }

        $wilayahs = $query->get();
        return view('admin.master.wilayah', compact('wilayahs'));
    }

    /**
     * Menyimpan data wilayah baru ke database.
     */
    public function store(StoreWilayahRequest $request): RedirectResponse
    {
        try {
            MstWilayah::create($request->validated());

            return redirect()->route('admin.wilayah.index')
                ->with('success', 'Wilayah berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan wilayah: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan sistem saat menyimpan data.');
        }
    }

    /**
     * Memperbarui data wilayah yang ada.
     */
    public function update(UpdateWilayahRequest $request, string $id): RedirectResponse
    {
        try {
            $wilayah = MstWilayah::findOrFail($id);
            $wilayah->update($request->validated());

            return redirect()->route('admin.wilayah.index')
                ->with('success', 'Wilayah berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal memperbarui wilayah ID ' . $id . ': ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan sistem saat memperbarui data.');
        }
    }

    /**
     * Menghapus data wilayah berdasarkan ID.
     */
    public function destroy(string $id): RedirectResponse
    {
        try {
            $wilayah = MstWilayah::findOrFail($id);
            $wilayah->delete();

            return redirect()->route('admin.wilayah.index')
                ->with('success', 'Wilayah berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus wilayah ID ' . $id . ': ' . $e->getMessage());

            return redirect()->route('admin.wilayah.index')
                ->with('error', 'Gagal menghapus wilayah karena kendala sistem.');
        }
    }

    /**
     * Menghapus beberapa data wilayah sekaligus.
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $request->validate(['ids' => 'required|array']);

        try {
            MstWilayah::whereIn('id_wilayah', $request->ids)->delete();

            return redirect()->route('admin.wilayah.index')
                ->with('success', 'Wilayah terpilih berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal melakukan bulk delete wilayah: ' . $e->getMessage());

            return redirect()->route('admin.wilayah.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data terpilih.');
        }
    }
}