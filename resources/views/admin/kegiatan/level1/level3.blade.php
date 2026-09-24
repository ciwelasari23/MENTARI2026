@extends('layouts.admin')

@section('title', 'Level 3: Detail Kegiatan')
@section('header', 'Kelola Detail Kegiatan Level 3')

@section('content')
<div class="space-y-6" x-data="{ modalTambah: false, selected: [], selectAll: false }">

    <div class="flex flex-col md:flex-row gap-4 justify-between items-start md:items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100 relative z-20">
        <h3 class="text-lg font-bold text-gray-800">Daftar Detail Kegiatan</h3>
        
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">

            <form action="{{ route('admin.level3.index') }}" method="GET" class="flex flex-row gap-2 w-full sm:w-auto">
                <div x-data="{
                    open: false,
                    searchQuery: '',
                    selectedValue: '{{ request('search') }}',
                    selectedLabel: '{{ request('search') ?: 'Semua Detail' }}',
                    options: [
                        { value: '', label: 'Semua Detail' },
                        @foreach($details->pluck('nama_keg_detail')->unique() as $k)
                        { value: '{{ addslashes($k) }}', label: '{{ addslashes($k) }}' },
                        @endforeach
                    ],
                    get filteredOptions() {
                        if (this.searchQuery === '') return this.options;
                        return this.options.filter(opt => opt.label.toLowerCase().includes(this.searchQuery.toLowerCase()));
                    },
                    selectOption(opt) {
                        this.selectedValue = opt.value;
                        this.selectedLabel = opt.label;
                        this.searchQuery = '';
                        this.open = false;
                    }
                }" class="relative w-full sm:w-64">
                    
                    <input type="hidden" name="search" :value="selectedValue">

                    <button type="button" @click="open = !open" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-white flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition-colors">
                        <span x-text="selectedLabel" class="truncate text-gray-700 font-medium"></span>
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="open" @click.away="open = false" class="absolute z-50 w-full mt-1.5 bg-white border border-gray-200 rounded-lg shadow-xl" style="display: none;" x-transition.opacity>
                        <div class="p-2 border-b border-gray-100">
                            <input type="text" x-model="searchQuery" placeholder="Cari Detail..." class="w-full px-3 py-2 border border-gray-200 rounded-md text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" autocomplete="off" @click.stop>
                        </div>
                        <div class="max-h-60 overflow-y-auto p-1.5 space-y-0.5">
                            <template x-for="opt in filteredOptions" :key="opt.value">
                                <div @click="selectOption(opt)" 
                                     class="px-3 py-2 cursor-pointer text-sm rounded-md transition-colors"
                                     :class="selectedValue === opt.value ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-100'">
                                    <span x-text="opt.label"></span>
                                </div>
                            </template>
                            <div x-show="filteredOptions.length === 0" class="px-3 py-2 text-sm text-gray-400 italic text-center">Detail tidak ditemukan</div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2.5 rounded-lg font-semibold text-sm shadow-sm flex items-center justify-center transition-colors shrink-0" title="Cari">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.level3.index') }}" class="bg-gray-100 text-gray-600 px-3 py-2.5 rounded-lg font-bold text-sm hover:bg-gray-200 flex items-center shrink-0">Reset</a>
                @endif
            </form>

            <!-- Tombol Hapus Terpilih -->
            <form action="{{ route('admin.level3.bulkDestroy') }}" method="POST" x-show="selected.length > 0" onsubmit="return confirm('Yakin ingin menghapus semua data yang dicentang? Data Level 4 di bawahnya juga bisa ikut terhapus.');" class="w-full sm:w-auto" style="display: none;">
                @csrf @method('DELETE')
                <template x-for="id in selected"><input type="hidden" name="ids[]" :value="id"></template>
                <button type="submit" class="w-full sm:w-auto bg-red-500 hover:bg-red-600 text-white flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold shadow-sm transition">
                    Hapus (<span x-text="selected.length"></span>)
                </button>
            </form>

            <!-- Tombol Tambah -->
            <button @click="modalTambah = true" class="w-full sm:w-auto bg-[#10b981] hover:bg-emerald-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold shadow-sm transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Detail
            </button>
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition.duration.500ms class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-xl shadow-sm text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl shadow-sm">
            <h3 class="text-sm font-bold text-red-800 ml-3">Gagal Menyimpan Data:</h3>
            <ul class="mt-1 text-sm text-red-700 list-disc list-inside ml-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-gray-100 text-slate-500 text-[11px] uppercase tracking-wider">
                        <th class="py-4 px-4 w-12 text-center"><input type="checkbox" x-model="selectAll" @change="selected = selectAll ? {{ json_encode($details->pluck('id_keg_detail')->map(fn($id) => (string)$id)) }} : []" class="w-4 h-4 text-[#14B8A6] border-gray-300 rounded cursor-pointer"></th>
                        <th class="py-4 px-4 w-16 text-center font-bold">No</th>
                        <th class="py-4 px-4 font-bold">Detail Kegiatan</th>
                        <th class="py-4 px-4 font-bold">Jadwal Pelaksanaan</th>
                        <th class="py-4 px-4 text-center font-bold w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($details as $index => $item)
                    <tr class="hover:bg-gray-50/50 transition-colors group" x-data="{ modalDetail: false, modalEdit: false, modalHapus: false }">
                        <td class="py-4 px-4 text-center"><input type="checkbox" x-model="selected" value="{{ $item->id_keg_detail }}" class="w-4 h-4 text-[#14B8A6] border-gray-300 rounded cursor-pointer"></td>
                        <td class="py-4 px-4 text-center text-gray-400 font-semibold text-sm">{{ $index + 1 }}</td>
                        
                        <!-- Nama Detail dan Induk -->
                        <td class="py-4 px-4">
                            <div class="flex flex-col justify-center">
                                <span class="text-sm font-bold text-gray-800">{{ $item->nama_keg_detail }}</span>
                                @if($item->kegiatan)
                                    <span class="text-xs font-medium text-gray-500 mt-1">
                                        {{ $item->kegiatan->nama_kegiatan ?? '-' }} 
                                        @if($item->kegiatan->output) | {{ $item->kegiatan->output->nama_output }} @endif
                                    </span>
                                @endif
                            </div>
                        </td>

                        <td class="py-4 px-4 text-xs font-medium text-gray-600">
                            {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }} <span class="mx-1 text-gray-400">-</span> 
                            {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}
                        </td>
                        
                        <td class="py-4 px-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5 opacity-80 group-hover:opacity-100 transition-opacity">
                                <button @click="modalDetail = true" class="text-blue-500 hover:text-white hover:bg-blue-500 bg-blue-50 p-2 rounded-lg transition-colors shadow-sm" title="Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                                <button @click="modalEdit = true" class="text-amber-500 hover:text-white hover:bg-amber-500 bg-amber-50 p-2 rounded-lg transition-colors shadow-sm" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button @click="modalHapus = true" class="text-red-500 hover:text-white hover:bg-red-500 bg-red-50 p-2 rounded-lg transition-colors shadow-sm" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>

                            <!-- Modal Detail -->
                            <div x-show="modalDetail" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[60] p-4 text-left" style="display: none;" x-transition>
                                <div class="bg-white rounded-2xl max-w-2xl w-full shadow-xl p-6" @click.away="modalDetail = false" x-transition.scale>
                                    <h3 class="text-base font-bold text-gray-800 mb-4 border-b border-gray-100 pb-3">Informasi Detail Kegiatan Level 3</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                        <div class="md:col-span-2">
                                            <label class="block text-xs font-bold text-gray-500 mb-1">Nama Detail Kegiatan</label>
                                            <div class="w-full bg-gray-50 border border-gray-100 rounded-lg p-3 font-semibold text-gray-800">{{ $item->nama_keg_detail }}</div>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-xs font-bold text-gray-500 mb-1">Kegiatan Induk (Level 2)</label>
                                            <div class="w-full bg-gray-50 border border-gray-100 rounded-lg p-3 font-semibold text-gray-800">{{ $item->kegiatan->nama_kegiatan ?? 'Tidak ada data' }} @if($item->kegiatan && $item->kegiatan->output) (| {{ $item->kegiatan->output->nama_output }}) @endif</div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 mb-1">Tanggal Mulai</label>
                                            <div class="w-full bg-gray-50 border border-gray-100 rounded-lg p-2.5 font-semibold text-gray-800">{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d F Y') }}</div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 mb-1">Tanggal Selesai</label>
                                            <div class="w-full bg-gray-50 border border-gray-100 rounded-lg p-2.5 font-semibold text-gray-800">{{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d F Y') }}</div>
                                        </div>
                                    </div>
                                    <div class="flex justify-end mt-6">
                                        <button type="button" @click="modalDetail = false" class="bg-gray-100 text-gray-700 px-4 py-2.5 rounded-lg font-bold hover:bg-gray-200">Tutup</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Edit -->
                            <div x-show="modalEdit" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[60] p-4 text-left" style="display: none;" x-transition>
                                <div class="bg-white rounded-2xl max-w-2xl w-full shadow-xl p-6" @click.away="modalEdit = false" x-transition.scale>
                                    <h3 class="text-base font-bold text-gray-800 mb-4 border-b border-gray-100 pb-3">Edit Detail Kegiatan Level 3</h3>
                                    <form action="{{ route('admin.level3.update', $item->id_keg_detail) }}" method="POST" class="text-sm">
                                        @csrf @method('PUT')
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            
                                            <!-- Dropdown Edit -->
                                            <div class="md:col-span-2 relative z-40" x-data="{
                                                openKeg: false,
                                                searchKeg: '',
                                                selectedKegValue: '{{ $item->id_kegiatan ?? '' }}',
                                                selectedKegLabel: '{{ $item->kegiatan ? addslashes($item->kegiatan->nama_kegiatan . ($item->kegiatan->output ? ' | ' . $item->kegiatan->output->nama_output : '')) : '-- Pilih Kegiatan Induk --' }}',
                                                kegiatans: [
                                                    { value: '', label: '-- Pilih Kegiatan Induk --' },
                                                    @foreach($kegiatanList as $keg)
                                                    { value: '{{ $keg->id_kegiatan }}', label: '{{ addslashes($keg->nama_kegiatan . ($keg->output ? ' | ' . $keg->output->nama_output : '')) }}' },
                                                    @endforeach
                                                ],
                                                get filteredKegiatans() {
                                                    if (this.searchKeg === '') return this.kegiatans;
                                                    return this.kegiatans.filter(k => k.label.toLowerCase().includes(this.searchKeg.toLowerCase()));
                                                },
                                                selectKeg(opt) {
                                                    this.selectedKegValue = opt.value;
                                                    this.selectedKegLabel = opt.label;
                                                    this.searchKeg = '';
                                                    this.openKeg = false;
                                                }
                                            }">
                                                <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Kegiatan Induk (Level 2)</label>
                                                <input type="hidden" name="id_kegiatan" :value="selectedKegValue" required>
                                                
                                                <button type="button" @click="openKeg = !openKeg" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-amber-500 shadow-sm transition-colors">
                                                    <span x-text="selectedKegLabel" class="truncate text-gray-700"></span>
                                                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                </button>

                                                <div x-show="openKeg" @click.away="openKeg = false" class="absolute w-full mt-1.5 bg-white border border-gray-200 rounded-lg shadow-xl" style="display: none;" x-transition.opacity>
                                                    <div class="p-2 border-b border-gray-100">
                                                        <input type="text" x-model="searchKeg" placeholder="Cari Kegiatan..." class="w-full px-3 py-2 border border-gray-200 rounded-md text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500" autocomplete="off" @click.stop>
                                                    </div>
                                                    <div class="max-h-48 overflow-y-auto p-1.5 space-y-0.5">
                                                        <template x-for="opt in filteredKegiatans" :key="opt.value">
                                                            <div @click="selectKeg(opt)" 
                                                                 class="px-3 py-2 cursor-pointer text-sm rounded-md transition-colors"
                                                                 :class="selectedKegValue === opt.value ? 'bg-amber-50 text-amber-700 font-bold' : 'text-gray-700 hover:bg-gray-100'">
                                                                <span x-text="opt.label"></span>
                                                            </div>
                                                        </template>
                                                        <div x-show="filteredKegiatans.length === 0" class="px-3 py-2 text-sm text-gray-400 italic text-center">Kegiatan tidak ditemukan</div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="md:col-span-2">
                                                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Detail Kegiatan</label>
                                                <input type="text" name="nama_keg_detail" value="{{ $item->nama_keg_detail }}" required class="w-full border border-gray-200 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-amber-500">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Mulai</label>
                                                <input type="date" name="tanggal_mulai" value="{{ $item->tanggal_mulai }}" required class="w-full border border-gray-200 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-amber-500">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Selesai</label>
                                                <input type="date" name="tanggal_selesai" value="{{ $item->tanggal_selesai }}" required class="w-full border border-gray-200 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-amber-500">
                                            </div>
                                        </div>
                                        <div class="flex justify-end gap-2 mt-6">
                                            <button type="button" @click="modalEdit = false" class="bg-gray-100 text-gray-700 px-4 py-2.5 rounded-lg font-bold hover:bg-gray-200">Batal</button>
                                            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2.5 rounded-lg font-bold shadow">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Modal Hapus -->
                            <div x-show="modalHapus" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[60] p-4 text-center whitespace-normal" style="display: none;" x-transition>
                                <div class="bg-white rounded-2xl max-w-sm w-full shadow-xl p-6" @click.away="modalHapus = false" x-transition.scale>
                                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4"><svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg></div>
                                    <h3 class="text-lg font-bold text-gray-800 mb-2">Konfirmasi Hapus</h3>
                                    <p class="text-sm text-gray-600 mb-6">Apakah Anda yakin ingin menghapus data ini? Data Level 4 di bawahnya juga bisa ikut terhapus.</p>
                                    <form action="{{ route('admin.level3.destroy', $item->id_keg_detail) }}" method="POST" class="flex justify-center gap-2">
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
                        <td colspan="5" class="p-8 text-center text-gray-400 italic">
                            @if(request('search')) Pencarian tidak ditemukan. @else Belum ada data detail kegiatan (Level 3). @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah (Utama) -->
    <div x-show="modalTambah" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[60] p-4 text-left" style="display: none;" x-transition>
        <div class="bg-white rounded-2xl max-w-2xl w-full shadow-xl p-6" @click.away="modalTambah = false" x-transition.scale>
            <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                <h3 class="text-base font-bold text-gray-800">Tambah Detail Kegiatan Level 3</h3>
                <button type="button" @click="modalTambah = false" class="text-gray-400 hover:text-gray-600 font-bold text-xl">&times;</button>
            </div>
            
            <form action="{{ route('admin.level3.store') }}" method="POST" class="text-sm">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    
                    <!-- Dropdown Tambah Bisa Diketik -->
                    <div class="md:col-span-2 relative z-40" x-data="{
                        openKeg: false,
                        searchKeg: '',
                        selectedKegValue: '',
                        selectedKegLabel: '-- Pilih Kegiatan Induk --',
                        kegiatans: [
                            { value: '', label: '-- Pilih Kegiatan Induk --' },
                            @foreach($kegiatanList as $keg)
                            { value: '{{ $keg->id_kegiatan }}', label: '{{ addslashes($keg->nama_kegiatan . ($keg->output ? ' | ' . $keg->output->nama_output : '')) }}' },
                            @endforeach
                        ],
                        get filteredKegiatans() {
                            if (this.searchKeg === '') return this.kegiatans;
                            return this.kegiatans.filter(k => k.label.toLowerCase().includes(this.searchKeg.toLowerCase()));
                        },
                        selectKeg(opt) {
                            this.selectedKegValue = opt.value;
                            this.selectedKegLabel = opt.label;
                            this.searchKeg = '';
                            this.openKeg = false;
                        }
                    }">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Kegiatan Induk (Level 2)</label>
                        <input type="hidden" name="id_kegiatan" :value="selectedKegValue" required>
                        
                        <button type="button" @click="openKeg = !openKeg" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-sm transition-colors">
                            <span x-text="selectedKegLabel" class="truncate text-gray-700"></span>
                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <div x-show="openKeg" @click.away="openKeg = false" class="absolute w-full mt-1.5 bg-white border border-gray-200 rounded-lg shadow-xl" style="display: none;" x-transition.opacity>
                            <div class="p-2 border-b border-gray-100">
                                <input type="text" x-model="searchKeg" placeholder="Cari Kegiatan..." class="w-full px-3 py-2 border border-gray-200 rounded-md text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" autocomplete="off" @click.stop>
                            </div>
                            <div class="max-h-48 overflow-y-auto p-1.5 space-y-0.5">
                                <template x-for="opt in filteredKegiatans" :key="opt.value">
                                    <div @click="selectKeg(opt)" 
                                         class="px-3 py-2 cursor-pointer text-sm rounded-md transition-colors"
                                         :class="selectedKegValue === opt.value ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-gray-700 hover:bg-gray-100'">
                                        <span x-text="opt.label"></span>
                                    </div>
                                </template>
                                <div x-show="filteredKegiatans.length === 0" class="px-3 py-2 text-sm text-gray-400 italic text-center">Kegiatan tidak ditemukan</div>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Nama Detail Kegiatan (Level 3)</label>
                        <input type="text" name="nama_keg_detail" required class="w-full border border-gray-200 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="Contoh: Pencacahan Lapangan...">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" required class="w-full border border-gray-200 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" required class="w-full border border-gray-200 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
                
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="modalTambah = false" class="bg-gray-100 text-gray-700 px-4 py-2.5 rounded-lg font-bold hover:bg-gray-200">Batal</button>
                    <button type="submit" class="bg-[#10b981] hover:bg-emerald-600 text-white px-4 py-2.5 rounded-lg font-bold shadow">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection