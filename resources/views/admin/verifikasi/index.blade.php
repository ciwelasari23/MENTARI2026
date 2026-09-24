@extends('layouts.admin')

@section('title', 'Verifikasi Laporan Petugas')
@section('header', 'Verifikasi Laporan Lapangan')

@section('content')
<div class="space-y-6" x-data="{ 
    modalOpen: false, 
    currentId: null, 
    currentStatus: 'approved', 
    currentCatatan: '',

    /* --- Modal Lihat Catatan Khusus --- */
    modalCatatanOpen: false,
    activeCatatanTitle: '',
    activeCatatanText: '',

    /* --- Filter Dropdown Pekerjaan & Wilayah --- */
    openFilter: false,
    searchFilter: '',
    selectedFilterId: '',
    selectedFilterLabel: '-- Semua Pekerjaan & Wilayah --',

    /* --- Filter Status Laporan --- */
    selectedStatus: '',

    /* --- Sorting Waktu Pengajuan (newest / oldest) --- */
    sortOrder: 'desc',

    filterData: [
        @foreach($laporans as $item)
        {
            id: '{{ $item->id_laporan }}',
            label: '{{ ($item->targetWilayah->proses->nama_proses ?? "-") . " - " . ($item->targetWilayah->wilayah->nama_kabkota ?? "") }}'
        },
        @endforeach
    ],

    get filteredList() {
        if(this.searchFilter === '') return this.filterData;
        return this.filterData.filter(f => f.label.toLowerCase().includes(this.searchFilter.toLowerCase()));
    },

    sortRows() {
        let tbody = document.getElementById('table-laporan-body');
        if (!tbody) return;
        let rows = Array.from(tbody.querySelectorAll('tr[data-timestamp]'));

        rows.sort((a, b) => {
            let timeA = parseInt(a.getAttribute('data-timestamp'));
            let timeB = parseInt(b.getAttribute('data-timestamp'));
            return this.sortOrder === 'desc' ? timeB - timeA : timeA - timeB;
        });

        rows.forEach((row, index) => {
            tbody.appendChild(row);
            let numCell = row.querySelector('.row-number');
            if(numCell) numCell.textContent = index + 1;
        });
    }
}">
    
    @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-xl text-sm shadow-sm flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button @click="show = false" class="text-emerald-600 font-bold">&times;</button>
        </div>
    @endif

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h3 class="text-base font-bold text-gray-800">Daftar Masuk Laporan Petugas / Mitra</h3>
                <p class="text-xs text-gray-500 mt-0.5">Verifikasi laporan realisasi pekerjaan dari lapangan.</p>
            </div>

            <!-- Bagian Filter & Sorting Controls -->
            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                
                <!-- 1. Filter Berdasarkan Status -->
                <div class="w-full md:w-44">
                    <select x-model="selectedStatus" class="w-full border border-gray-200 rounded-lg p-2.5 text-sm bg-white cursor-pointer shadow-sm focus:outline-none focus:ring-1 focus:ring-[#005A9C]">
                        <option value="">-- Semua Status --</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Disetujui</option>
                        <option value="revision">Perlu Revisi</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>

                <!-- 2. Sorting Berdasarkan Waktu Pengajuan -->
                <div class="w-full md:w-44">
                    <select x-model="sortOrder" @change="sortRows()" class="w-full border border-gray-200 rounded-lg p-2.5 text-sm bg-white cursor-pointer shadow-sm focus:outline-none focus:ring-1 focus:ring-[#005A9C]">
                        <option value="desc">Waktu: Terbaru</option>
                        <option value="asc">Waktu: Terlama</option>
                    </select>
                </div>

                <!-- 3. Dropdown Pencarian Interaktif untuk Filter Pekerjaan & Wilayah -->
                <div class="relative w-full md:w-72" @click.away="openFilter = false">
                    <div @click="openFilter = !openFilter" class="w-full border border-gray-200 rounded-lg p-2.5 text-sm bg-white cursor-pointer flex justify-between items-center shadow-sm">
                        <span x-text="selectedFilterLabel" class="truncate font-medium text-gray-700"></span>
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>

                    <div x-show="openFilter" class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-xl p-2 max-h-60 overflow-y-auto" style="display: none;">
                        <input type="text" x-model="searchFilter" placeholder="Ketik untuk mencari..." class="w-full px-3 py-1.5 border border-gray-200 rounded text-sm mb-2 focus:outline-none focus:ring-1 focus:ring-[#005A9C]">
                        <ul>
                            <li @click="selectedFilterId = ''; selectedFilterLabel = '-- Semua Pekerjaan & Wilayah --'; openFilter = false; searchFilter = ''" 
                                class="px-3 py-2 hover:bg-blue-50 text-sm rounded cursor-pointer text-gray-600 font-medium border-b border-gray-50">
                                -- Tampilkan Semua --
                            </li>
                            <template x-for="f in filteredList" :key="f.id">
                                <li @click="selectedFilterId = f.id; selectedFilterLabel = f.label; openFilter = false; searchFilter = ''" 
                                    class="px-3 py-2 hover:bg-blue-50 text-sm rounded cursor-pointer text-gray-800 flex items-center justify-between border-b border-gray-50">
                                    <span x-text="f.label" class="truncate"></span>
                                    <span x-show="selectedFilterId == f.id" class="text-[#005A9C] font-bold">✓</span>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        <!-- Tabel Data -->
        <div class="overflow-x-auto border border-gray-100 rounded-xl">
            <table class="w-full text-left border-collapse text-sm whitespace-nowrap min-w-[1000px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-600 font-semibold">
                        <th class="p-4 text-center w-16">No</th>
                        <th class="p-4">Pekerjaan & Wilayah</th>
                        <th class="p-4 text-center">Target</th>
                        <th class="p-4 text-center">Realisasi</th>
                        <th class="p-4 text-center">Bukti</th>
                        <th class="p-4">Diajukan Oleh</th>
                        <th class="p-4">Waktu Pengajuan</th>
                        <th class="p-4">Diverifikasi Oleh</th>
                        <th class="p-4">Status & Waktu Diverifikasi</th>
                        <th class="p-4 text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-laporan-body" class="divide-y divide-gray-100 text-gray-700">
                    @forelse($laporans as $index => $item)
                    <tr class="hover:bg-gray-50/50 transition-colors" 
                        x-show="(selectedFilterId === '' || selectedFilterId === '{{ $item->id_laporan }}') && 
                                (selectedStatus === '' || selectedStatus === '{{ $item->status_laporan ?? 'pending' }}')"
                        data-timestamp="{{ strtotime($item->created_at) }}">
                        <td class="p-4 text-center font-medium text-gray-500 row-number">{{ $index + 1 }}</td>
                        
                        <td class="p-4">
                            <span class="font-bold text-gray-800">{{ $item->targetWilayah->proses->nama_proses ?? '-' }}</span><br>
                            <span class="text-xs text-gray-600 font-medium block">{{ $item->targetWilayah->proses->detail->nama_keg_detail ?? '-' }}</span>
                            <span class="text-xs text-gray-400">{{ $item->targetWilayah->wilayah->kode_nama_kabkota ?? '-' }}</span>
                        </td>
                        <!-- Target Daerah -->
                        <td class="p-4 text-center font-semibold text-gray-600">
                            {{ $item->targetWilayah->target_daerah ?? '-' }} {{ $item->targetWilayah->proses->satuan_target ?? '' }}
                        </td>

                        <!-- Realisasi -->
                        <td class="p-4 text-center font-bold text-[#005A9C]">
                            {{ $item->realisasi_saat_ini }} {{ $item->targetWilayah->proses->satuan_target ?? '' }}
                        </td>

                        <!-- Bukti -->
                        <td class="p-4 text-center space-y-1">
                            @if($item->link_bukti)
                                <a href="{{ Str::startsWith($item->link_bukti, 'http') ? $item->link_bukti : 'https://' . $item->link_bukti }}" target="_blank" class="text-blue-600 hover:underline font-medium block">Lihat Tautan</a>
                            @endif
                            @if($item->file_bukti)
                                @php
                                    $fileNameOnly = basename($item->file_bukti);
                                    $fileUrl = route('laporan.file', ['filename' => $fileNameOnly]);
                                @endphp
                                <a href="{{ $fileUrl }}" target="_blank" class="text-emerald-600 hover:underline font-medium block">Lihat File</a>
                            @endif
                            @if(!$item->link_bukti && !$item->file_bukti)
                                <span class="text-gray-400 italic">Tidak ada</span>
                            @endif
                        </td>

                        <!-- Diajukan Oleh -->
                        <td class="p-4 font-medium text-gray-800">
                            {{ $item->pelapor->nama_lengkap ?? ($item->user->nama_lengkap ?? '-') }}
                        </td>

                        <!-- Waktu Pengajuan -->
                        <td class="p-4">
                            <span class="text-gray-700 font-medium block">{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}</span>
                            <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($item->created_at)->format('H:i') }} WIB</span>
                        </td>

                        <!-- Diverifikasi Oleh -->
                        <td class="p-4 font-medium text-gray-800">
                            {{ $item->verifikator->nama_lengkap ?? '-' }}
                        </td>

                        <!-- Status & Waktu Diverifikasi -->
                        <td class="p-4">
                            <div class="flex flex-col items-start gap-1">
                                @php $st = $item->status_laporan ?? 'pending'; @endphp
                                @if($st == 'approved')
                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold px-2.5 py-0.5 rounded-full text-[10px] uppercase">Disetujui</span>
                                    <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($item->updated_at)->format('d/m/Y H:i') }} WIB</span>
                                @elseif($st == 'revision')
                                    <div class="flex items-center gap-1.5">
                                        <span class="bg-blue-50 text-blue-700 border border-blue-200 font-bold px-2.5 py-0.5 rounded-full text-[10px] uppercase">Perlu Revisi</span>
                                        @if($item->catatan_verifikasi)
                                            <button @click="modalCatatanOpen = true; activeCatatanTitle = 'Catatan Perlu Revisi'; activeCatatanText = '{{ addslashes($item->catatan_verifikasi) }}'" 
                                                class="bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold px-2 py-0.5 rounded shadow-xs transition inline-flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                Lihat Catatan
                                            </button>
                                        @endif
                                    </div>
                                    <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($item->updated_at)->format('d/m/Y H:i') }} WIB</span>
                                @elseif($st == 'rejected')
                                    <div class="flex items-center gap-1.5">
                                        <span class="bg-red-50 text-red-700 border border-red-200 font-bold px-2.5 py-0.5 rounded-full text-[10px] uppercase">Ditolak</span>
                                        @if($item->catatan_verifikasi)
                                            <button @click="modalCatatanOpen = true; activeCatatanTitle = 'Catatan Penolakan Laporan'; activeCatatanText = '{{ addslashes($item->catatan_verifikasi) }}'" 
                                                class="bg-red-600 hover:bg-red-700 text-white text-[11px] font-bold px-2 py-0.5 rounded shadow-xs transition inline-flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                Lihat Catatan
                                            </button>
                                        @endif
                                    </div>
                                    <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($item->updated_at)->format('d/m/Y H:i') }} WIB</span>
                                @else
                                    <span class="bg-amber-50 text-amber-700 border border-amber-200 font-bold px-2.5 py-0.5 rounded-full text-[10px] uppercase inline-block mb-1">Pending</span>
                                    <span class="block text-xs italic text-gray-400">Belum diverifikasi</span>
                                @endif
                            </div>
                        </td>

                        <!-- Aksi Verifikasi -->
                        <td class="p-4 text-center">
                            <button @click="modalOpen = true; currentId = '{{ $item->id_laporan }}'; currentStatus = '{{ $item->status_laporan }}'; currentCatatan = '{{ $item->catatan_verifikasi }}';" 
                                class="inline-flex items-center gap-1.5 text-teal-600 hover:text-white bg-teal-50 hover:bg-teal-600 px-3.5 py-2 rounded-lg transition-colors font-bold shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Verifikasi
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="p-8 text-center text-gray-400 italic">
                            Belum ada laporan masuk dari lapangan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Lihat Catatan Revisi / Penolakan -->
    <div x-show="modalCatatanOpen" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4" style="display: none;" x-transition.opacity>
        <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 space-y-4" @click.away="modalCatatanOpen = false" x-transition.scale>
            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span x-text="activeCatatanTitle"></span>
                </h3>
                <button type="button" @click="modalCatatanOpen = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
            </div>
            
            <div class="p-3 bg-gray-50 border border-gray-100 rounded-xl text-gray-700 text-sm italic leading-relaxed">
                <span x-text="activeCatatanText"></span>
            </div>

            <div class="flex justify-end pt-2">
                <button type="button" @click="modalCatatanOpen = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Modal Form Verifikasi Utama -->
    <div x-show="modalOpen" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4" style="display: none;" x-transition.opacity>
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 space-y-4" @click.away="modalOpen = false" x-transition.scale>
            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                <h3 class="text-base font-bold text-gray-800">Proses Verifikasi Laporan</h3>
                <button type="button" @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
            </div>
            
            <form x-bind:action="currentId ? '{{ route('admin.verifikasi.update', ['id' => '__ID__']) }}'.replace('__ID__', currentId) : '#'" method="POST" class="space-y-4 text-sm">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1 text-xs">Status Laporan</label>
                    <select name="status_laporan" x-model="currentStatus" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg outline-none text-sm focus:ring-2 focus:ring-[#005A9C]">
                        <option value="pending">Diajukan (Pending)</option>
                        <option value="approved">Disetujui (Approved)</option>
                        <option value="revision">Perlu Revisi (Revision)</option>
                        <option value="rejected">Ditolak (Rejected)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1 text-xs">Catatan Verifikator</label>
                    <textarea name="catatan_verifikator" x-model="currentCatatan" rows="3" placeholder="Berikan catatan atau alasan revisi/penolakan..." class="w-full px-3 py-2.5 border border-gray-200 rounded-lg outline-none text-sm focus:ring-2 focus:ring-[#005A9C]"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-[#14B8A6] hover:bg-teal-600 text-white rounded-lg font-semibold shadow">Simpan Verifikasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection