@extends('layouts.admin')

@section('title', 'Target Wilayah')
@section('header', 'Pengelolaan Target Wilayah')

@section('content')
<div class="space-y-6" x-data="{ 
    modalTambah: false, 
    modalImport: false,
    modalEdit: false, 
    modalDetail: false, 
    editData: {}, 
    detailData: {},
    selected: [],
    selectAll: false,
    allIds: {{ json_encode(collect($targets)->pluck('id_target_wilayah')) }},
    toggleAll() {
        this.selected = this.selectAll ? this.allIds : [];
    }
}">

    <div class="flex flex-col xl:flex-row gap-4 justify-between items-center card-container bg-white p-5 rounded-xl shadow-sm border border-gray-100 relative z-20">
        <h3 class="text-lg font-bold text-gray-800 w-full xl:w-auto">Daftar Target Wilayah</h3>

        <div class="flex flex-col sm:flex-row items-center gap-3 w-full xl:w-auto justify-end flex-wrap">

            <form action="{{ route('admin.target.index') }}" method="GET" class="flex flex-wrap gap-2 items-center w-full sm:w-auto">
                <div class="relative w-full sm:w-auto" x-data="{ 
                    open: false, 
                    searchQuery: '{{ request('search') }}',
                    items: {{ json_encode(collect($targets)->map(fn($t) => $t->proses->nama_proses ?? '')->filter()->unique()->values()) }},
                    get filteredItems() {
                        if (this.searchQuery === '') return this.items;
                        return this.items.filter(i => i.toLowerCase().includes(this.searchQuery.toLowerCase()));
                    }
                }" @click.away="open = false">
                    
                    <div @click="open = !open" class="form-input bg-white cursor-pointer flex items-center justify-between min-w-[260px] text-sm py-2 px-3 border border-gray-200 rounded-lg">
                        <span x-text="searchQuery || '-- Cari Proses Kegiatan --'" :class="{'text-gray-400': !searchQuery, 'text-gray-800 font-medium': searchQuery}"></span>
                        <svg class="w-4 h-4 text-gray-500 ml-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>

                    <input type="hidden" name="search" x-model="searchQuery">

                    <div x-show="open" class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg p-2 max-h-60 overflow-y-auto" style="display: none;">
                        <input type="text" x-model="searchQuery" @keydown.enter.prevent="$el.closest('form').submit()" placeholder="Ketik nama proses..." class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <ul>
                            <li @click="searchQuery = ''; open = false; $el.closest('form').submit()" class="px-3 py-1.5 hover:bg-gray-100 rounded cursor-pointer text-sm text-gray-500">-- Tampilkan Semua --</li>
                            <template x-for="item in filteredItems" :key="item">
                                <li @click="searchQuery = item; open = false; $el.closest('form').submit()" class="px-3 py-1.5 hover:bg-emerald-50 hover:text-emerald-700 rounded cursor-pointer text-sm text-gray-800 flex items-center justify-between">
                                    <span x-text="item"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

                @if(request('search'))
                    <a href="{{ route('admin.target.index') }}" class="bg-gray-100 text-gray-600 px-3 py-2 rounded-lg font-bold text-sm hover:bg-gray-200 flex items-center">Reset</a>
                @endif
            </form>

            <div x-show="selected.length > 0" x-transition style="display: none;">
                <form action="{{ route('admin.target.bulkDestroy') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ' + selected.length + ' data terpilih?')">
                    @csrf @method('DELETE')
                    <template x-for="id in selected">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-sm px-4 py-2 rounded-lg flex items-center justify-center gap-2 whitespace-nowrap shadow-sm">
                        Hapus (<span x-text="selected.length"></span>)
                    </button>
                </form>
            </div>

            <button @click="modalImport = true" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-2 whitespace-nowrap shadow-sm">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Import Excel
            </button>

            <button @click="modalTambah = true" class="bg-[#10b981] hover:bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-2 whitespace-nowrap shadow-sm">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Target
            </button>
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-xl shadow-sm text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl shadow-sm">
            <h3 class="text-sm font-bold text-red-800 ml-3">Validasi Gagal:</h3>
            <ul class="mt-1 text-sm text-red-700 list-disc list-inside ml-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse text-sm min-w-[700px]">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-gray-100 text-slate-500 text-[11px] uppercase tracking-wider">
                        <th class="py-4 px-4 text-center w-12">
                            <input type="checkbox" x-model="selectAll" @change="toggleAll" class="w-4 h-4 text-teal-600 border-gray-300 rounded cursor-pointer">
                        </th>
                        <th class="py-4 px-4 w-16 text-center font-bold">No</th>
                        <th class="py-4 px-4 font-bold">Nama Proses Kegiatan</th>
                        <th class="py-4 px-4 font-bold">Wilayah (Kabupaten/Kota)</th>
                        <th class="py-4 px-4 text-center font-bold">Target Daerah</th>
                        <th class="py-4 px-4 text-center font-bold w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($targets as $index => $item)
                    <tr class="hover:bg-gray-50/50 transition-colors group" x-data="{ modalHapus: false }">
                        <td class="py-4 px-4 text-center">
                            <input type="checkbox" :value="{{ $item->id_target_wilayah }}" x-model="selected" class="w-4 h-4 text-teal-600 border-gray-300 rounded cursor-pointer">
                        </td>
                        <td class="py-4 px-4 text-center text-gray-400 font-semibold">{{ $index + 1 }}</td>
                        <td class="py-4 px-4">
                            <div class="flex flex-col justify-center">
                                <span class="font-bold text-gray-800">{{ $item->proses->nama_proses ?? 'Proses tidak ditemukan' }}</span>
                                @if($item->proses && $item->proses->detail)
                                    <span class="text-gray-500 font-medium text-xs mt-0.5">{{ $item->proses->detail->nama_keg_detail }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="py-4 px-4 font-medium text-gray-600">{{ $item->wilayah->kode_nama_kabkota ?? 'Wilayah tidak ditemukan' }}</td>
                        <td class="py-4 px-4 font-bold text-[#14B8A6] text-center">{{ number_format($item->target_daerah, 0, ',', '.') }}</td>
                        <td class="py-4 px-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5 opacity-80 group-hover:opacity-100 transition-opacity">
                                <button @click="detailData = {
                                    nama_proses: '{{ addslashes($item->proses->nama_proses ?? '-') }}',
                                    kabkota: '{{ addslashes($item->wilayah->kode_nama_kabkota ?? '-') }}',
                                    target_daerah: '{{ number_format($item->target_daerah, 0, ',', '.') }}',
                                    created_at: '{{ $item->created_at ? $item->created_at->format('d-m-Y H:i') : '-' }}'
                                }; modalDetail = true" class="text-blue-500 hover:text-white hover:bg-blue-500 bg-blue-50 p-2 rounded-lg transition-colors shadow-sm" title="Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>

                                <button @click="editData = {
                                    id: '{{ $item->id_target_wilayah }}',
                                    id_proses: '{{ $item->id_proses }}',
                                    id_wilayah: '{{ $item->id_wilayah }}',
                                    nama_wilayah: '{{ addslashes($item->wilayah->kode_nama_kabkota ?? '') }}',
                                    target_daerah: '{{ $item->target_daerah }}'
                                }; modalEdit = true" class="text-amber-500 hover:text-white hover:bg-amber-500 bg-amber-50 p-2 rounded-lg transition-colors shadow-sm" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>

                                <button @click="modalHapus = true" class="text-red-500 hover:text-white hover:bg-red-500 bg-red-50 p-2 rounded-lg transition-colors shadow-sm" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>

                            <!-- Modal Hapus -->
                            <div x-show="modalHapus" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[60] p-4 text-center whitespace-normal" style="display: none;" x-transition>
                                <div class="bg-white rounded-2xl max-w-sm w-full shadow-xl p-6" @click.away="modalHapus = false">
                                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4"><svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg></div>
                                    <h3 class="text-lg font-bold text-gray-800 mb-2">Konfirmasi Hapus</h3>
                                    <p class="text-sm text-gray-600 mb-6">Apakah Anda yakin ingin menghapus target wilayah ini?</p>
                                    <form action="{{ route('admin.target.destroy', $item->id_target_wilayah) }}" method="POST" class="flex justify-center gap-2">
                                        @csrf @method('DELETE')
                                        <button type="button" @click="modalHapus = false" class="bg-gray-100 text-gray-700 px-4 py-2.5 rounded-lg text-sm font-bold hover:bg-gray-200">Batal</button>
                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2.5 rounded-lg text-sm font-bold shadow">Ya, Hapus!</button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-400 italic">
                            @if(request('search')) Pencarian tidak ditemukan. @else Belum ada data target wilayah. @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Detail -->
    <div x-show="modalDetail" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[60] p-4 text-left" style="display: none;" x-transition>
        <div class="bg-white rounded-2xl max-w-md w-full shadow-xl p-6" @click.away="modalDetail = false">
            <h3 class="text-base font-bold text-gray-800 mb-4 border-b border-gray-100 pb-3">Detail Target Wilayah</h3>
            <div class="space-y-4 text-sm">
                <div><label class="block text-xs font-bold text-gray-500 mb-1">Nama Proses Kegiatan</label><div class="bg-gray-50 border border-gray-100 rounded-lg p-3 font-semibold text-gray-800" x-text="detailData.nama_proses"></div></div>
                <div><label class="block text-xs font-bold text-gray-500 mb-1">Kabupaten/Kota</label><div class="bg-gray-50 border border-gray-100 rounded-lg p-3 font-semibold text-gray-800" x-text="detailData.kabkota"></div></div>
                <div><label class="block text-xs font-bold text-gray-500 mb-1">Target Daerah</label><div class="bg-gray-50 border border-gray-100 rounded-lg p-3 font-bold text-[#14B8A6] text-base" x-text="detailData.target_daerah"></div></div>
                <div><label class="block text-xs font-bold text-gray-500 mb-1">Dibuat Pada</label><div class="bg-gray-50 border border-gray-100 rounded-lg p-3 font-medium text-gray-700" x-text="detailData.created_at"></div></div>
            </div>
            <div class="flex justify-end mt-6">
                <button type="button" @click="modalDetail = false" class="bg-gray-100 text-gray-700 px-4 py-2.5 rounded-lg font-bold hover:bg-gray-200">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Modal Import Excel -->
    <div x-show="modalImport" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[60] p-4 text-left" style="display: none;" x-transition>
        <div class="bg-white rounded-2xl max-w-md w-full shadow-xl p-6" @click.away="modalImport = false">
            <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                <h3 class="text-base font-bold text-gray-800">Import Target Wilayah (Excel)</h3>
                <button type="button" @click="modalImport = false" class="text-gray-400 hover:text-gray-600 font-bold text-xl">&times;</button>
            </div>
            <form action="{{ route('admin.target.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-sm">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">File Excel (.xlsx, .xls, .csv)</label>
                    <input type="file" name="file" required accept=".xlsx, .xls, .csv" class="w-full border border-gray-200 rounded-lg p-2 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                </div>
                <div class="bg-gray-50 border border-gray-100 rounded-lg p-3 text-xs text-gray-600 space-y-1">
                    <p class="font-bold text-gray-700">Petunjuk:</p>
                    <p>Pastikan akumulasi target per kabupaten/kota memenuhi jumlah total Target Provinsi pada Level 4.</p>
                    <a href="{{ route('admin.target.template') }}" class="text-teal-600 font-semibold hover:underline inline-block mt-1">Unduh Template Excel Di Sini &darr;</a>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="modalImport = false" class="bg-gray-100 text-gray-700 px-4 py-2.5 rounded-lg font-bold hover:bg-gray-200">Batal</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg font-bold shadow">Upload & Import</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah Target -->
    <div x-show="modalTambah" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[60] p-4 text-left" style="display: none;" x-transition>
        <div class="bg-white rounded-2xl max-w-lg w-full shadow-xl p-6" @click.away="modalTambah = false">
            <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                <h3 class="text-base font-bold text-gray-800">Tambah Target Wilayah</h3>
                <button type="button" @click="modalTambah = false" class="text-gray-400 hover:text-gray-600 font-bold text-xl">&times;</button>
            </div>
            <form action="{{ route('admin.target.store') }}" method="POST" class="space-y-4 text-sm">
                @csrf
                
                <!-- Proses Kegiatan -->
                <div class="relative" x-data="{ 
                    openCreateProses: false, 
                    searchCreateProses: '', 
                    selectedCreateProsesId: '',
                    selectedCreateProsesLabel: '-- Pilih Proses Kegiatan --',
                    prosesData: {{ json_encode($prosesList->map(fn($p) => ['id' => $p->id_proses, 'label' => $p->nama_proses])) }},
                    get filteredProses() {
                        if (this.searchCreateProses === '') return this.prosesData;
                        return this.prosesData.filter(p => p.label.toLowerCase().includes(this.searchCreateProses.toLowerCase()));
                    }
                }" @click.away="openCreateProses = false">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Proses Kegiatan (Level 4)</label>
                    <input type="hidden" name="id_proses" x-model="selectedCreateProsesId" required>
                    
                    <button type="button" @click="openCreateProses = !openCreateProses" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-sm">
                        <span x-text="selectedCreateProsesLabel" class="truncate text-gray-700 font-medium"></span>
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="openCreateProses" class="absolute z-50 mt-1.5 w-full bg-white border border-gray-200 rounded-lg shadow-xl p-2" style="display: none;" x-transition.opacity>
                        <input type="text" x-model="searchCreateProses" placeholder="Cari proses..." class="w-full px-3 py-2 border border-gray-200 rounded-md text-sm mb-2 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" autocomplete="off" @click.stop>
                        <div class="max-h-48 overflow-y-auto space-y-0.5">
                            <template x-for="p in filteredProses" :key="p.id">
                                <div @click="selectedCreateProsesId = p.id; selectedCreateProsesLabel = p.label; openCreateProses = false; searchCreateProses = ''" 
                                     class="px-3 py-2 hover:bg-emerald-50 hover:text-emerald-700 rounded-md cursor-pointer text-sm text-gray-700 flex items-center justify-between"
                                     :class="selectedCreateProsesId == p.id ? 'bg-emerald-50 text-emerald-700 font-bold' : ''">
                                    <span x-text="p.label" class="truncate"></span>
                                    <span x-show="selectedCreateProsesId == p.id" class="text-emerald-600 font-bold">✓</span>
                                </div>
                            </template>
                            <div x-show="filteredProses.length === 0" class="px-3 py-2 text-sm text-gray-400 italic text-center">Proses tidak ditemukan</div>
                        </div>
                    </div>
                </div>

                <!-- Wilayah Kabupaten -->
                <div class="relative" x-data="{ 
                    openCreateWilayah: false, 
                    searchCreateWilayah: '', 
                    selectedCreateWilayahId: '',
                    selectedCreateWilayahLabel: '-- Pilih Wilayah Kabupaten/Kota --',
                    wilayahData: {{ json_encode($wilayahs->unique('kode_nama_kabkota')->values()->map(fn($w) => ['id' => $w->id_wilayah, 'label' => $w->kode_nama_kabkota])) }},
                    get filteredWilayah() {
                        if (this.searchCreateWilayah === '') return this.wilayahData;
                        return this.wilayahData.filter(w => w.label.toLowerCase().includes(this.searchCreateWilayah.toLowerCase()));
                    }
                }" @click.away="openCreateWilayah = false">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Wilayah Kabupaten/Kota</label>
                    <input type="hidden" name="id_wilayah" x-model="selectedCreateWilayahId" required>
                    
                    <button type="button" @click="openCreateWilayah = !openCreateWilayah" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-sm">
                        <span x-text="selectedCreateWilayahLabel" class="truncate text-gray-700 font-medium"></span>
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="openCreateWilayah" class="absolute z-50 mt-1.5 w-full bg-white border border-gray-200 rounded-lg shadow-xl p-2" style="display: none;" x-transition.opacity>
                        <input type="text" x-model="searchCreateWilayah" placeholder="Cari kabupaten..." class="w-full px-3 py-2 border border-gray-200 rounded-md text-sm mb-2 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" autocomplete="off" @click.stop>
                        <div class="max-h-48 overflow-y-auto space-y-0.5">
                            <template x-for="w in filteredWilayah" :key="w.id">
                                <div @click="selectedCreateWilayahId = w.id; selectedCreateWilayahLabel = w.label; openCreateWilayah = false; searchCreateWilayah = ''" 
                                     class="px-3 py-2 hover:bg-emerald-50 hover:text-emerald-700 rounded-md cursor-pointer text-sm text-gray-700 flex items-center justify-between"
                                     :class="selectedCreateWilayahId == w.id ? 'bg-emerald-50 text-emerald-700 font-bold' : ''">
                                    <span x-text="w.label" class="truncate"></span>
                                    <span x-show="selectedCreateWilayahId == w.id" class="text-emerald-600 font-bold">✓</span>
                                </div>
                            </template>
                            <div x-show="filteredWilayah.length === 0" class="px-3 py-2 text-sm text-gray-400 italic text-center">Kabupaten tidak ditemukan</div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Target Daerah</label>
                    <input type="number" name="target_daerah" required min="1" class="w-full border border-gray-200 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="Contoh: 100">
                    <p class="text-[11px] text-gray-500 mt-1">Validasi sistem: Akumulasi target kabupaten/kota harus pas dengan total target provinsi pada kegiatan induk.</p>
                </div>
                
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="modalTambah = false" class="bg-gray-100 text-gray-700 px-4 py-2.5 rounded-lg font-bold hover:bg-gray-200">Batal</button>
                    <button type="submit" class="bg-[#10b981] hover:bg-emerald-600 text-white px-4 py-2.5 rounded-lg font-bold shadow">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Target -->
    <div x-show="modalEdit" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[60] p-4 text-left" style="display: none;" x-transition>
        <div class="bg-white rounded-2xl max-w-lg w-full shadow-xl p-6" @click.away="modalEdit = false">
            <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                <h3 class="text-base font-bold text-gray-800">Edit Target Wilayah</h3>
                <button type="button" @click="modalEdit = false" class="text-gray-400 hover:text-gray-600 font-bold text-xl">&times;</button>
            </div>
            <form :action="'{{ url('admin/target') }}/' + editData.id" method="POST" class="space-y-4 text-sm">
                @csrf @method('PUT')
                
                <!-- Proses Kegiatan -->
                <div class="relative" x-data="{ 
                    openEditProses: false, 
                    searchEditProses: '', 
                    prosesData: {{ json_encode($prosesList->map(fn($p) => ['id' => $p->id_proses, 'label' => $p->nama_proses])) }},
                    get selectedEditProsesLabel() {
                        const found = this.prosesData.find(p => p.id == editData.id_proses);
                        return found ? found.label : '-- Pilih Proses Kegiatan --';
                    },
                    get filteredProses() {
                        if (this.searchEditProses === '') return this.prosesData;
                        return this.prosesData.filter(p => p.label.toLowerCase().includes(this.searchEditProses.toLowerCase()));
                    }
                }" @click.away="openEditProses = false">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Proses Kegiatan (Level 4)</label>
                    <input type="hidden" name="id_proses" :value="editData.id_proses" required>
                    
                    <button type="button" @click="openEditProses = !openEditProses" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-amber-500 shadow-sm">
                        <span x-text="selectedEditProsesLabel" class="truncate text-gray-700 font-medium"></span>
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="openEditProses" class="absolute z-50 mt-1.5 w-full bg-white border border-gray-200 rounded-lg shadow-xl p-2" style="display: none;" x-transition.opacity>
                        <input type="text" x-model="searchEditProses" placeholder="Cari proses..." class="w-full px-3 py-2 border border-gray-200 rounded-md text-sm mb-2 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500" autocomplete="off" @click.stop>
                        <div class="max-h-48 overflow-y-auto space-y-0.5">
                            <template x-for="p in filteredProses" :key="p.id">
                                <div @click="editData.id_proses = p.id; openEditProses = false; searchEditProses = ''" 
                                     class="px-3 py-2 hover:bg-amber-50 hover:text-amber-700 rounded-md cursor-pointer text-sm text-gray-700 flex items-center justify-between"
                                     :class="editData.id_proses == p.id ? 'bg-amber-50 text-amber-700 font-bold' : ''">
                                    <span x-text="p.label" class="truncate"></span>
                                    <span x-show="editData.id_proses == p.id" class="text-amber-600 font-bold">✓</span>
                                </div>
                            </template>
                            <div x-show="filteredProses.length === 0" class="px-3 py-2 text-sm text-gray-400 italic text-center">Proses tidak ditemukan</div>
                        </div>
                    </div>
                </div>

                <!-- Wilayah Kabupaten -->
                <div class="relative" x-data="{ 
                    openEditWilayah: false, 
                    searchEditWilayah: '', 
                    wilayahData: {{ json_encode($wilayahs->unique('kode_nama_kabkota')->values()->map(fn($w) => ['id' => $w->id_wilayah, 'label' => $w->kode_nama_kabkota])) }},
                    get selectedEditWilayahLabel() {
                        if(editData.nama_wilayah) return editData.nama_wilayah;
                        const found = this.wilayahData.find(w => w.id == editData.id_wilayah);
                        return found ? found.label : '-- Pilih Wilayah Kabupaten/Kota --';
                    },
                    get filteredWilayah() {
                        if (this.searchEditWilayah === '') return this.wilayahData;
                        return this.wilayahData.filter(w => w.label.toLowerCase().includes(this.searchEditWilayah.toLowerCase()));
                    }
                }" @click.away="openEditWilayah = false">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Wilayah Kabupaten/Kota</label>
                    <input type="hidden" name="id_wilayah" :value="editData.id_wilayah" required>
                    
                    <button type="button" @click="openEditWilayah = !openEditWilayah" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-amber-500 shadow-sm">
                        <span x-text="selectedEditWilayahLabel" class="truncate text-gray-700 font-medium"></span>
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="openEditWilayah" class="absolute z-50 mt-1.5 w-full bg-white border border-gray-200 rounded-lg shadow-xl p-2" style="display: none;" x-transition.opacity>
                        <input type="text" x-model="searchEditWilayah" placeholder="Cari kabupaten..." class="w-full px-3 py-2 border border-gray-200 rounded-md text-sm mb-2 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500" autocomplete="off" @click.stop>
                        <div class="max-h-48 overflow-y-auto space-y-0.5">
                            <template x-for="w in filteredWilayah" :key="w.id">
                                <div @click="editData.id_wilayah = w.id; editData.nama_wilayah = w.label; openEditWilayah = false; searchEditWilayah = ''" 
                                     class="px-3 py-2 hover:bg-amber-50 hover:text-amber-700 rounded-md cursor-pointer text-sm text-gray-700 flex items-center justify-between"
                                     :class="editData.id_wilayah == w.id ? 'bg-amber-50 text-amber-700 font-bold' : ''">
                                    <span x-text="w.label" class="truncate"></span>
                                    <span x-show="editData.id_wilayah == w.id" class="text-amber-600 font-bold">✓</span>
                                </div>
                            </template>
                            <div x-show="filteredWilayah.length === 0" class="px-3 py-2 text-sm text-gray-400 italic text-center">Kabupaten tidak ditemukan</div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Target Daerah</label>
                    <input type="number" name="target_daerah" x-model="editData.target_daerah" required min="1" class="w-full border border-gray-200 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="modalEdit = false" class="bg-gray-100 text-gray-700 px-4 py-2.5 rounded-lg font-bold hover:bg-gray-200">Batal</button>
                    <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2.5 rounded-lg font-bold shadow">Perbarui</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection