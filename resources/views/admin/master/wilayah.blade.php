@extends('layouts.admin')

@section('title', 'Master Data Wilayah')

@section('header', 'Master Data Wilayah')

@section('content')

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">
<link rel="stylesheet" href="{{ asset('css/wilayah.css') }}">

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <div class="mb-4">
        <h2 class="text-base font-bold text-gray-800">Filter & Pencarian Wilayah</h2>
        <p class="text-xs text-gray-500 mt-0.5">Saring data wilayah berdasarkan Kabupaten/Kota, Kecamatan, Desa, atau kata kunci tertentu.</p>
    </div>

    <form action="{{ route('admin.wilayah.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
  
        <input type="hidden" name="perPage" value="{{ request('perPage', 10) }}">

        <div class="relative" x-data="{ 
            open: false, 
            searchQuery: '{{ request('kabkota') }}', 
            selectedVal: '{{ request('kabkota') }}',
            items: {{ json_encode($listKabkota->pluck('kode_nama_kabkota')->filter()->values()) }},
            init() {
                this.selectedVal = '{{ request('kabkota') }}';
                this.searchQuery = '{{ request('kabkota') }}';
            },
            get filteredItems() {
                if (this.searchQuery === '' || this.selectedVal === this.searchQuery) return this.items;
                return this.items.filter(i => i.toLowerCase().includes(this.searchQuery.toLowerCase()));
            }
        }" @click.away="open = false">
            <label class="block text-xs font-bold text-gray-700 mb-1">Kabupaten/Kota</label>
            <div @click="open = !open" class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs bg-white cursor-pointer flex items-center justify-between shadow-sm">
                <span x-text="selectedVal || 'Semua Kab/Kota'" :class="{'text-gray-400': !selectedVal, 'text-gray-800': selectedVal}"></span>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </div>
            <input type="hidden" name="kabkota" x-model="selectedVal">

            <div x-show="open" class="absolute z-50 mt-1.5 w-full bg-white border border-gray-200 rounded-xl shadow-xl p-2.5 max-h-60 overflow-y-auto" style="display: none;">
                <input type="text" x-model="searchQuery" placeholder="Cari Kab/Kota..." class="w-full px-3.5 py-2 border border-gray-200 rounded-lg text-xs mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                <ul>
                    <li @click="selectedVal = ''; searchQuery = ''; open = false" class="px-3 py-2 hover:bg-gray-100 rounded-lg cursor-pointer text-xs text-gray-500">Semua Kab/Kota</li>
                    <template x-for="item in filteredItems" :key="item">
                        <li @click="selectedVal = item; searchQuery = item; open = false" class="px-3 py-2 hover:bg-emerald-50 hover:text-emerald-700 rounded-lg cursor-pointer text-xs text-gray-800 flex items-center justify-between">
                            <span x-text="item"></span>
                            <span x-show="selectedVal == item" class="text-emerald-600 font-bold">✓</span>
                        </li>
                    </template>
                </ul>
            </div>
        </div>

        <!-- Filter Kecamatan -->
        <div class="relative" x-data="{ 
            open: false, 
            searchQuery: '{{ request('kecamatan') }}', 
            selectedVal: '{{ request('kecamatan') }}',
            items: {{ json_encode($listKecamatan->pluck('kode_nama_kecamatan')->filter()->values()) }},
            get filteredItems() {
                if (this.searchQuery === '' || this.selectedVal === this.searchQuery) return this.items;
                return this.items.filter(i => i.toLowerCase().includes(this.searchQuery.toLowerCase()));
            }
        }" @click.away="open = false">
            <label class="block text-xs font-bold text-gray-700 mb-1">Kecamatan</label>
            <div @click="open = !open" class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs bg-white cursor-pointer flex items-center justify-between shadow-sm">
                <span x-text="selectedVal || 'Semua Kecamatan'" :class="{'text-gray-400': !selectedVal, 'text-gray-800': selectedVal}"></span>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </div>
            <input type="hidden" name="kecamatan" x-model="selectedVal">

            <div x-show="open" class="absolute z-50 mt-1.5 w-full bg-white border border-gray-200 rounded-xl shadow-xl p-2.5 max-h-60 overflow-y-auto" style="display: none;">
                <input type="text" x-model="searchQuery" placeholder="Cari Kecamatan..." class="w-full px-3.5 py-2 border border-gray-200 rounded-lg text-xs mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                <ul>
                    <li @click="selectedVal = ''; searchQuery = ''; open = false" class="px-3 py-2 hover:bg-gray-100 rounded-lg cursor-pointer text-xs text-gray-500">Semua Kecamatan</li>
                    <template x-for="item in filteredItems" :key="item">
                        <li @click="selectedVal = item; searchQuery = item; open = false" class="px-3 py-2 hover:bg-emerald-50 hover:text-emerald-700 rounded-lg cursor-pointer text-xs text-gray-800 flex items-center justify-between">
                            <span x-text="item"></span>
                            <span x-show="selectedVal == item" class="text-emerald-600 font-bold">✓</span>
                        </li>
                    </template>
                </ul>
            </div>
        </div>

        <!-- Filter Desa/Kel -->
        <div class="relative" x-data="{ 
            open: false, 
            searchQuery: '{{ request('desa') }}', 
            selectedVal: '{{ request('desa') }}',
            items: {{ json_encode($listDesa->pluck('kode_nama_desa')->filter()->values()) }},
            get filteredItems() {
                if (this.searchQuery === '' || this.selectedVal === this.searchQuery) return this.items;
                return this.items.filter(i => i.toLowerCase().includes(this.searchQuery.toLowerCase()));
            }
        }" @click.away="open = false">
            <label class="block text-xs font-bold text-gray-700 mb-1">Desa/Kel</label>
            <div @click="open = !open" class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs bg-white cursor-pointer flex items-center justify-between shadow-sm">
                <span x-text="selectedVal || 'Semua Desa'" :class="{'text-gray-400': !selectedVal, 'text-gray-800': selectedVal}"></span>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </div>
            <input type="hidden" name="desa" x-model="selectedVal">

            <div x-show="open" class="absolute z-50 mt-1.5 w-full bg-white border border-gray-200 rounded-xl shadow-xl p-2.5 max-h-60 overflow-y-auto" style="display: none;">
                <input type="text" x-model="searchQuery" placeholder="Cari Desa/Kel..." class="w-full px-3.5 py-2 border border-gray-200 rounded-lg text-xs mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                <ul>
                    <li @click="selectedVal = ''; searchQuery = ''; open = false" class="px-3 py-2 hover:bg-gray-100 rounded-lg cursor-pointer text-xs text-gray-500">Semua Desa</li>
                    <template x-for="item in filteredItems" :key="item">
                        <li @click="selectedVal = item; searchQuery = item; open = false" class="px-3 py-2 hover:bg-emerald-50 hover:text-emerald-700 rounded-lg cursor-pointer text-xs text-gray-800 flex items-center justify-between">
                            <span x-text="item"></span>
                            <span x-show="selectedVal == item" class="text-emerald-600 font-bold">✓</span>
                        </li>
                    </template>
                </ul>
            </div>
        </div>

        <!-- Pencarian Teks Bebas -->
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Cari Wilayah / ID</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik kata kunci..." class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs bg-white focus:ring-1 focus:ring-blue-500 focus:outline-none shadow-sm">
        </div>

        <div class="lg:col-span-4 flex items-center justify-end gap-2 pt-2">
            <button type="submit" class="bg-[#10b981] hover:bg-emerald-600 text-white px-4 py-2.5 rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-1.5">
                Terapkan
            </button>
            @if(request('kabkota') || request('kecamatan') || request('desa') || request('search'))
                <a href="{{ route('admin.wilayah.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2.5 rounded-xl text-xs font-bold hover:bg-gray-200 transition">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

<!-- KOTAK STATISTIK RINGKASAN DATA -->
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
        <span class="text-[10px] font-bold text-gray-400 uppercase">Total Data</span>
        <h3 class="text-base font-bold text-gray-800 mt-1">{{ method_exists($wilayahs, 'total') ? number_format($wilayahs->total(), 0, ',', '.') : count($wilayahs) }}</h3>
    </div>
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
        <span class="text-[10px] font-bold text-gray-400 uppercase">Provinsi</span>
        <h3 class="text-base font-bold text-gray-800 mt-1">1</h3>
    </div>
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
        <span class="text-[10px] font-bold text-gray-400 uppercase">Kabupaten/Kota</span>
        <h3 class="text-base font-bold text-gray-800 mt-1">{{ isset($listKabkota) ? number_format($listKabkota->count(), 0, ',', '.') : 0 }}</h3>
    </div>
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
        <span class="text-[10px] font-bold text-gray-400 uppercase">Kecamatan</span>
        <h3 class="text-base font-bold text-gray-800 mt-1">{{ isset($listKecamatan) ? number_format($listKecamatan->count(), 0, ',', '.') : 0 }}</h3>
    </div>
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
        <span class="text-[10px] font-bold text-gray-400 uppercase">Desa/Kelurahan</span>
        <h3 class="text-base font-bold text-gray-800 mt-1">{{ isset($listDesa) ? number_format($listDesa->count(), 0, ',', '.') : 0 }}</h3>
    </div>
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
        <span class="text-[10px] font-bold text-gray-400 uppercase">Total KK</span>
        <h3 class="text-base font-bold text-gray-800 mt-1">{{ isset($totalKK) ? number_format($totalKK, 0, ',', '.') : '-' }}</h3>
    </div>
</div>

<!-- KONTEN TABEL & DAFTAR WILAYAH -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6 space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-bold text-gray-800">Daftar Wilayah Administrasi</h2>
            <p class="text-xs text-gray-500 mt-0.5">Kelola data wilayah.</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Form Entries per Page -->
            <form id="form-entries" action="{{ route('admin.wilayah.index') }}" method="GET" class="flex items-center gap-2 text-xs text-gray-600">
                @foreach(request()->except(['page', 'perPage', '_token']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <select name="perPage" onchange="document.getElementById('form-entries').submit()" class="border border-gray-200 rounded-lg px-2.5 py-1.5 bg-white text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="10" {{ request('perPage', 10) == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('perPage') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('perPage') == 100 ? 'selected' : '' }}>100</option>
                    <option value="1000" {{ request('perPage') == 1000 ? 'selected' : '' }}>1000</option>
                </select>
                <span>entries per page</span>
            </form>

            <a href="{{ route('admin.wilayah.export', request()->query()) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 border border-gray-200 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export CSV
            </a>
        </div>
    </div>

    <div class="overflow-x-auto border border-gray-100 rounded-xl">
        <table id="wilayahTable" class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/70 border-b border-gray-100 text-gray-500 text-[11px] uppercase tracking-wider font-semibold">
                    <th class="py-3.5 px-4 text-center w-12 font-bold">No</th>
                    <th class="py-3.5 px-4 font-bold">IDSubSls</th>
                    <th class="py-3.5 px-4 font-bold">Provinsi</th>
                    <th class="py-3.5 px-4 font-bold">Kab/Kota</th>
                    <th class="py-3.5 px-4 font-bold">Kecamatan</th>
                    <th class="py-3.5 px-4 font-bold">Desa/Kel</th>
                    <th class="py-3.5 px-4 font-bold">SLS/RT/RW</th>
                    <th class="py-3.5 px-4 font-bold">Sub SLS</th>
                    <th class="py-3.5 px-4 text-center font-bold">KK</th>
                    <th class="py-3.5 px-4 text-center font-bold w-24">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                @if(isset($wilayahs) && count($wilayahs) > 0)
                    @foreach($wilayahs as $index => $item)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="py-3.5 px-4 text-center font-semibold text-gray-400 text-xs">
                            {{ method_exists($wilayahs, 'firstItem') ? $wilayahs->firstItem() + $index : $index + 1 }}
                        </td>
                        <td class="py-3.5 px-4 text-gray-600 font-mono font-semibold text-xs">{{ $item->id_wilayah }}</td>
                        <td class="py-3.5 px-4 font-bold text-gray-800 text-sm">{{ $item->nama_provinsi ?? '-' }}</td>
                        <td class="py-3.5 px-4 text-gray-700 text-sm">{{ $item->kode_nama_kabkota ?? '-' }}</td>
                        <td class="py-3.5 px-4 text-gray-600 text-sm">{{ $item->kode_nama_kecamatan ?? '-' }}</td>
                        <td class="py-3.5 px-4 text-gray-600 text-sm">{{ $item->kode_nama_desa ?? '-' }}</td>
                        <td class="py-3.5 px-4 text-gray-600 font-mono text-xs">{{ $item->kode_nama_sls ?? '-' }}</td>
                        <td class="py-3.5 px-4 text-gray-600 font-mono text-xs">{{ $item->kode_nama_sub_sls ?? '-' }}</td>
                        <td class="py-3.5 px-4 text-center font-mono text-gray-700 text-sm">
                            {{ isset($item->jumlah_kk) ? number_format($item->jumlah_kk, 0, ',', '.') : '-' }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <button type="button" class="bg-emerald-50 hover:bg-emerald-500 text-emerald-600 hover:text-white px-3 py-1.5 rounded-xl text-xs font-bold transition btn-detail shadow-sm" data-item='@json($item)'>
                                Lihat Peta
                            </button>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="10" class="py-8 px-4 text-center text-gray-400 italic text-xs">Belum ada data wilayah yang sesuai.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    
    <!-- PAGINATION SECTION -->
    @if(method_exists($wilayahs, 'links'))
    <div class="pt-3 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500">
        <div>
            Menampilkan <span class="font-semibold text-gray-700">{{ $wilayahs->firstItem() ?? 0 }}</span> ke <span class="font-semibold text-gray-700">{{ $wilayahs->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-gray-700">{{ number_format($wilayahs->total(), 0, ',', '.') }}</span> entri
        </div>

        <div class="flex items-center gap-1.5">
            @if ($wilayahs->onFirstPage())
                <span class="px-3.5 py-2 border border-gray-200 rounded-xl bg-gray-50 text-gray-300 cursor-not-allowed flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </span>
            @else
                <a href="{{ $wilayahs->previousPageUrl() }}" class="px-3.5 py-2 border border-gray-300 rounded-xl bg-white text-gray-700 hover:bg-gray-50 flex items-center justify-center transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
            @endif

            <span class="px-4 py-2 border border-emerald-500 bg-emerald-500 text-white font-bold rounded-xl text-xs shadow-sm">
                {{ $wilayahs->currentPage() }}
            </span>

            @if ($wilayahs->hasMorePages())
                <a href="{{ $wilayahs->nextPageUrl() }}" class="px-3.5 py-2 border border-gray-300 rounded-xl bg-white text-gray-700 hover:bg-gray-50 flex items-center justify-center transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            @else
                <span class="px-3.5 py-2 border border-gray-200 rounded-xl bg-gray-50 text-gray-300 cursor-not-allowed flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </span>
            @endif
        </div>
    </div>
    @endif
</div>

<!-- MODAL DETAIL PETA -->
<div id="modalDetail" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4" x-show="openModal" style="display: none;">
    <div @click.away="openModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 max-h-[90vh] overflow-y-auto shadow-2xl transform transition-all text-xs" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
        <div class="flex justify-between items-start pb-4 mb-4 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-800">Detail Wilayah</h3>
            <button @click="openModal = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold">
                &times;
            </button>
        </div>
        <div class="space-y-3.5 text-xs">
            <div class="grid grid-cols-3 gap-3">
                <span class="font-bold text-gray-500">IDSubSls</span>
                <span id="detail_id_wilayah" class="col-span-2 font-mono font-semibold text-gray-800 text-sm">-</span>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <span class="font-bold text-gray-500">Provinsi</span>
                <span id="detail_nama_provinsi" class="col-span-2 font-semibold text-gray-800 text-sm">-</span>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <span class="font-bold text-gray-500">Kab/Kota</span>
                <span id="detail_kode_nama_kabkota" class="col-span-2 text-gray-800 font-semibold text-sm">-</span>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <span class="font-bold text-gray-500">Kecamatan</span>
                <span id="detail_kode_nama_kecamatan" class="col-span-2 text-gray-800 font-semibold text-sm">-</span>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <span class="font-bold text-gray-500">Desa/Kel</span>
                <span id="detail_kode_nama_desa" class="col-span-2 text-gray-800 font-semibold text-sm">-</span>
            </div>
        </div>
        <div class="flex justify-end mt-6 pt-4 border-t border-gray-100">
            <button type="button" @click="openModal = false" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition">Tutup</button>
        </div>
    </div>
</div>

<!-- DataTables JS CDN -->
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
            
            $('#detail_id_wilayah').text(data.id_wilayah || '-');
            $('#detail_nama_provinsi').text(data.nama_provinsi || '-');
            $('#detail_kode_nama_kabkota').text(data.kode_nama_kabkota || '-');
            $('#detail_kode_nama_kecamatan').text(data.kode_nama_kecamatan || '-');
            $('#detail_kode_nama_desa').text(data.kode_nama_desa || '-');
            
            Alpine.store('modal', { open: true });
            if(document.getElementById('modalDetail').__x === undefined) {
                 document.getElementById('modalDetail').classList.remove('hidden');
                 document.getElementById('modalDetail').classList.add('flex');
            }
        });
    });
</script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('openModal', () => ({
            openModal: false
        }))
        Alpine.store('modal', { open: false });
    })
</script>
@endsection