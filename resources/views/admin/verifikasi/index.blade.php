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

    /* --- Modal Riwayat Log Laporan --- */
    modalRiwayatOpen: false,
    activeRiwayatList: [],
    activeRiwayatTitle: '',

    /* --- Modal Nilai / Selesai --- */
    modalNilaiOpen: false,
    targetIdNilai: null,
    skorManualNilai: '',

    /* --- Modal Alert Custom di Tengah --- */
    customAlertOpen: false,
    customAlertTitle: '',
    customAlertMessage: '',
    customAlertAction: null,

    /* --- Filter Dropdown Pekerjaan & Wilayah --- */
    openFilter: false,
    searchFilter: '',
    selectedFilterId: '',
    selectedFilterLabel: 'Semua Pekerjaan & Wilayah',

    /* --- Filter Status Laporan --- */
    selectedStatus: '',

    /* --- Sorting Waktu Pengajuan --- */
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
    },

    submitVerifikasi(event) {
        event.preventDefault();

        if ((this.currentStatus === 'revision' || this.currentStatus === 'rejected') && !this.currentCatatan.trim()) {
            this.showCustomAlert('Perhatian', 'Harap berikan catatan atau alasan untuk status Perlu Revisi atau Ditolak.', null);
            return;
        }

        if (this.currentStatus === 'approved') {
            this.modalOpen = false; 

            this.showCustomAlert(
                'Konfirmasi Persetujuan', 
                'Laporan berhasil disetujui. Apakah Anda ingin langsung menandai kegiatan ini sebagai selesai dan melakukan penilaian/rating?', 
                () => {
                    let form = document.getElementById('form-verifikasi');
                    let formData = new FormData(form);
                    
                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            this.targetIdNilai = this.currentId;
                            this.skorManualNilai = '';
                            this.modalNilaiOpen = true;
                        }
                    }).catch(() => {
                        form.submit();
                    });
                }
            );
        } else {
            let pesan = this.currentStatus === 'revision' 
                ? 'Status laporan diubah menjadi Perlu Revisi beserta catatan.' 
                : 'Laporan telah ditolak beserta alasannya.';
            
            this.showCustomAlert('Informasi Verifikasi', pesan, () => {
                this.modalOpen = false;
                document.getElementById('form-verifikasi').submit();
            });
        }
    },

    showCustomAlert(title, message, callback) {
        this.customAlertTitle = title;
        this.customAlertMessage = message;
        this.customAlertAction = callback;
        this.customAlertOpen = true;
    },

    handleAlertConfirm() {
        this.customAlertOpen = false;
        if (this.customAlertAction) {
            this.customAlertAction();
        }
    }
}">
    
    @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-xs shadow-sm flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button @click="show = false" class="text-emerald-600 font-bold">&times;</button>
        </div>
    @endif

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                <h3 class="text-base font-bold text-gray-800">Daftar Masuk Laporan Petugas / Mitra</h3>
                <p class="text-xs text-gray-500 mt-0.5">Verifikasi laporan realisasi pekerjaan dari lapangan.</p>
            </div>

            <!-- Bagian Filter & Sorting Controls -->
            <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                <div class="w-auto">
                    <select x-model="selectedStatus" class="border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs bg-white cursor-pointer shadow-sm focus:outline-none focus:ring-1 focus:ring-[#005A9C] font-medium text-gray-700">
                        <option value="">Status: Semua</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Disetujui</option>
                        <option value="revision">Perlu Revisi</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>

                <div class="w-auto">
                    <select x-model="sortOrder" @change="sortRows()" class="border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs bg-white cursor-pointer shadow-sm focus:outline-none focus:ring-1 focus:ring-[#005A9C] font-medium text-gray-700">
                        <option value="desc">Waktu: Terbaru</option>
                        <option value="asc">Waktu: Terlama</option>
                    </select>
                </div>

                <div class="relative w-auto min-w-[240px]" @click.away="openFilter = false">
                    <div @click="openFilter = !openFilter" class="border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs bg-white cursor-pointer flex justify-between items-center gap-2 shadow-sm">
                        <span x-text="selectedFilterLabel" class="truncate font-medium text-gray-700"></span>
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>

                    <div x-show="openFilter" class="absolute right-0 z-40 mt-1.5 w-72 bg-white border border-gray-200 rounded-xl shadow-xl p-2.5 max-h-60 overflow-y-auto" style="display: none;">
                        <input type="text" x-model="searchFilter" placeholder="Ketik cari pekerjaan/wilayah..." class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs mb-2 focus:outline-none focus:ring-1 focus:ring-[#005A9C]">
                        <ul>
                            <li @click="selectedFilterId = ''; selectedFilterLabel = 'Semua Pekerjaan & Wilayah'; openFilter = false; searchFilter = ''" 
                                class="px-3 py-2 hover:bg-blue-50 text-xs rounded-lg cursor-pointer text-gray-600 font-medium border-b border-gray-50">
                                -- Tampilkan Semua --
                            </li>
                            <template x-for="f in filteredList" :key="f.id">
                                <li @click="selectedFilterId = f.id; selectedFilterLabel = f.label; openFilter = false; searchFilter = ''" 
                                    class="px-3 py-2 hover:bg-blue-50 text-xs rounded-lg cursor-pointer text-gray-800 flex items-center justify-between border-b border-gray-50">
                                    <span x-text="f.label" class="truncate pr-2"></span>
                                    <span x-show="selectedFilterId == f.id" class="text-[#005A9C] font-bold shrink-0">✓</span>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Baris Entries per Page -->
        <form id="form-entries" action="{{ route('admin.verifikasi.index') }}" method="GET" class="flex items-center gap-2 text-xs text-gray-600 pt-1">
            @foreach(request()->except(['page', 'perPage', '_token']) as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
            <select name="perPage" onchange="document.getElementById('form-entries').submit()" class="border border-gray-200 rounded-lg px-2.5 py-1.5 bg-white text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                <option value="10" {{ request('perPage', 10) == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('perPage') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('perPage') == 100 ? 'selected' : '' }}>100</option>
            </select>
            <span>entries per page</span>
        </form>

        <!-- Tabel Data -->
        <div class="overflow-x-auto border border-gray-100 rounded-xl">
            <table class="w-full text-left border-collapse whitespace-nowrap min-w-[1150px]">
                <thead>
                    <tr class="bg-gray-50/70 border-b border-gray-100 text-gray-500 text-[11px] uppercase tracking-wider font-semibold">
                        <th class="py-3.5 px-4 text-center w-12 font-bold">No</th>
                        <th class="py-3.5 px-4 font-bold">Pekerjaan & Wilayah</th>
                        <th class="py-3.5 px-4 text-center font-bold">Target</th>
                        <th class="py-3.5 px-4 text-center font-bold">Realisasi</th>
                        <th class="py-3.5 px-4 text-center font-bold">Bukti</th>
                        <th class="py-3.5 px-4 text-center font-bold">Diajukan Oleh</th>
                        <th class="py-3.5 px-4 text-center font-bold">Waktu Pengajuan</th>
                        <th class="py-3.5 px-4 text-center font-bold">Diverifikasi Oleh</th>
                        <th class="py-3.5 px-4 text-center font-bold">Penilaian</th>
                        <th class="py-3.5 px-4 text-center font-bold">Status Verifikasi</th>
                        <th class="py-3.5 px-4 text-center font-bold">Waktu Verifikasi</th>
                        <th class="py-3.5 px-4 text-center font-bold w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-laporan-body" class="divide-y divide-gray-100 text-sm text-gray-700">
                    @forelse($laporans as $index => $item)
                    @php
                        $groupKey = $item->id_target_wilayah . '-' . ($item->id_user_pelapor ?? 0);
                        $riwayatGroup = $allLaporans[$groupKey] ?? collect([$item]);
                        $jumlahRiwayat = $riwayatGroup->count();
                    @endphp
                    <tr class="hover:bg-gray-50/50 transition-colors" 
                        x-show="(selectedFilterId === '' || selectedFilterId === '{{ $item->id_laporan }}') && 
                                (selectedStatus === '' || selectedStatus === '{{ $item->status_laporan ?? 'pending' }}')"
                        data-timestamp="{{ strtotime($item->created_at) }}">
                        <td class="py-3.5 px-4 text-center font-medium text-gray-400 text-xs row-number">
                            {{ method_exists($laporans, 'firstItem') ? $laporans->firstItem() + $index : $index + 1 }}
                        </td>
                        
                        <!-- Pekerjaan & Wilayah -->
                        <td class="py-3.5 px-4">
                            <span class="font-bold text-gray-800 text-sm">{{ $item->targetWilayah->proses->nama_proses ?? '-' }}</span><br>
                            <span class="text-xs text-gray-600 font-medium block mt-0.5">{{ $item->targetWilayah->proses->detail->nama_keg_detail ?? '-' }}</span>
                            <span class="text-xs text-gray-400">{{ $item->targetWilayah->wilayah->kode_nama_kabkota ?? '-' }}</span>
                        </td>

                        <!-- Target -->
                        <td class="py-3.5 px-4 text-center font-semibold text-gray-600 text-sm">
                            {{ $item->targetWilayah->target_daerah ?? '-' }} {{ $item->targetWilayah->proses->satuan_target ?? '' }}
                        </td>

                        <!-- Realisasi -->
                        <td class="py-3.5 px-4 text-center font-bold text-[#005A9C] text-sm">
                            {{ $item->realisasi_saat_ini }} {{ $item->targetWilayah->proses->satuan_target ?? '' }}
                        </td>

                        <!-- Bukti -->
                        <td class="py-3.5 px-4 text-center space-y-1 text-xs">
                            @if($item->link_bukti)
                                <a href="{{ Str::startsWith($item->link_bukti, 'http') ? $item->link_bukti : 'https://' . $item->link_bukti }}" target="_blank" class="text-blue-600 hover:underline font-medium block">Tautan</a>
                            @endif
                            @if($item->file_bukti)
                                @php
                                    $fileNameOnly = basename($item->file_bukti);
                                    $fileUrl = route('laporan.file', ['filename' => $fileNameOnly]);
                                @endphp
                                <a href="{{ $fileUrl }}" target="_blank" class="text-emerald-600 hover:underline font-medium block">File</a>
                            @endif
                            @if(!$item->link_bukti && !$item->file_bukti)
                                <span class="text-gray-400 italic">Tidak ada</span>
                            @endif
                        </td>

                        <!-- Diajukan Oleh + Tombol Riwayat -->
                        <td class="py-3.5 px-4 text-center font-medium text-gray-800 text-sm">
                            <div>{{ $item->pelapor->nama_lengkap ?? ($item->user->nama_lengkap ?? '-') }}</div>
                            @if($jumlahRiwayat > 0)
                                <button @click="
                                    activeRiwayatTitle = 'Riwayat Pengajuan: {{ addslashes($item->targetWilayah->proses->nama_proses ?? '-') }} ({{ addslashes($item->targetWilayah->wilayah->kode_nama_kabkota ?? '-') }})';
                                    activeRiwayatList = [
                                        @foreach($riwayatGroup as $rev)
                                        {
                                            tanggal: '{{ \Carbon\Carbon::parse($rev->created_at)->format('d/m/Y H:i') }} WIB',
                                            realisasi: '{{ $rev->realisasi_saat_ini }}',
                                            status: '{{ strtoupper($rev->status_laporan ?? 'pending') }}',
                                            catatan: '{{ addslashes($rev->catatan_verifikasi ?? ($rev->catatan_verifikator ?? '-')) }}'
                                        },
                                        @endforeach
                                    ];
                                    modalRiwayatOpen = true;
                                " class="mt-1.5 inline-flex items-center gap-1 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 px-2.5 py-1 rounded-lg text-xs font-bold shadow-2xs transition">
                                    📜 Riwayat ({{ $jumlahRiwayat }})
                                </button>
                            @endif
                        </td>

                        <!-- Waktu Pengajuan -->
                        <td class="py-3.5 px-4 text-center text-xs">
                            <span class="text-gray-700 font-medium block">{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}</span>
                            <span class="text-gray-500">{{ \Carbon\Carbon::parse($item->created_at)->format('H:i') }} WIB</span>
                        </td>

                        <!-- Diverifikasi Oleh -->
                        <td class="py-3.5 px-4 text-center font-medium text-gray-800 text-sm">
                            {{ $item->verifikator->nama_lengkap ?? '-' }}
                        </td>

                        <!-- Penilaian (Kembali Bersih Menampilkan Skor & Tombol Edit) -->
                        <td class="py-3.5 px-4 text-center">
                            @php
                                $skorNilai = $item->skor_manual ?? ($item->targetWilayah->skor_manual ?? null);
                            @endphp
                            @if($skorNilai !== null)
                                <div class="flex flex-col items-center justify-center gap-1">
                                    <span class="font-bold text-gray-800 text-sm">
                                        {{ $skorNilai }} / 100
                                    </span>
                                    <button @click="targetIdNilai = '{{ $item->id_laporan }}'; skorManualNilai = '{{ $skorNilai }}'; modalNilaiOpen = true;" 
                                        class="text-gray-400 hover:text-blue-600 transition-colors inline-flex items-center gap-1 text-xs" title="Edit Nilai">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        Edit
                                    </button>
                                </div>
                            @else
                                <span class="text-gray-400 italic text-xs">- Belum dinilai -</span>
                            @endif
                        </td>

                        <!-- Status Verifikasi (Dengan Centang Hijau Tanda Disetujui / Selesai) -->
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex flex-col items-center justify-center gap-1.5">
                                @php $st = $item->status_laporan ?? 'pending'; @endphp
                                @if($st == 'approved')
                                    <div class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-1 rounded-lg font-bold text-xs uppercase tracking-wide">
                                        <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Disetujui / Selesai
                                    </div>
                                @elseif($st == 'revision')
                                    <div class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 border border-blue-200 px-3 py-1 rounded-lg font-bold text-xs uppercase tracking-wide">
                                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                        Perlu Revisi
                                    </div>
                                    @if($item->catatan_verifikasi || $item->catatan_verifikator)
                                        <button @click="modalCatatanOpen = true; activeCatatanTitle = 'Catatan Perlu Revisi'; activeCatatanText = '{{ addslashes($item->catatan_verifikasi ?? $item->catatan_verifikator) }}'" 
                                            class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold px-2.5 py-1 rounded-lg shadow-2xs transition inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Catatan
                                        </button>
                                    @endif
                                @elseif($st == 'rejected')
                                    <div class="inline-flex items-center gap-1.5 bg-rose-50 text-rose-700 border border-rose-200 px-3 py-1 rounded-lg font-bold text-xs uppercase tracking-wide">
                                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                        Ditolak
                                    </div>
                                    @if($item->catatan_verifikasi || $item->catatan_verifikator)
                                        <button @click="modalCatatanOpen = true; activeCatatanTitle = 'Catatan Penolakan Laporan'; activeCatatanText = '{{ addslashes($item->catatan_verifikasi ?? $item->catatan_verifikator) }}'" 
                                            class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold px-2.5 py-1 rounded-lg shadow-2xs transition inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Catatan
                                        </button>
                                    @endif
                                @else
                                    <div class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 border border-amber-200 px-3 py-1 rounded-lg font-bold text-xs uppercase tracking-wide">
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                        Pending
                                    </div>
                                    <span class="block text-xs italic text-gray-400">Belum diverifikasi</span>
                                @endif
                            </div>
                        </td>

                        <!-- Waktu Verifikasi -->
                        <td class="py-3.5 px-4 text-center text-xs">
                            @php $st = $item->status_laporan ?? 'pending'; @endphp
                            @if($st != 'pending')
                                <span class="text-gray-700 font-medium block">{{ \Carbon\Carbon::parse($item->updated_at)->format('d/m/Y') }}</span>
                                <span class="text-gray-500">{{ \Carbon\Carbon::parse($item->updated_at)->format('H:i') }} WIB</span>
                            @else
                                <span class="text-gray-400 italic">-</span>
                            @endif
                        </td>

                        <!-- Aksi -->
                        <td class="py-3.5 px-4 text-center">
                            <button @click="modalOpen = true; currentId = '{{ $item->id_laporan }}'; currentStatus = '{{ $item->status_laporan }}'; currentCatatan = '{{ $item->catatan_verifikasi ?? $item->catatan_verifikator }}';" 
                                class="inline-flex items-center gap-1.5 text-teal-600 hover:text-white bg-teal-50 hover:bg-teal-600 px-3.5 py-2 rounded-xl transition-colors font-bold text-xs shadow-sm">
                                Verifikasi
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="py-8 px-4 text-center text-gray-400 italic text-xs">
                            Belum ada laporan masuk dari lapangan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Bagian Footer Tabel (Showing entries & Navigasi Halaman Dinamis) -->
        @if(method_exists($laporans, 'links'))
        <div class="flex flex-col sm:flex-row justify-between items-center text-xs text-gray-500 pt-3 gap-3">
            <div>
                Menampilkan <span class="font-semibold text-gray-700">{{ $laporans->firstItem() ?? 0 }}</span> ke <span class="font-semibold text-gray-700">{{ $laporans->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-gray-700">{{ number_format($laporans->total(), 0, ',', '.') }}</span> entri
            </div>
            
            <div class="flex items-center gap-1.5">
                {{-- Tombol Previous --}}
                @if ($laporans->onFirstPage())
                    <span class="px-3.5 py-2 border border-gray-200 rounded-xl bg-gray-50 text-gray-300 cursor-not-allowed flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </span>
                @else
                    <a href="{{ $laporans->previousPageUrl() }}" class="px-3.5 py-2 border border-gray-300 rounded-xl bg-white text-gray-700 hover:bg-gray-50 flex items-center justify-center transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </a>
                @endif

                {{-- Kotak Angka Halaman Aktif --}}
                <span class="px-4 py-2 border border-emerald-500 bg-emerald-500 text-white font-bold rounded-xl text-xs shadow-sm">
                    {{ $laporans->currentPage() }}
                </span>

                {{-- Tombol Next --}}
                @if ($laporans->hasMorePages())
                    <a href="{{ $laporans->nextPageUrl() }}" class="px-3.5 py-2 border border-gray-300 rounded-xl bg-white text-gray-700 hover:bg-gray-50 flex items-center justify-center transition shadow-sm">
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

    <!-- Modal Lihat Riwayat Pengajuan Log Lengkap -->
    <div x-show="modalRiwayatOpen" class="fixed inset-0 bg-black/50 backdrop-blur-xs flex items-center justify-center z-50 p-4" style="display: none;" x-transition.opacity>
        <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 space-y-4" @click.away="modalRiwayatOpen = false" x-transition.scale>
            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                <h3 class="text-sm font-bold text-gray-800 flex items-center gap-1.5">
                    📜 <span x-text="activeRiwayatTitle"></span>
                </h3>
                <button type="button" @click="modalRiwayatOpen = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold">&times;</button>
            </div>
            
            <div class="max-h-72 overflow-y-auto space-y-2 pr-1 text-xs">
                <template x-for="(rev, idx) in activeRiwayatList" :key="idx">
                    <div class="p-3 bg-gray-50 border border-gray-100 rounded-xl space-y-1">
                        <div class="flex justify-between items-center font-bold text-gray-700">
                            <span x-text="'Pengajuan #' + (activeRiwayatList.length - idx)"></span>
                            <span x-text="rev.tanggal" class="text-[10px] text-gray-400 font-normal"></span>
                        </div>
                        <div class="text-gray-600">
                            Realisasi: <span class="font-bold text-[#005A9C]" x-text="rev.realisasi"></span> | 
                            Status: <span class="font-bold uppercase" x-text="rev.status"></span>
                        </div>
                        <div class="text-gray-500 italic pt-0.5" x-show="rev.catatan && rev.catatan !== '-'">
                            Catatan: <span x-text="rev.catatan"></span>
                        </div>
                    </div>
                </template>
            </div>

            <div class="flex justify-end pt-3 border-t border-gray-100">
                <button type="button" @click="modalRiwayatOpen = false" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Modal Lihat Catatan Revisi / Penolakan -->
    <div x-show="modalCatatanOpen" class="fixed inset-0 bg-black/50 backdrop-blur-xs flex items-center justify-center z-50 p-4" style="display: none;" x-transition.opacity>
        <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 space-y-4" @click.away="modalCatatanOpen = false" x-transition.scale>
            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                <h3 class="text-sm font-bold text-gray-800 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span x-text="activeCatatanTitle"></span>
                </h3>
                <button type="button" @click="modalCatatanOpen = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold">&times;</button>
            </div>
            
            <div class="p-3.5 bg-gray-50 border border-gray-100 rounded-xl text-gray-700 text-xs italic leading-relaxed">
                <span x-text="activeCatatanText"></span>
            </div>

            <div class="flex justify-end pt-2">
                <button type="button" @click="modalCatatanOpen = false" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Modal Form Verifikasi Utama -->
    <div x-show="modalOpen" class="fixed inset-0 bg-black/50 backdrop-blur-xs flex items-center justify-center z-40 p-4" style="display: none;" x-transition.opacity>
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 space-y-4" @click.away="modalOpen = false" x-transition.scale>
            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                <h3 class="text-sm font-bold text-gray-800">Proses Verifikasi Laporan</h3>
                <button type="button" @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold">&times;</button>
            </div>
            
            <form id="form-verifikasi" x-bind:action="currentId ? '{{ route('admin.verifikasi.update', ['id' => '__ID__']) }}'.replace('__ID__', currentId) : '#'" method="POST" @submit="submitVerifikasi($event)" class="space-y-4 text-xs">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1 text-[10px]">Status Laporan</label>
                    <select name="status_laporan" x-model="currentStatus" required class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl outline-none text-xs focus:ring-2 focus:ring-[#005A9C]">
                        <option value="pending">Diajukan (Pending)</option>
                        <option value="approved">Disetujui (Approved)</option>
                        <option value="revision">Perlu Revisi (Revision)</option>
                        <option value="rejected">Ditolak (Rejected)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1 text-[10px]">
                        Catatan Verifikator 
                        <span x-show="currentStatus === 'revision' || currentStatus === 'rejected'" class="text-red-500">*Wajib diisi</span>
                    </label>
                    <textarea name="catatan_verifikator" x-model="currentCatatan" @keydown.enter.stop rows="3" placeholder="Berikan catatan atau alasan revisi/penolakan..." class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl outline-none text-xs focus:ring-2 focus:ring-[#005A9C]"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-bold">Batal</button>
                    <button type="submit" class="px-4 py-2.5 bg-[#14B8A6] hover:bg-teal-600 text-white rounded-xl font-bold shadow-2xs">Simpan Verifikasi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Penilaian & Selesai -->
    <div x-show="modalNilaiOpen" class="fixed inset-0 bg-black/50 backdrop-blur-xs flex items-center justify-center z-50 p-4" style="display: none;" x-transition.opacity>
        <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 space-y-4" @click.away="modalNilaiOpen = false" x-transition.scale>
            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                <h4 class="font-bold text-gray-800 text-sm">Penilaian / Rating Kegiatan Selesai</h4>
                <button @click="modalNilaiOpen = false" class="text-gray-400 hover:text-gray-600 font-bold text-lg">&times;</button>
            </div>
            
            <form x-bind:action="targetIdNilai ? '{{ route('evaluasi.update-skor', ['id' => '__ID__']) }}'.replace('__ID__', targetIdNilai) : '#'" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')
                <div class="space-y-2">
                    <p class="text-gray-500 leading-relaxed">Silakan masukkan skor penilaian (0 - 100) untuk rekapitulasi evaluasi kegiatan.</p>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-700 mb-1 uppercase tracking-wider">Skor Penilaian (0 - 100)</label>
                        <input type="number" name="skor_manual" x-model="skorManualNilai" min="0" max="100" required class="w-full border border-gray-200 rounded-xl p-3 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none font-bold text-blue-600 text-center bg-gray-50" placeholder="Contoh: 85">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" @click="modalNilaiOpen = false" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-bold">Batal</button>
                    <button type="submit" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-2xs">Simpan Nilai</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Alert Custom di Tengah -->
    <div x-show="customAlertOpen" class="fixed inset-0 bg-black/60 backdrop-blur-xs flex items-center justify-center z-[70] p-4" style="display: none;" x-transition.opacity>
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 space-y-4 text-center" @click.away="customAlertOpen = false" x-transition.scale>
            <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mx-auto text-xl font-bold">
                ℹ
            </div>
            <div class="space-y-1">
                <h4 class="font-bold text-gray-800 text-sm" x-text="customAlertTitle"></h4>
                <p class="text-xs text-gray-500 leading-relaxed" x-text="customAlertMessage"></p>
            </div>
            
            <div class="flex justify-center gap-2 pt-3">
                <button type="button" @click="customAlertOpen = false" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold">Batal</button>
                <button type="button" @click="handleAlertConfirm()" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>
</div>
@endsection