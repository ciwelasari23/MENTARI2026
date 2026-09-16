<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MstWilayah;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WilayahController extends Controller
{
    /**
     * Menampilkan daftar master Wilayah dengan fitur pencarian dan paginasi.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $query = MstWilayah::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id_wilayah', 'like', "%{$search}%")
                  ->orWhere('kode_wilayah', 'like', "%{$search}%")
                  ->orWhere('nama_provinsi', 'like', "%{$search}%")
                  ->orWhere('kode_nama_kabkota', 'like', "%{$search}%")
                  ->orWhere('kode_nama_kecamatan', 'like', "%{$search}%")
                  ->orWhere('kode_nama_desa', 'like', "%{$search}%")
                  ->orWhere('kode_nama_sls', 'like', "%{$search}%")
                  ->orWhere('kode_nama_sub_sls', 'like', "%{$search}%");
            });
        }

        $wilayahs = $query->paginate(15)->withQueryString();

        return view('admin.master.wilayah', compact('wilayahs'));
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

        $validator = \Illuminate\Support\Facades\Validator::make($data, [
            'id_wilayah'          => 'required|string|max:50|unique:mst_wilayah,id_wilayah',
            'kode_wilayah'        => 'nullable|string|max:50',
            'nama_provinsi'       => 'required|string|max:100', // Wajib diisi sesuai form
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
     * Mengimpor data master Wilayah dari file CSV/Excel.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file_excel' => 'required|mimes:csv,txt,xlsx,xls'
        ]);

        try {
            $file = $request->file('file_excel');
            $handle = fopen($file->getRealPath(), 'r');

            fgetcsv($handle, 1000, ',');

            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (empty($row) || count($row) < 2) {
                    $row = str_getcsv(implode(',', $row), ';');
                    if (count($row) < 2) continue;
                }

                if (count($row) >= 8) {
                    $id                = trim($row[0] ?? '');
                    $kodeWilayah       = trim($row[1] ?? '');
                    $namaProvinsi      = trim($row[2] ?? '');
                    $kodeNamaKabkota   = trim($row[3] ?? '');
                    $kodeNamaKecamatan = trim($row[4] ?? '');
                    $kodeNamaDesa      = trim($row[5] ?? '');
                    $kodeNamaSls       = trim($row[6] ?? '');
                    $kodeNamaSubSls    = trim($row[7] ?? '');
                } else {
                    $id                = null;
                    $kodeWilayah       = trim($row[0] ?? '');
                    $namaProvinsi      = trim($row[1] ?? '');
                    $kodeNamaKabkota   = trim($row[2] ?? '');
                    $kodeNamaKecamatan = trim($row[3] ?? '');
                    $kodeNamaDesa      = trim($row[4] ?? '');
                    $kodeNamaSls       = trim($row[5] ?? '');
                    $kodeNamaSubSls    = trim($row[6] ?? '');
                }

                if (empty($namaProvinsi) && empty($kodeWilayah)) continue;

                $data = [
                    'kode_wilayah'        => $kodeWilayah,
                    'nama_provinsi'       => $namaProvinsi,
                    'kode_nama_kabkota'   => $kodeNamaKabkota,
                    'kode_nama_kecamatan' => $kodeNamaKecamatan,
                    'kode_nama_desa'      => $kodeNamaDesa,
                    'kode_nama_sls'       => $kodeNamaSls,
                    'kode_nama_sub_sls'   => $kodeNamaSubSls,
                ];

                if (!empty($id)) {
                    MstWilayah::updateOrCreate(['id_wilayah' => $id], $data);
                } else {
                    MstWilayah::create($data);
                }
            }
            fclose($handle);

            return redirect()->route('admin.wilayah.index')
                ->with('success', 'Data master Wilayah berhasil diimpor sepenuhnya!');
        } catch (\Exception $e) {
            Log::error('Gagal impor file wilayah: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Gagal mengimpor file: ' . $e->getMessage());
        }
    }
}