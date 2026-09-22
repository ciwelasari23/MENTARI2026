@extends('layouts.admin')

@section('title', 'Master Data Wilayah')

@section('header', 'Master Data Wilayah')

@section('content')

<!-- Tambahkan CDN DataTables & CSS Kustom Wilayah -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">
<link rel="stylesheet" href="{{ asset('css/wilayah.css') }}">

<!-- CARD FILTER UTAMA DI ATAS -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
    <div class="mb-4">
        <h2 class="text-base font-bold text-gray-800">Filter & Pencarian Wilayah</h2>
        <p class="text-xs text-gray-500 mt-0.5">Saring data wilayah berdasarkan Kabupaten/Kota, Kecamatan, atau kata kunci tertentu.</p>
    </div>

    <form action="{{ route('admin.wilayah.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
        <!-- Filter Kab/Kota -->
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Kabupaten/Kota</label>
            <select name="kabkota" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">Semua Kab/Kota</option>
                <?php if(isset($listKabkota)): ?>
                    <?php foreach($listKabkota as $kab): ?>
                        <?php if(!empty($kab->kode_nama_kabkota)): ?>
                            <option value="{{ $kab->kode_nama_kabkota }}" {{ request('kabkota') == $kab->kode_nama_kabkota ? 'selected' : '' }}>
                                {{ $kab->kode_nama_kabkota }}
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <!-- Filter Kecamatan -->
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Kecamatan</label>
            <select name="kecamatan" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">Semua Kecamatan</option>
                <?php if(isset($listKecamatan)): ?>
                    <?php foreach($listKecamatan as $kec): ?>
                        <?php if(!empty($kec->kode_nama_kecamatan)): ?>
                            <option value="{{ $kec->kode_nama_kecamatan }}" {{ request('kecamatan') == $kec->kode_nama_kecamatan ? 'selected' : '' }}>
                                {{ $kec->kode_nama_kecamatan }}
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <!-- Filter Desa/Kel -->
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Desa/Kel</label>
            <select name="desa" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">Semua Desa</option>
                <?php if(isset($listDesa)): ?>
                    <?php foreach($listDesa as $ds): ?>
                        <?php if(!empty($ds->kode_nama_desa)): ?>
                            <option value="{{ $ds->kode_nama_desa }}" {{ request('desa') == $ds->kode_nama_desa ? 'selected' : '' }}>
                                {{ $ds->kode_nama_desa }}
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <!-- Pencarian Teks Bebas -->
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Cari Wilayah / ID</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik kata kunci..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <!-- Tombol Aksi Filter (Terapkan & Reset) -->
        <div class="lg:col-span-4 flex items-center justify-end gap-2 pt-2">
            <button type="submit" class="bg-[#10b981] hover:bg-emerald-600 text-white px-5 py-2 rounded-lg text-sm font-semibold transition shadow-sm flex items-center gap-1.5">
                Terapkan
            </button>
            <?php if(request('kabkota') || request('kecamatan') || request('desa') || request('search')): ?>
                <a href="{{ route('admin.wilayah.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-200 transition">
                    Reset
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- KOTAK STATISTIK RINGKASAN DATA DI ATAS TABEL -->
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <span class="text-xs font-semibold text-gray-400 uppercase">Total Data</span>
        <h3 class="text-xl font-bold text-gray-800 mt-1">{{ method_exists($wilayahs, 'total') ? number_format($wilayahs->total(), 0, ',', '.') : count($wilayahs) }}</h3>
    </div>
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <span class="text-xs font-semibold text-gray-400 uppercase">Provinsi</span>
        <h3 class="text-xl font-bold text-gray-800 mt-1">1</h3>
    </div>
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <span class="text-xs font-semibold text-gray-400 uppercase">Kabupaten/Kota</span>
        <h3 class="text-xl font-bold text-gray-800 mt-1">{{ isset($listKabkota) ? count($listKabkota) : 0 }}</h3>
    </div>
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <span class="text-xs font-semibold text-gray-400 uppercase">Kecamatan</span>
        <h3 class="text-xl font-bold text-gray-800 mt-1">{{ isset($listKecamatan) ? count($listKecamatan) : 0 }}</h3>
    </div>
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <span class="text-xs font-semibold text-gray-400 uppercase">Desa/Kelurahan</span>
        <h3 class="text-xl font-bold text-gray-800 mt-1">{{ isset($listDesa) ? count($listDesa) : 0 }}</h3>
    </div>
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <span class="text-xs font-semibold text-gray-400 uppercase">Total KK</span>
        <h3 class="text-xl font-bold text-gray-800 mt-1">{{ isset($totalKK) ? number_format($totalKK, 0, ',', '.') : '-' }}</h3>
    </div>
</div>

<!-- KONTEN TABEL & DAFTAR WILAYAH -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Daftar Wilayah Administrasi</h2>
            <p class="text-xs text-gray-500 mt-0.5">Kelola data wilayah secara interaktif melalui tabel di bawah ini.</p>
        </div>
        <div>
            <a href="#" onclick="alert('Fitur Ekspor CSV'); return false;" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-1.5 border border-gray-200 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export CSV
            </a>
        </div>
    </div>

    <div class="overflow-x-auto border border-gray-100 rounded-lg mt-2">
        <table id="wilayahTable" class="w-full text-left border-collapse text-sm whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50 border-b text-gray-600 font-semibold text-xs uppercase tracking-wider">
                    <th class="p-4 text-center w-16">No</th>
                    <th class="p-4">IDSubSls</th>
                    <th class="p-4">Provinsi</th>
                    <th class="p-4">Kab/Kota</th>
                    <th class="p-4">Kecamatan</th>
                    <th class="p-4">Desa/Kel</th>
                    <th class="p-4">SLS/RT/RW</th>
                    <th class="p-4">Sub SLS</th>
                    <th class="p-4 text-center">KK</th>
                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if(isset($wilayahs) && count($wilayahs) > 0): ?>
                    <?php foreach($wilayahs as $index => $item): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 text-center font-medium text-gray-600">{{ method_exists($wilayahs, 'firstItem') ? $wilayahs->firstItem() + $index : $index + 1 }}</td>
                        <td class="p-4 text-gray-600 font-mono font-semibold">{{ $item->id_wilayah }}</td>
                        <td class="p-4 font-semibold text-gray-800">{{ $item->nama_provinsi ?? '-' }}</td>
                        <td class="p-4 text-gray-700">{{ $item->kode_nama_kabkota ?? '-' }}</td>
                        <td class="p-4 text-gray-600">{{ $item->kode_nama_kecamatan ?? '-' }}</td>
                        <td class="p-4 text-gray-600">{{ $item->kode_nama_desa ?? '-' }}</td>
                        <td class="p-4 text-gray-600 font-mono">{{ $item->kode_nama_sls ?? '-' }}</td>
                        <td class="p-4 text-gray-600 font-mono">{{ $item->kode_nama_sub_sls ?? '-' }}</td>
                        <!-- Kolom KK diisi dari kolom jumlah_kk di database/CSV -->
                       <!-- Kolom KK pada Tabel -->
                        <td class="p-4 text-center font-mono text-gray-700">
                            {{ isset($item->jumlah_kk) ? number_format($item->jumlah_kk, 0, ',', '.') : '-' }}
                        </td>
                        <td class="p-4 text-center">
                            <button type="button" class="bg-emerald-50 hover:bg-emerald-100 text-emerald-600 px-3 py-1 rounded text-xs font-semibold transition btn-detail" data-item='@json($item)'>
                                Lihat Peta
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="p-8 text-center text-gray-400">Belum ada data wilayah.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- PAGINATION RINGKAS & KECIL -->
    <div class="mt-4 pt-3 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
        <div class="text-gray-500 text-[11px]">
            @if(method_exists($wilayahs, 'total'))
                Menampilkan <span class="font-semibold text-gray-700">{{ $wilayahs->firstItem() ?? 0 }}</span> - <span class="font-semibold text-gray-700">{{ $wilayahs->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-gray-700">{{ number_format($wilayahs->total(), 0, ',', '.') }}</span> data
            @endif
        </div>
        <div class="custom-pagination">
            {{ method_exists($wilayahs, 'onEachSide') ? $wilayahs->onEachSide(1)->links() : '' }}
        </div>
    </div>
</div>

<!-- MODAL DETAIL PETA -->
<div id="modalDetail" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl max-w-xl w-full p-6 max-h-[90vh] overflow-y-auto shadow-xl">
        <h3 class="text-lg font-bold mb-4 text-gray-800">Detail Peta Wilayah</h3>
        <div class="space-y-3 text-sm">
            <div class="flex border-b pb-2">
                <span class="w-40 font-medium text-gray-500">IDSubSls</span>
                <span id="detail_id_wilayah" class="font-mono font-semibold text-gray-800">-</span>
            </div>
            <div class="flex border-b pb-2">
                <span class="w-40 font-medium text-gray-500">Provinsi</span>
                <span id="detail_nama_provinsi" class="font-semibold text-gray-800">-</span>
            </div>
            <div class="flex border-b pb-2">
                <span class="w-40 font-medium text-gray-500">Kab/Kota</span>
                <span id="detail_kode_nama_kabkota" class="text-gray-800">-</span>
            </div>
            <div class="flex border-b pb-2">
                <span class="w-40 font-medium text-gray-500">Kecamatan</span>
                <span id="detail_kode_nama_kecamatan" class="text-gray-800">-</span>
            </div>
            <div class="flex border-b pb-2">
                <span class="w-40 font-medium text-gray-500">Desa/Kel</span>
                <span id="detail_kode_nama_desa" class="text-gray-800">-</span>
            </div>
        </div>
        <div class="flex justify-end mt-6">
            <button type="button" onclick="closeModal('modalDetail')" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg text-sm font-semibold transition">Tutup</button>
        </div>
    </div>
</div>

<!-- jQuery & DataTables JS CDN -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>

<script>
    $(document).ready(function() {
        $('#wilayahTable').DataTable({
            "paging": false,
            "info": false,
            "searching": false,
            "ordering": true,
            "columnDefs": [
                { "orderable": false, "targets": [0, 9] }
            ],
            "language": {
                "emptyTable": "Belum ada data wilayah."
            }
        });

        $(document).on('click', '.btn-detail', function() {
            const data = JSON.parse($(this).attr('data-item'));
            openDetailModal(data);
        });
    });

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.getElementById(id).classList.remove('flex');
    }

    function openDetailModal(data) {
        document.getElementById('detail_id_wilayah').innerText = data.id_wilayah ?? '-';
        document.getElementById('detail_nama_provinsi').innerText = data.nama_provinsi ?? '-';
        document.getElementById('detail_kode_nama_kabkota').innerText = data.kode_nama_kabkota ?? '-';
        document.getElementById('detail_kode_nama_kecamatan').innerText = data.kode_nama_kecamatan ?? '-';
        document.getElementById('detail_kode_nama_desa').innerText = data.kode_nama_desa ?? '-';
        
        document.getElementById('modalDetail').classList.remove('hidden');
        document.getElementById('modalDetail').classList.add('flex');
    }
</script>
@endsection