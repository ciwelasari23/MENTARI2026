@extends('layouts.admin')

@section('title', 'Target Wilayah')
@section('header', 'Pengelolaan Target Wilayah')

@section('content')
<div class="space-y-6" x-data="{ 
    modalTambah: false, 
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

    <div class="flex flex-col xl:flex-row gap-4 justify-between items-center card-container">
        
        <h3 class="text-lg font-bold text-gray-800 w-full xl:w-auto">Daftar Target Wilayah</h3>

        <div class="flex flex-col sm:flex-row items-center gap-3 w-full xl:w-auto justify-end">

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
                    
                    <div @click="open = !open" class="form-input bg-white cursor-pointer flex items-center justify-between min-w-[280px] text-sm py-2">
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
                            <li x-show="searchQuery !== '' && !items.includes(searchQuery)" @click="open = false; $el.closest('form').submit()" class="px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded cursor-pointer text-sm font-semibold mt-1 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                Cari: "<span x-text="searchQuery"></span>"
                            </li>
                        </ul>
                    </div>
                </div>

                @if(request('search'))
                    <a href="{{ route('admin.target.index') }}" class="bg-gray-200 text-gray-700 px-3 py-2 rounded-lg font-bold text-sm hover:bg-gray-300 flex items-center">Reset</a>
                @endif
            </form>

            <div x-show="selected.length > 0" x-transition style="display: none;">
                <form action="{{ route('admin.target.bulkDestroy') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ' + selected.length + ' data terpilih?')">
                    @csrf
                    @method('DELETE')
                    <template x-for="id in selected">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                    <button type="submit" class="btn-action-delete text-sm px-4 py-2 rounded-lg flex items-center justify-center gap-2 whitespace-nowrap w-full sm:w-auto">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Hapus Terpilih (<span x-text="selected.length"></span>)
                    </button>
                </form>
            </div>

            <button @click="modalTambah = true" class="bg-[#10b981] hover:bg-emerald-600 text-white px-5 py-2 rounded-md text-sm font-semibold transition flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Target Wilayah
            </button>
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition.duration.500ms class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-container overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-gray-50 border-b text-gray-600">
                    <th class="p-3 text-center w-12">
                        <input type="checkbox" x-model="selectAll" @change="toggleAll" class="w-4 h-4 text-teal-600 bg-white border-gray-300 rounded focus:ring-teal-500">
                    </th>
                    <th class="p-3 w-16 text-center">No</th>
                    <th class="p-3">Nama Proses Kegiatan</th>
                    <th class="p-3">Wilayah (Kabupaten/Kota)</th>
                    <th class="p-3 text-center">Target Daerah</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($targets as $index => $item)
                <tr class="border-b hover:bg-gray-50" x-data="{ modalHapus: false }">
                    
                    <td class="p-3 text-center">
                        <input type="checkbox" :value="{{ $item->id_target_wilayah }}" x-model="selected" class="w-4 h-4 text-teal-600 bg-white border-gray-300 rounded focus:ring-teal-500">
                    </td>

                    <td class="p-3 text-center">{{ $index + 1 }}</td>
                    <td class="p-3 font-medium text-gray-800">{{ $item->proses->nama_proses ?? 'Proses tidak ditemukan' }}</td>
                    
                    <!-- Hanya Menampilkan Nama Kabupaten -->
                    <td class="p-3 font-medium text-gray-600">
                        {{ $item->wilayah->kode_nama_kabkota ?? 'Wilayah tidak ditemukan' }}
                    </td>

                    <td class="p-3 font-semibold text-gray-800 text-center">{{ $item->target_daerah }}</td>
                    <td class="p-3 text-center whitespace-nowrap">
                        <div class="flex items-center justify-center gap-1">
                            <button @click="detailData = {
                                nama_proses: '{{ addslashes($item->proses->nama_proses ?? '-') }}',
                                kabkota: '{{ addslashes($item->wilayah->kode_nama_kabkota ?? '-') }}',
                                target_daerah: '{{ $item->target_daerah }}',
                                created_at: '{{ $item->created_at ? $item->created_at->format('d-m-Y H:i') : '-' }}'
                            }; modalDetail = true" class="text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 p-1.5 rounded-md transition-colors" title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </button>

                            <button @click="editData = {
                                id: '{{ $item->id_target_wilayah }}',
                                id_proses: '{{ $item->id_proses }}',
                                id_wilayah: '{{ $item->id_wilayah }}',
                                nama_wilayah: '{{ addslashes($item->wilayah->kode_nama_kabkota ?? '') }}',
                                target_daerah: '{{ $item->target_daerah }}'
                            }; modalEdit = true" class="text-amber-500 hover:text-amber-700 bg-amber-50 hover:bg-amber-100 p-1.5 rounded-md transition-colors" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>

                            <button @click="modalHapus = true" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-1.5 rounded-md transition-colors" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>

                        <!-- Modal Hapus Inline -->
                        <div x-show="modalHapus" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 text-center whitespace-normal" style="display: none;">
                            <div class="card-container max-w-sm w-full shadow-xl" @click.away="modalHapus = false">
                                <h3 class="text-lg font-bold text-gray-800 mb-2 mt-2">Konfirmasi Hapus</h3>
                                <p class="text-sm text-gray-600 mb-6">Apakah Anda yakin ingin menghapus target wilayah ini?</p>
                                <form action="{{ route('admin.target.destroy', $item->id_target_wilayah) }}" method="POST" class="flex justify-center gap-2">
                                    @csrf @method('DELETE')
                                    <button type="button" @click="modalHapus = false" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-bold">Batal</button>
                                    <button type="submit" class="btn-action-delete text-[0.875rem] px-4 py-2 rounded-lg">Ya, Hapus!</button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-6 text-center text-gray-400">
                        @if(request('search'))
                            Pencarian "{{ request('search') }}" tidak ditemukan.
                        @else
                            Belum ada data target wilayah.
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Detail Target -->
    <div x-show="modalDetail" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" style="display: none;">
        <div class="card-container max-w-md w-full shadow-xl space-y-4" @click.away="modalDetail = false">
            <h3 class="text-lg font-bold text-gray-800 border-b pb-2">Detail Target Wilayah</h3>
            <div class="space-y-3 text-sm text-gray-700">
                <div><span class="font-semibold text-gray-500 block text-xs">Nama Proses Kegiatan:</span><span class="font-medium text-gray-800" x-text="detailData.nama_proses"></span></div>
                <div><span class="font-semibold text-gray-500 block text-xs">Kabupaten/Kota:</span><span x-text="detailData.kabkota"></span></div>
                <div><span class="font-semibold text-gray-500 block text-xs">Target Daerah:</span><span class="font-bold text-teal-600 text-base" x-text="detailData.target_daerah"></span></div>
                <div><span class="font-semibold text-gray-500 block text-xs">Dibuat Pada:</span><span x-text="detailData.created_at"></span></div>
            </div>
            <div class="flex justify-end pt-2">
                <button type="button" @click="modalDetail = false" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-bold">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Target -->
    <div x-show="modalTambah" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" style="display: none;">
        <div class="card-container max-w-2xl w-full shadow-xl overflow-visible" @click.away="modalTambah = false">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Tambah Target Wilayah</h3>
            <form action="{{ route('admin.target.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <!-- Dropdown Tambah: Proses Kegiatan -->
                <div class="relative" x-data="{ 
                    openCreateProses: false, 
                    searchCreateProses: '', 
                    selectedCreateProsesId: '',
                    selectedCreateProsesLabel: '-- Pilih / Cari Proses --',
                    prosesData: {{ json_encode($prosesList->map(fn($p) => ['id' => $p->id_proses, 'label' => $p->nama_proses])) }},
                    get filteredProses() {
                        if (this.searchCreateProses === '') return this.prosesData;
                        return this.prosesData.filter(p => p.label.toLowerCase().includes(this.searchCreateProses.toLowerCase()));
                    }
                }" @click.away="openCreateProses = false">
                    <label class="form-label text-xs font-bold text-gray-700">Proses Kegiatan</label>
                    <div @click="openCreateProses = !openCreateProses" class="form-input bg-white cursor-pointer flex items-center justify-between text-sm py-2">
                        <span x-text="selectedCreateProsesLabel" :class="{'text-gray-400': !selectedCreateProsesId, 'text-gray-800 font-medium': selectedCreateProsesId}"></span>
                        <svg class="w-4 h-4 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                    <input type="hidden" name="id_proses" x-model="selectedCreateProsesId" required>
                    <div x-show="openCreateProses" class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg p-2 max-h-60 overflow-y-auto" style="display: none;">
                        <input type="text" x-model="searchCreateProses" placeholder="Cari proses..." class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <ul>
                            <template x-for="p in filteredProses" :key="p.id">
                                <li @click="selectedCreateProsesId = p.id; selectedCreateProsesLabel = p.label; openCreateProses = false; searchCreateProses = ''" class="px-3 py-2 hover:bg-emerald-50 hover:text-emerald-700 rounded cursor-pointer text-sm text-gray-800 flex items-center justify-between border-b border-gray-50">
                                    <span x-text="p.label"></span>
                                    <span x-show="selectedCreateProsesId == p.id" class="text-emerald-600 font-bold">✓</span>
                                </li>
                            </template>
                            <li x-show="filteredProses.length === 0" class="px-3 py-2 text-sm text-gray-400 text-center">Proses tidak ditemukan</li>
                        </ul>
                    </div>
                </div>

                <!-- Dropdown Tambah: Wilayah (Hanya Kabupaten) -->
                <div class="relative" x-data="{ 
                    openCreateWilayah: false, 
                    searchCreateWilayah: '', 
                    selectedCreateWilayahId: '',
                    selectedCreateWilayahLabel: '-- Pilih / Cari Kabupaten --',
                    // Filter dari Laravel: Ambil unique berdasarkan Kabupaten agar dropdown tidak kepanjangan
                    wilayahData: {{ json_encode($wilayahs->unique('kode_nama_kabkota')->values()->map(fn($w) => ['id' => $w->id_wilayah, 'label' => $w->kode_nama_kabkota])) }},
                    get filteredWilayah() {
                        if (this.searchCreateWilayah === '') return this.wilayahData;
                        return this.wilayahData.filter(w => w.label.toLowerCase().includes(this.searchCreateWilayah.toLowerCase()));
                    }
                }" @click.away="openCreateWilayah = false">
                    <label class="form-label text-xs font-bold text-gray-700">Wilayah Kabupaten</label>
                    <div @click="openCreateWilayah = !openCreateWilayah" class="form-input bg-white cursor-pointer flex items-center justify-between text-sm py-2">
                        <span x-text="selectedCreateWilayahLabel" :class="{'text-gray-400': !selectedCreateWilayahId, 'text-gray-800 font-medium': selectedCreateWilayahId}" class="truncate"></span>
                        <svg class="w-4 h-4 text-gray-500 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                    <input type="hidden" name="id_wilayah" x-model="selectedCreateWilayahId" required>
                    <div x-show="openCreateWilayah" class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg p-2 max-h-60 overflow-y-auto" style="display: none;">
                        <input type="text" x-model="searchCreateWilayah" placeholder="Cari kabupaten..." class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <ul>
                            <template x-for="w in filteredWilayah" :key="w.id">
                                <li @click="selectedCreateWilayahId = w.id; selectedCreateWilayahLabel = w.label; openCreateWilayah = false; searchCreateWilayah = ''" class="px-3 py-2 hover:bg-emerald-50 hover:text-emerald-700 rounded cursor-pointer text-sm text-gray-800 flex items-center justify-between border-b border-gray-50">
                                    <span x-text="w.label"></span>
                                    <span x-show="selectedCreateWilayahId == w.id" class="text-emerald-600 font-bold shrink-0 ml-2">✓</span>
                                </li>
                            </template>
                            <li x-show="filteredWilayah.length === 0" class="px-3 py-2 text-sm text-gray-400 text-center">Kabupaten tidak ditemukan</li>
                        </ul>
                    </div>
                </div>

                <div>
                    <label class="form-label text-xs font-bold text-gray-700">Target Daerah</label>
                    <input type="number" name="target_daerah" required min="1" class="form-input w-full px-3 py-2 border rounded-md" placeholder="Contoh: 100">
                </div>
                
                <div class="flex justify-end gap-2 mt-6 pt-2">
                    <button type="button" @click="modalTambah = false" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-bold">Batal</button>
                    <button type="submit" class="btn-teal">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Target -->
    <div x-show="modalEdit" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" style="display: none;">
        <div class="card-container max-w-2xl w-full shadow-xl overflow-visible" @click.away="modalEdit = false">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Edit Target Wilayah</h3>
            <form :action="'{{ url('admin/target') }}/' + editData.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                
                <!-- Dropdown Edit: Proses Kegiatan -->
                <div class="relative" x-data="{ 
                    openEditProses: false, 
                    searchEditProses: '', 
                    prosesData: {{ json_encode($prosesList->map(fn($p) => ['id' => $p->id_proses, 'label' => $p->nama_proses])) }},
                    get selectedEditProsesLabel() {
                        const found = this.prosesData.find(p => p.id == editData.id_proses);
                        return found ? found.label : '-- Pilih / Cari Proses --';
                    },
                    get filteredProses() {
                        if (this.searchEditProses === '') return this.prosesData;
                        return this.prosesData.filter(p => p.label.toLowerCase().includes(this.searchEditProses.toLowerCase()));
                    }
                }" @click.away="openEditProses = false">
                    <label class="form-label text-xs font-bold text-gray-700">Proses Kegiatan</label>
                    <div @click="openEditProses = !openEditProses" class="form-input bg-white cursor-pointer flex items-center justify-between text-sm py-2">
                        <span x-text="selectedEditProsesLabel" :class="{'text-gray-400': !editData.id_proses, 'text-gray-800 font-medium': editData.id_proses}"></span>
                        <svg class="w-4 h-4 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                    <input type="hidden" name="id_proses" :value="editData.id_proses" required>
                    <div x-show="openEditProses" class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg p-2 max-h-60 overflow-y-auto" style="display: none;">
                        <input type="text" x-model="searchEditProses" placeholder="Cari proses..." class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <ul>
                            <template x-for="p in filteredProses" :key="p.id">
                                <li @click="editData.id_proses = p.id; openEditProses = false; searchEditProses = ''" class="px-3 py-2 hover:bg-emerald-50 hover:text-emerald-700 rounded cursor-pointer text-sm text-gray-800 flex items-center justify-between border-b border-gray-50">
                                    <span x-text="p.label"></span>
                                    <span x-show="editData.id_proses == p.id" class="text-emerald-600 font-bold">✓</span>
                                </li>
                            </template>
                            <li x-show="filteredProses.length === 0" class="px-3 py-2 text-sm text-gray-400 text-center">Proses tidak ditemukan</li>
                        </ul>
                    </div>
                </div>

                <!-- Dropdown Edit: Wilayah (Hanya Kabupaten) -->
                <div class="relative" x-data="{ 
                    openEditWilayah: false, 
                    searchEditWilayah: '', 
                    wilayahData: {{ json_encode($wilayahs->unique('kode_nama_kabkota')->values()->map(fn($w) => ['id' => $w->id_wilayah, 'label' => $w->kode_nama_kabkota])) }},
                    get selectedEditWilayahLabel() {
                        // Menggunakan data nama kabupaten langsung jika dropdown ID tidak pas karena unique filter
                        if(editData.nama_wilayah) return editData.nama_wilayah;
                        
                        const found = this.wilayahData.find(w => w.id == editData.id_wilayah);
                        return found ? found.label : '-- Pilih / Cari Kabupaten --';
                    },
                    get filteredWilayah() {
                        if (this.searchEditWilayah === '') return this.wilayahData;
                        return this.wilayahData.filter(w => w.label.toLowerCase().includes(this.searchEditWilayah.toLowerCase()));
                    }
                }" @click.away="openEditWilayah = false">
                    <label class="form-label text-xs font-bold text-gray-700">Wilayah Kabupaten</label>
                    <div @click="openEditWilayah = !openEditWilayah" class="form-input bg-white cursor-pointer flex items-center justify-between text-sm py-2">
                        <span x-text="selectedEditWilayahLabel" :class="{'text-gray-400': !editData.id_wilayah, 'text-gray-800 font-medium': editData.id_wilayah}" class="truncate"></span>
                        <svg class="w-4 h-4 text-gray-500 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                    <input type="hidden" name="id_wilayah" :value="editData.id_wilayah" required>
                    <div x-show="openEditWilayah" class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg p-2 max-h-60 overflow-y-auto" style="display: none;">
                        <input type="text" x-model="searchEditWilayah" placeholder="Cari kabupaten..." class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <ul>
                            <template x-for="w in filteredWilayah" :key="w.id">
                                <li @click="editData.id_wilayah = w.id; editData.nama_wilayah = w.label; openEditWilayah = false; searchEditWilayah = ''" class="px-3 py-2 hover:bg-emerald-50 hover:text-emerald-700 rounded cursor-pointer text-sm text-gray-800 flex items-center justify-between border-b border-gray-50">
                                    <span x-text="w.label"></span>
                                    <span x-show="editData.id_wilayah == w.id" class="text-emerald-600 font-bold shrink-0 ml-2">✓</span>
                                </li>
                            </template>
                            <li x-show="filteredWilayah.length === 0" class="px-3 py-2 text-sm text-gray-400 text-center">Kabupaten tidak ditemukan</li>
                        </ul>
                    </div>
                </div>

                <div>
                    <label class="form-label text-xs font-bold text-gray-700">Target Daerah</label>
                    <input type="number" name="target_daerah" x-model="editData.target_daerah" required min="1" class="form-input w-full px-3 py-2 border rounded-md">
                </div>
                
                <div class="flex justify-end gap-2 mt-6 pt-2">
                    <button type="button" @click="modalEdit = false" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-bold">Batal</button>
                    <button type="submit" class="btn-teal">Perbarui</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection