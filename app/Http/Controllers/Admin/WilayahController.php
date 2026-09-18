<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MstWilayah;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WilayahController extends Controller
{
    /**
     * Menampilkan daftar master Wilayah dengan fitur filter per kolom dan paginasi.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search'); // Tangkap input search
        $kabkota = $request->input('kabkota');
        $kecamatan = $request->input('kecamatan');
        $desa = $request->input('desa');
        $sls = $request->input('sls');
        $subSls = $request->input('sub_sls');

        $query = MstWilayah::query();

        // 1. Logika Filter Pencarian Global (Search Box)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('id_wilayah', 'LIKE', "%{$search}%")
                  ->orWhere('kode_wilayah', 'LIKE', "%{$search}%")
                  ->orWhere('nama_provinsi', 'LIKE', "%{$search}%")
                  ->orWhere('kode_nama_kabkota', 'LIKE', "%{$search}%")
                  ->orWhere('kode_nama_kecamatan', 'LIKE', "%{$search}%")
                  ->orWhere('kode_nama_desa', 'LIKE', "%{$search}%")
                  ->orWhere('kode_nama_sls', 'LIKE', "%{$search}%")
                  ->orWhere('kode_nama_sub_sls', 'LIKE', "%{$search}%");
            });
        }

        // 2. Logika Filter Berdasarkan Kolom Dropdown
        if ($kabkota) {
            $query->where('kode_nama_kabkota', $kabkota);
        }
        if ($kecamatan) {
            $query->where('kode_nama_kecamatan', $kecamatan);
        }
        if ($desa) {
            $query->where('kode_nama_desa', $desa);
        }
        if ($sls) {
            $query->where('kode_nama_sls', $sls);
        }
        if ($subSls) {
            $query->where('kode_nama_sub_sls', $subSls);
        }

        // 3. Ambil daftar unik untuk opsi dropdown filter di setiap header kolom Blade
        $listKabkota = MstWilayah::select('kode_nama_kabkota')
            ->whereNotNull('kode_nama_kabkota')
            ->where('kode_nama_kabkota', '!=', '')
            ->distinct()
            ->orderBy('kode_nama_kabkota', 'asc')
            ->get();

        $listKecamatan = MstWilayah::select('kode_nama_kecamatan')
            ->whereNotNull('kode_nama_kecamatan')
            ->where('kode_nama_kecamatan', '!=', '')
            ->distinct()
            ->orderBy('kode_nama_kecamatan', 'asc')
            ->get();

        $listDesa = MstWilayah::select('kode_nama_desa')
            ->whereNotNull('kode_nama_desa')
            ->where('kode_nama_desa', '!=', '')
            ->distinct()
            ->orderBy('kode_nama_desa', 'asc')
            ->get();

        $listSls = MstWilayah::select('kode_nama_sls')
            ->whereNotNull('kode_nama_sls')
            ->where('kode_nama_sls', '!=', '')
            ->distinct()
            ->orderBy('kode_nama_sls', 'asc')
            ->get();

        $listSubSls = MstWilayah::select('kode_nama_sub_sls')
            ->whereNotNull('kode_nama_sub_sls')
            ->where('kode_nama_sub_sls', '!=', '')
            ->distinct()
            ->orderBy('kode_nama_sub_sls', 'asc')
            ->get();

        $wilayahs = $query->paginate(15)->withQueryString();

        return view('admin.master.wilayah', compact(
            'wilayahs', 
            'listKabkota', 
            'listKecamatan', 
            'listDesa', 
            'listSls', 
            'listSubSls'
        ));
    }
    /**
     * Menyimpan data wilayah baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = [
            'id_wilayah'          => $request->input('id_wilayah') ?? $request->input('id_sub_sls'),
            'kode_wilayah'        => $request->input('kode_wilayah'),
            'nama_provinsi'       => $request->input('nama_provinsi') ?? $request->input('provinsi'),
            'kode_nama_kabkota'   => $request->input('kode_nama_kabkota') ?? $request->input('kabkota'),
            'kode_nama_kecamatan' => $request->input('kode_nama_kecamatan') ?? $request->input('kecamatan'),
            'kode_nama_desa'      => $request->input('kode_nama_desa') ?? $request->input('desa'),
            'kode_nama_sls'       => $request->input('kode_nama_sls') ?? $request->input('sls'),
            'kode_nama_sub_sls'   => $request->input('kode_nama_sub_sls') ?? $request->input('sub_sls'),
        ];

        $validator = Validator::make($data, [
            'id_wilayah'          => 'required|string|max:50|unique:mst_wilayah,id_wilayah',
            'kode_wilayah'        => 'nullable|string|max:50',
            'nama_provinsi'       => 'required|string|max:100',
            'kode_nama_kabkota'   => 'nullable|string|max:150',
            'kode_nama_kecamatan' => 'nullable|string|max:150',
            'kode_nama_desa'      => 'nullable|string|max:150',
            'kode_nama_sls'       => 'nullable|string|max:150',
            'kode_nama_sub_sls'   => 'nullable|string|max:150',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Validasi gagal: ' . implode(', ', $validator->errors()->all()));
        }

        try {
            MstWilayah::create($data);

            return redirect()->route('admin.wilayah.index')
                ->with('success', 'Wilayah berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan wilayah: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui data wilayah berdasarkan ID.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'kode_wilayah'        => 'nullable|string|max:50',
            'nama_provinsi'       => 'nullable|string|max:100',
            'kode_nama_kabkota'   => 'nullable|string|max:150',
            'kode_nama_kecamatan' => 'nullable|string|max:150',
            'kode_nama_desa'      => 'nullable|string|max:150',
            'kode_nama_sls'       => 'nullable|string|max:150',
            'kode_nama_sub_sls'   => 'nullable|string|max:150',
        ]);

        try {
            $wilayah = MstWilayah::where('id_wilayah', $id)->firstOrFail();
            $wilayah->update($validated);

            return redirect()->route('admin.wilayah.index')
                ->with('success', 'Wilayah berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal memperbarui wilayah ID ' . $id . ': ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data wilayah berdasarkan ID.
     */
    public function destroy(string $id): RedirectResponse
    {
        try {
            $wilayah = MstWilayah::where('id_wilayah', $id)->firstOrFail();
            $wilayah->delete();

            return redirect()->route('admin.wilayah.index')
                ->with('success', 'Wilayah berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus wilayah ID ' . $id . ': ' . $e->getMessage());

            return redirect()->route('admin.wilayah.index')
                ->with('error', 'Gagal menghapus wilayah: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus banyak data wilayah sekaligus (Bulk Delete).
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => 'required|string'
        ]);

        try {
            $ids = explode(',', $request->ids);
            
            MstWilayah::whereIn('id_wilayah', $ids)->delete();

            return redirect()->route('admin.wilayah.index')
                ->with('success', count($ids) . ' data terpilih berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus banyak wilayah: ' . $e->getMessage());

            return redirect()->route('admin.wilayah.index')
                ->with('error', 'Gagal menghapus data terpilih: ' . $e->getMessage());
        }
    }
}