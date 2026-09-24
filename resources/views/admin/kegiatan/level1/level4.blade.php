@extends('layouts.admin')

@section('title', 'Level 4: Proses Kegiatan')
@section('header', 'Kelola Proses Kegiatan Level 4')

@section('content')
<div class="space-y-6" x-data="{ modalTambah: false, selected: [], selectAll: false }">

    <div class="flex flex-col md:flex-row gap-4 justify-between items-start md:items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100 relative z-20">
        <h3 class="text-lg font-bold text-gray-800">Daftar Proses Kegiatan</h3>
        
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">

            <form action="{{ route('admin.level4.index') }}" method="GET" class="flex flex-row gap-2 w-full sm:w-auto">
                <div x-data="{
                    open: false,
                    searchQuery: '{{ request('search') }}',
                    items: {{ json_encode($prosesList->pluck('nama_proses')->unique()->values()) }},
                    get filteredItems() {
                        if (this.searchQuery === '') return this.items;
                        return this.items.filter(i => i.toLowerCase().includes(this.searchQuery.toLowerCase()));
                    }
                }" @click.away="open = false" class="relative w-full sm:w-64">
                    
                    <input type="hidden" name="search" x-model="searchQuery">

                    <button type="button" @click="open = !open" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-white flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition-colors">
                        <span x-text="searchQuery || 'Semua Proses Kegiatan'" :class="{'text-gray-400': !searchQuery, 'text-gray-700 font-medium': searchQuery}" class="truncate"></span>
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="open" class="absolute z-50 w-full mt-1.5 bg-white border border-gray-200 rounded-lg shadow-xl p-2" style="display: none;" x-transition.opacity>
                        <div class="p-2 border-b border-gray-100 mb-1">
                            <input type="text" x-model="searchQuery" @keydown.enter.prevent="$el.closest('form').submit()" placeholder="Cari Nama Proses..." class="w-full px-3 py-2 border border-gray-200 rounded-md text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" autocomplete="off">
                        </div>
                        <div class="max-h-60 overflow-y-auto p-1 space-y-0.5">
                            <div @click="searchQuery = ''; open = false; $el.closest('form').submit()" class="px-3 py-2 hover:bg-gray-100 rounded-md cursor-pointer text-sm text-gray-500">-- Tampilkan Semua --</div>
                            <template x-for="item in filteredItems" :key="item">
                                <div @click="searchQuery = item; open = false; $el.closest('form').submit()" 
                                     class="px-3 py-2 hover:bg-blue-50 hover:text-blue-700 rounded-md cursor-pointer text-sm text-gray-700 truncate"
                                     :class="searchQuery === item ? 'bg-blue-50 text-blue-700 font-bold' : ''">
                                    <span x-text="item"></span>
                                </div>
                            </template>
                            <div x-show="filteredItems.length === 0" class="px-3 py-2 text-sm text-gray-400 italic text-center">Proses tidak ditemukan</div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2.5 rounded-lg font-semibold text-sm shadow-sm flex items-center justify-center transition-colors shrink-0" title="Cari">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.level4.index') }}" class="bg-gray-100 text-gray-600 px-3 py-2.5 rounded-lg font-bold text-sm hover:bg-gray-200 flex items-center shrink-0">Reset</a>
                @endif
            </form>

            <!-- Tombol Hapus Terpilih -->
            <form action="{{ route('admin.level4.bulkDestroy') }}" method="POST" x-show="selected.length > 0" onsubmit="return confirm('Yakin ingin menghapus semua data yang dicentang?');" class="w-full sm:w-auto" style="display: none;">
                @csrf @method('DELETE')
                <template x-for="id in selected"><input type="hidden" name="ids[]" :value="id"></template>
                <button type="submit" class="w-full sm:w-auto bg-red-500 hover:bg-red-600 text-white flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold shadow-sm transition">
                    Hapus (<span x-text="selected.length"></span>)
                </button>
            </form>

            <button @click="modalTambah = true" class="w-full sm:w-auto bg-[#10b981] hover:bg-emerald-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold shadow-sm transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Proses
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
                        <th class="py-4 px-4 w-12 text-center"><input type="checkbox" x-model="selectAll" @change="selected = selectAll ? {{ json_encode($prosesList->pluck('id_proses')->map(fn($id) => (string)$id)) }} : []" class="w-4 h-4 text-[#14B8A6] border-gray-300 rounded cursor-pointer"></th>
                        <th class="py-4 px-4 w-16 text-center font-bold">No</th>
                        <th class="py-4 px-4 font-bold">Nama Proses (Level 4)</th>
                        <th class="py-4 px-4 font-bold">Jadwal</th>
                        <th class="py-4 px-4 font-bold">Target Total Provinsi</th>
                        <th class="py-4 px-4 text-center font-bold w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($prosesList as $index => $item)
                    <tr class="hover:bg-gray-50/50 transition-colors group" x-data="{ modalEdit: false, modalDetail: false, modalHapus: false }">
                        <td class="py-4 px-4 text-center">
                            <input type="checkbox" x-model="selected" value="{{ $item->id_proses }}" class="w-4 h-4 text-[#14B8A6] border-gray-300 rounded cursor-pointer">
                        </td>
                        <td class="py-4 px-4 text-center text-gray-400 font-semibold">{{ $index + 1 }}</td>
                        
                        <!-- Nama Proses & Detail Induk (Tanpa Tanda Kurung) -->
                        <td class="py-4 px-4">
                            <div class="flex flex-col justify-center">
                                <span class="font-bold text-gray-800">{{ $item->nama_proses }}</span>
                                @if($item->detail)
                                    <span class="text-gray-500 font-medium text-xs mt-0.5">{{ $item->detail->nama_keg_detail }}</span>
                                @endif
                            </div>
                        </td>

                        <!-- Jadwal -->
                        <td class="py-4 px-4 text-xs text-gray-600 whitespace-nowrap">
                            <span class="bg-gray-50 border border-gray-200 px-2 py-1 rounded-md font-medium">
                                {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }} - 
                                {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}
                            </span>
                        </td>

                        <!-- Target Provinsi -->
                        <td class="py-4 px-4 font-bold text-[#14B8A6]">
                            {{ number_format($item->target_total_provinsi, 0, ',', '.') }} 
                            <span class="text-gray-500 text-xs font-normal ml-1">{{ $item->satuan_target }}</span>
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
                                <div class="bg-white rounded-2xl max-w-lg w-full shadow-xl p-6" @click.away="modalDetail = false" x-transition.scale>
                                    <h3 class="text-base font-bold text-gray-800 mb-4 border-b border-gray-100 pb-3">Informasi Proses Kegiatan Level 4</h3>
                                    <div class="space-y-4 text-sm">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 mb-1">Nama Proses Kegiatan</label>
                                            <div class="bg-gray-50 border border-gray-100 rounded-lg p-3 font-semibold text-gray-800">{{ $item->nama_proses }}</div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 mb-1">Detail Induk (Level 3)</label>
                                            <div class="bg-gray-50 border border-gray-100 rounded-lg p-3 font-semibold text-gray-800">{{ $item->detail->nama_keg_detail ?? 'Tidak ada data' }}</div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-bold text-gray-500 mb-1">Tanggal Mulai</label>
                                                <div class="bg-gray-50 border border-gray-100 rounded-lg p-2.5 font-medium text-gray-800">{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d F Y') }}</div>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-500 mb-1">Tanggal Selesai</label>
                                                <div class="bg-gray-50 border border-gray-100 rounded-lg p-2.5 font-medium text-gray-800">{{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d F Y') }}</div>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 mb-1">Target Total Provinsi (Dibagikan ke Kabupaten/Kota)</label>
                                            <div class="bg-gray-50 border border-gray-100 rounded-lg p-3 font-bold text-[#14B8A6] text-base">
                                                {{ number_format($item->target_total_provinsi, 0, ',', '.') }} {{ $item->satuan_target }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex justify-end mt-6">
                                        <button type="button" @click="modalDetail = false" class="bg-gray-100 text-gray-700 px-4 py-2.5 rounded-lg font-bold hover:bg-gray-200">Tutup</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Edit -->
                            <div x-show="modalEdit" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[60] p-4 text-left" style="display: none;" x-transition>
                                <div class="bg-white rounded-2xl max-w-lg w-full shadow-xl p-6" @click.away="modalEdit = false" x-transition.scale>
                                    <h3 class="text-base font-bold text-gray-800 mb-4 border-b border-gray-100 pb-3">Edit Proses Kegiatan Level 4</h3>
                                    <form action="{{ route('admin.level4.update', $item->id_proses) }}" method="POST" class="space-y-4 text-sm">
                                        @csrf @method('PUT')
                                        
                                        <!-- Dropdown Edit Detail Induk -->
                                        <div class="relative" x-data="{ 
                                            openEdit: false, 
                                            searchEdit: '', 
                                            selectedEditId: '{{ $item->id_keg_detail }}',
                                            selectedEditLabel: '{{ addslashes($item->detail->nama_keg_detail ?? '-- Pilih Detail Level 3 --') }}',
                                            detailsData: {{ json_encode($details->map(function($d) { return ['id' => $d->id_keg_detail, 'label' => $d->nama_keg_detail]; })) }},
                                            get filteredEdit() {
                                                if (this.searchEdit === '') return this.detailsData;
                                                return this.detailsData.filter(d => d.label.toLowerCase().includes(this.searchEdit.toLowerCase()));
                                            }
                                        }" @click.away="openEdit = false">
                                            <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Detail Kegiatan Induk (Level 3)</label>
                                            <input type="hidden" name="id_keg_detail" x-model="selectedEditId" required>
                                            
                                            <button type="button" @click="openEdit = !openEdit" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-amber-500 shadow-sm">
                                                <span x-text="selectedEditLabel" class="truncate text-gray-700 font-medium"></span>
                                                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </button>

                                            <div x-show="openEdit" class="absolute z-50 mt-1.5 w-full bg-white border border-gray-200 rounded-lg shadow-xl p-2" style="display: none;" x-transition.opacity>
                                                <input type="text" x-model="searchEdit" placeholder="Cari detail induk..." class="w-full px-3 py-2 border border-gray-200 rounded-md text-sm mb-2 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500" autocomplete="off" @click.stop>
                                                <div class="max-h-48 overflow-y-auto space-y-0.5">
                                                    <template x-for="det in filteredEdit" :key="det.id">
                                                        <div @click="selectedEditId = det.id; selectedEditLabel = det.label; openEdit = false; searchEdit = ''" 
                                                             class="px-3 py-2 hover:bg-amber-50 hover:text-amber-700 rounded-md cursor-pointer text-sm text-gray-700 flex items-center justify-between"
                                                             :class="selectedEditId == det.id ? 'bg-amber-50 text-amber-700 font-bold' : ''">
                                                            <span x-text="det.label" class="truncate"></span>
                                                            <span x-show="selectedEditId == det.id" class="text-amber-600 font-bold">✓</span>
                                                        </div>
                                                    </template>
                                                    <div x-show="filteredEdit.length === 0" class="px-3 py-2 text-sm text-gray-400 italic text-center">Detail tidak ditemukan</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1">Nama Proses Kegiatan (Level 4)</label>
                                            <input type="text" name="nama_proses" value="{{ $item->nama_proses }}" required class="w-full border border-gray-200 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-amber-500">
                                        </div>

                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Mulai</label>
                                                <input type="date" name="tanggal_mulai" value="{{ $item->tanggal_mulai }}" required class="w-full border border-gray-200 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-amber-500">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Selesai</label>
                                                <input type="date" name="tanggal_selesai" value="{{ $item->tanggal_selesai }}" required class="w-full border border-gray-200 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-amber-500">
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-bold text-gray-700 mb-1">Target Total Provinsi</label>
                                                <input type="number" name="target_total_provinsi" min="1" value="{{ $item->target_total_provinsi }}" required class="w-full border border-gray-200 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-amber-500">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-700 mb-1">Satuan Target</label>
                                                <select name="satuan_target" required class="w-full border border-gray-200 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                                                    <option value="Dokumen" {{ $item->satuan_target == 'Dokumen' ? 'selected' : '' }}>Dokumen</option>
                                                    <option value="Laporan" {{ $item->satuan_target == 'Laporan' ? 'selected' : '' }}>Laporan</option>
                                                    <option value="Desa / Kelurahan" {{ $item->satuan_target == 'Desa / Kelurahan' ? 'selected' : '' }}>Desa / Kelurahan</option>
                                                    <option value="Kecamatan" {{ $item->satuan_target == 'Kecamatan' ? 'selected' : '' }}>Kecamatan</option>
                                                    <option value="Kabupaten / Kota" {{ $item->satuan_target == 'Kabupaten / Kota' ? 'selected' : '' }}>Kabupaten / Kota</option>
                                                    <option value="Perusahaan / Usaha" {{ $item->satuan_target == 'Perusahaan / Usaha' ? 'selected' : '' }}>Perusahaan / Usaha</option>
                                                    <option value="Rumah Tangga" {{ $item->satuan_target == 'Rumah Tangga' ? 'selected' : '' }}>Rumah Tangga (KK)</option>
                                                    <option value="Responden" {{ $item->satuan_target == 'Responden' ? 'selected' : '' }}>Responden</option>
                                                    <option value="Kegiatan" {{ $item->satuan_target == 'Kegiatan' ? 'selected' : '' }}>Kegiatan</option>
                                                    <option value="Rekomendasi" {{ $item->satuan_target == 'Rekomendasi' ? 'selected' : '' }}>Rekomendasi</option>
                                                </select>
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
                                    <p class="text-sm text-gray-600 mb-6">Apakah Anda yakin ingin menghapus data ini?</p>
                                    <form action="{{ route('admin.level4.destroy', $item->id_proses) }}" method="POST" class="flex justify-center gap-2">
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
                            @if(request('search')) Pencarian tidak ditemukan. @else Belum ada data proses kegiatan (Level 4). @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah (Utama) -->
    <div x-show="modalTambah" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[60] p-4 text-left" style="display: none;" x-transition>
        <div class="bg-white rounded-2xl max-w-lg w-full shadow-xl p-6" @click.away="modalTambah = false" x-transition.scale>
            <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                <h3 class="text-base font-bold text-gray-800">Tambah Proses Kegiatan Level 4</h3>
                <button type="button" @click="modalTambah = false" class="text-gray-400 hover:text-gray-600 font-bold text-xl">&times;</button>
            </div>
            
            <form action="{{ route('admin.level4.store') }}" method="POST" class="space-y-4 text-sm">
                @csrf
                
                <!-- Dropdown Tambah Detail Induk -->
                <div class="relative" x-data="{ 
                    openCreate: false, 
                    searchCreate: '', 
                    selectedCreateId: '',
                    selectedCreateLabel: '-- Pilih Detail Level 3 --',
                    detailsData: {{ json_encode($details->map(function($d) { return ['id' => $d->id_keg_detail, 'label' => $d->nama_keg_detail]; })) }},
                    get filteredCreate() {
                        if (this.searchCreate === '') return this.detailsData;
                        return this.detailsData.filter(d => d.label.toLowerCase().includes(this.searchCreate.toLowerCase()));
                    }
                }" @click.away="openCreate = false">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Detail Kegiatan Induk (Level 3)</label>
                    <input type="hidden" name="id_keg_detail" x-model="selectedCreateId" required>
                    
                    <button type="button" @click="openCreate = !openCreate" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-sm">
                        <span x-text="selectedCreateLabel" class="truncate text-gray-700 font-medium"></span>
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="openCreate" class="absolute z-50 mt-1.5 w-full bg-white border border-gray-200 rounded-lg shadow-xl p-2" style="display: none;" x-transition.opacity>
                        <input type="text" x-model="searchCreate" placeholder="Cari detail induk..." class="w-full px-3 py-2 border border-gray-200 rounded-md text-sm mb-2 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" autocomplete="off" @click.stop>
                        <div class="max-h-48 overflow-y-auto space-y-0.5">
                            <template x-for="det in filteredCreate" :key="det.id">
                                <div @click="selectedCreateId = det.id; selectedCreateLabel = det.label; openCreate = false; searchCreate = ''" 
                                     class="px-3 py-2 hover:bg-emerald-50 hover:text-emerald-700 rounded-md cursor-pointer text-sm text-gray-700 flex items-center justify-between"
                                     :class="selectedCreateId == det.id ? 'bg-emerald-50 text-emerald-700 font-bold' : ''">
                                    <span x-text="det.label" class="truncate"></span>
                                    <span x-show="selectedCreateId == det.id" class="text-emerald-600 font-bold">✓</span>
                                </div>
                            </template>
                            <div x-show="filteredCreate.length === 0" class="px-3 py-2 text-sm text-gray-400 italic text-center">Detail tidak ditemukan</div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Nama Proses Kegiatan (Level 4)</label>
                    <input type="text" name="nama_proses" required placeholder="Contoh: Pencacahan Lapangan..." class="w-full border border-gray-200 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" required class="w-full border border-gray-200 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" required class="w-full border border-gray-200 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Target Total Provinsi</label>
                        <input type="number" name="target_total_provinsi" min="1" required placeholder="Jumlah total..." class="w-full border border-gray-200 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Satuan Target</label>
                        <select name="satuan_target" required class="w-full border border-gray-200 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                            <option value="">-- Pilih Satuan --</option>
                            <option value="Dokumen">Dokumen</option>
                            <option value="Laporan">Laporan</option>
                            <option value="Desa / Kelurahan">Desa / Kelurahan</option>
                            <option value="Kecamatan">Kecamatan</option>
                            <option value="Kabupaten / Kota">Kabupaten / Kota</option>
                            <option value="Perusahaan / Usaha">Perusahaan / Usaha</option>
                            <option value="Rumah Tangga">Rumah Tangga (KK)</option>
                            <option value="Responden">Responden</option>
                            <option value="Kegiatan">Kegiatan</option>
                            <option value="Rekomendasi">Rekomendasi</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="modalTambah = false" class="bg-gray-100 text-gray-700 px-4 py-2.5 rounded-lg font-bold hover:bg-gray-200">Batal</button>
                    <button type="submit" class="bg-[#10b981] hover:bg-emerald-600 text-white px-4 py-2.5 rounded-lg font-bold shadow">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection