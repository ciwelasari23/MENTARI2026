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
                    // Kirim AJAX ke database agar status berubah jadi approved, lalu reload / buka modal nilai
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
                        // Fallback jika fetch error, langsung submit form
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
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-3 rounded-xl text-xs shadow-sm flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button @click="show = false" class="text-emerald-600 font-bold">&times;</button>
        </div>
    @endif

    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 space-y-4">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3">
            <div>
                <h3 class="text-sm font-bold text-gray-800">Daftar Masuk Laporan Petugas / Mitra</h3>
                <p class="text-[11px] text-gray-500 mt-0.5">Verifikasi laporan realisasi pekerjaan dari lapangan.</p>
            </div>

            <!-- Bagian Filter & Sorting Controls -->
            <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                <div class="w-auto">
                    <select x-model="selectedStatus" class="border border-gray-200 rounded-lg px-3 py-1.5 text-xs bg-white cursor-pointer shadow-xs focus:outline-none focus:ring-1 focus:ring-[#005A9C] font-medium text-gray-700">
                        <option value="">Status: Semua</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Disetujui</option>
                        <option value="revision">Perlu Revisi</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>

                <div class="w-auto">
                    <select x-model="sortOrder" @change="sortRows()" class="border border-gray-200 rounded-lg px-3 py-1.5 text-xs bg-white cursor-pointer shadow-xs focus:outline-none focus:ring-1 focus:ring-[#005A9C] font-medium text-gray-700">
                        <option value="desc">Waktu: Terbaru</option>
                        <option value="asc">Waktu: Terlama</option>
                    </select>
                </div>

                <div class="relative w-auto min-w-[220px]" @click.away="openFilter = false">
                    <div @click="openFilter = !openFilter" class="border border-gray-200 rounded-lg px-3 py-1.5 text-xs bg-white cursor-pointer flex justify-between items-center gap-2 shadow-xs">
                        <span x-text="selectedFilterLabel" class="truncate font-medium text-gray-700"></span>
                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>

                    <div x-show="openFilter" class="absolute right-0 z-40 mt-1 w-72 bg-white border border-gray-200 rounded-lg shadow-xl p-2 max-h-60 overflow-y-auto" style="display: none;">
                        <input type="text" x-model="searchFilter" placeholder="Ketik cari pekerjaan/wilayah..." class="w-full px-2.5 py-1.5 border border-gray-200 rounded text-xs mb-2 focus:outline-none focus:ring-1 focus:ring-[#005A9C]">
                        <ul>
                            <li @click="selectedFilterId = ''; selectedFilterLabel = 'Semua Pekerjaan & Wilayah'; openFilter = false; searchFilter = ''" 
                                class="px-2.5 py-1.5 hover:bg-blue-50 text-xs rounded cursor-pointer text-gray-600 font-medium border-b border-gray-50">
                                -- Tampilkan Semua --
                            </li>
                            <template x-for="f in filteredList" :key="f.id">
                                <li @click="selectedFilterId = f.id; selectedFilterLabel = f.label; openFilter = false; searchFilter = ''" 
                                    class="px-2.5 py-1.5 hover:bg-blue-50 text-xs rounded cursor-pointer text-gray-800 flex items-center justify-between border-b border-gray-50">
                                    <span x-text="f.label" class="truncate pr-2"></span>
                                    <span x-show="selectedFilterId == f.id" class="text-[#005A9C] font-bold shrink-0">✓</span>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Data -->
        <div class="overflow-x-auto border border-gray-100 rounded-xl">
            <table class="w-full text-left border-collapse text-xs whitespace-nowrap min-w-[1100px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-600 font-semibold">
                        <th class="p-3 text-center w-12">No</th>
                        <th class="p-3">Pekerjaan & Wilayah</th>
                        <th class="p-3 text-center">Target</th>
                        <th class="p-3 text-center">Realisasi</th>
                        <th class="p-3 text-center">Bukti</th>
                        <th class="p-3">Diajukan Oleh</th>
                        <th class="p-3">Waktu Pengajuan</th>
                        <th class="p-3">Diverifikasi Oleh</th>
                        <th class="p-3 text-center">Penilaian</th>
                        <th class="p-3">Status Verifikasi</th>
                        <th class="p-3 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-laporan-body" class="divide-y divide-gray-100 text-gray-700">
                    @forelse($laporans as $index => $item)
                    <tr class="hover:bg-gray-50/50 transition-colors" 
                        x-show="(selectedFilterId === '' || selectedFilterId === '{{ $item->id_laporan }}') && 
                                (selectedStatus === '' || selectedStatus === '{{ $item->status_laporan ?? 'pending' }}')"
                        data-timestamp="{{ strtotime($item->created_at) }}">
                        <td class="p-3 text-center font-medium text-gray-500 row-number">{{ $index + 1 }}</td>
                        
                        <td class="p-3">
                            <span class="font-bold text-gray-800">{{ $item->targetWilayah->proses->nama_proses ?? '-' }}</span><br>
                            <span class="text-[11px] text-gray-600 font-medium block">{{ $item->targetWilayah->proses->detail->nama_keg_detail ?? '-' }}</span>
                            <span class="text-[10px] text-gray-400">{{ $item->targetWilayah->wilayah->kode_nama_kabkota ?? '-' }}</span>
                        </td>

                        <td class="p-3 text-center font-semibold text-gray-600">
                            {{ $item->targetWilayah->target_daerah ?? '-' }} {{ $item->targetWilayah->proses->satuan_target ?? '' }}
                        </td>

                        <td class="p-3 text-center font-bold text-[#005A9C]">
                            {{ $item->realisasi_saat_ini }} {{ $item->targetWilayah->proses->satuan_target ?? '' }}
                        </td>

                        <td class="p-3 text-center space-y-1">
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

                        <td class="p-3 font-medium text-gray-800">
                            {{ $item->pelapor->nama_lengkap ?? ($item->user->nama_lengkap ?? '-') }}
                        </td>

                        <td class="p-3">
                            <span class="text-gray-700 font-medium block">{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}</span>
                            <span class="text-[10px] text-gray-500">{{ \Carbon\Carbon::parse($item->created_at)->format('H:i') }} WIB</span>
                        </td>

                        <td class="p-3 font-medium text-gray-800">
                            {{ $item->verifikator->nama_lengkap ?? '-' }}
                        </td>
                        <!-- Kolom Penilaian (Angka bersih tanpa kotak & ikon edit di bawahnya) -->
                        <td class="p-3 text-center">
                            @php
                                $skorNilai = $item->skor_manual ?? ($item->targetWilayah->skor_manual ?? null);
                            @endphp
                            @if($skorNilai !== null)
                                <div class="flex flex-col items-center justify-center gap-1">
                                    <span class="font-bold text-gray-800 text-sm">
                                        {{ $skorNilai }}
                                    </span>
                                    <button @click="targetIdNilai = '{{ $item->id_laporan }}'; skorManualNilai = '{{ $skorNilai }}'; modalNilaiOpen = true;" 
                                        class="text-gray-400 hover:text-blue-600 transition-colors inline-flex items-center gap-0.5 text-[10px]" title="Edit Nilai">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        Edit
                                    </button>
                                </div>
                            @else
                                <span class="text-gray-400 italic text-[11px]">- Belum ada -</span>
                            @endif
                        </td>

                        <td class="p-3">
                            <div class="flex flex-col items-start gap-1">
                                @php $st = $item->status_laporan ?? 'pending'; @endphp
                                @if($st == 'approved')
                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold px-2 py-0.5 rounded-full text-[9px] uppercase">Disetujui</span>
                                    <span class="text-[10px] text-gray-500">{{ \Carbon\Carbon::parse($item->updated_at)->format('d/m/Y H:i') }} WIB</span>
                                @elseif($st == 'revision')
                                    <div class="flex items-center gap-1">
                                        <span class="bg-blue-50 text-blue-700 border border-blue-200 font-bold px-2 py-0.5 rounded-full text-[9px] uppercase">Perlu Revisi</span>
                                        @if($item->catatan_verifikasi)
                                            <button @click="modalCatatanOpen = true; activeCatatanTitle = 'Catatan Perlu Revisi'; activeCatatanText = '{{ addslashes($item->catatan_verifikasi) }}'" 
                                                class="bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-bold px-1.5 py-0.5 rounded shadow-2xs transition inline-flex items-center gap-0.5">
                                                Catatan
                                            </button>
                                        @endif
                                    </div>
                                    <span class="text-[10px] text-gray-500">{{ \Carbon\Carbon::parse($item->updated_at)->format('d/m/Y H:i') }} WIB</span>
                                @elseif($st == 'rejected')
                                    <div class="flex items-center gap-1">
                                        <span class="bg-red-50 text-red-700 border border-red-200 font-bold px-2 py-0.5 rounded-full text-[9px] uppercase">Ditolak</span>
                                        @if($item->catatan_verifikasi)
                                            <button @click="modalCatatanOpen = true; activeCatatanTitle = 'Catatan Penolakan Laporan'; activeCatatanText = '{{ addslashes($item->catatan_verifikasi) }}'" 
                                                class="bg-red-600 hover:bg-red-700 text-white text-[10px] font-bold px-1.5 py-0.5 rounded shadow-2xs transition inline-flex items-center gap-0.5">
                                                Catatan
                                            </button>
                                        @endif
                                    </div>
                                    <span class="text-[10px] text-gray-500">{{ \Carbon\Carbon::parse($item->updated_at)->format('d/m/Y H:i') }} WIB</span>
                                @else
                                    <span class="bg-amber-50 text-amber-700 border border-amber-200 font-bold px-2 py-0.5 rounded-full text-[9px] uppercase inline-block mb-0.5">Pending</span>
                                    <span class="block text-[10px] italic text-gray-400">Belum diverifikasi</span>
                                @endif
                            </div>
                        </td>

                        <td class="p-3 text-center">
                            <button @click="modalOpen = true; currentId = '{{ $item->id_laporan }}'; currentStatus = '{{ $item->status_laporan }}'; currentCatatan = '{{ $item->catatan_verifikasi }}';" 
                                class="inline-flex items-center gap-1 text-teal-600 hover:text-white bg-teal-50 hover:bg-teal-600 px-3 py-1.5 rounded-lg transition-colors font-bold text-xs shadow-2xs">
                                Verifikasi
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="p-6 text-center text-gray-400 italic text-xs">
                            Belum ada laporan masuk dari lapangan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Lihat Catatan Revisi / Penolakan -->
    <div x-show="modalCatatanOpen" class="fixed inset-0 bg-black/50 backdrop-blur-xs flex items-center justify-center z-50 p-4" style="display: none;" x-transition.opacity>
        <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-5 space-y-3" @click.away="modalCatatanOpen = false" x-transition.scale>
            <div class="flex justify-between items-center border-b border-gray-100 pb-2">
                <h3 class="text-sm font-bold text-gray-800 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span x-text="activeCatatanTitle"></span>
                </h3>
                <button type="button" @click="modalCatatanOpen = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold">&times;</button>
            </div>
            
            <div class="p-2.5 bg-gray-50 border border-gray-100 rounded-xl text-gray-700 text-xs italic leading-relaxed">
                <span x-text="activeCatatanText"></span>
            </div>

            <div class="flex justify-end pt-1">
                <button type="button" @click="modalCatatanOpen = false" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Modal Form Verifikasi Utama -->
    <div x-show="modalOpen" class="fixed inset-0 bg-black/50 backdrop-blur-xs flex items-center justify-center z-40 p-4" style="display: none;" x-transition.opacity>
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-5 space-y-3" @click.away="modalOpen = false" x-transition.scale>
            <div class="flex justify-between items-center border-b border-gray-100 pb-2">
                <h3 class="text-sm font-bold text-gray-800">Proses Verifikasi Laporan</h3>
                <button type="button" @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold">&times;</button>
            </div>
            
            <form id="form-verifikasi" x-bind:action="currentId ? '{{ route('admin.verifikasi.update', ['id' => '__ID__']) }}'.replace('__ID__', currentId) : '#'" method="POST" @submit="submitVerifikasi($event)" class="space-y-3 text-xs">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1 text-[10px]">Status Laporan</label>
                    <select name="status_laporan" x-model="currentStatus" required class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none text-xs focus:ring-2 focus:ring-[#005A9C]">
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
                    <textarea name="catatan_verifikator" x-model="currentCatatan" @keydown.enter.stop rows="3" placeholder="Berikan catatan atau alasan revisi/penolakan..." class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none text-xs focus:ring-2 focus:ring-[#005A9C]"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" @click="modalOpen = false" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold">Batal</button>
                    <button type="submit" class="px-3.5 py-1.5 bg-[#14B8A6] hover:bg-teal-600 text-white rounded-lg font-semibold shadow-2xs">Simpan Verifikasi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Penilaian & Selesai -->
    <div x-show="modalNilaiOpen" class="fixed inset-0 bg-black/50 backdrop-blur-xs flex items-center justify-center z-50 p-4" style="display: none;" x-transition.opacity>
        <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-5 space-y-3" @click.away="modalNilaiOpen = false" x-transition.scale>
            <div class="flex justify-between items-center border-b border-gray-100 pb-2">
                <h4 class="font-bold text-gray-800 text-sm">Penilaian / Rating Kegiatan Selesai</h4>
                <button @click="modalNilaiOpen = false" class="text-gray-400 hover:text-gray-600 font-bold text-lg">&times;</button>
            </div>
            
            <form x-bind:action="targetIdNilai ? '{{ route('evaluasi.update-skor', ['id' => '__ID__']) }}'.replace('__ID__', targetIdNilai) : '#'" method="POST" class="space-y-3">
                @csrf
                @method('PUT')
                <div class="space-y-2">
                    <p class="text-[11px] text-gray-500 leading-relaxed">Silakan masukkan skor penilaian (0 - 100) untuk rekapitulasi evaluasi kegiatan.</p>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-700 mb-1 uppercase tracking-wider">Skor Penilaian (0 - 100)</label>
                        <input type="number" name="skor_manual" x-model="skorManualNilai" min="0" max="100" required class="w-full border border-gray-200 rounded-xl p-2.5 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none font-bold text-blue-600 text-center bg-gray-50" placeholder="Contoh: 85">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" @click="modalNilaiOpen = false" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold shadow-2xs">Simpan Nilai</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Alert Custom di Tengah -->
    <div x-show="customAlertOpen" class="fixed inset-0 bg-black/60 backdrop-blur-xs flex items-center justify-center z-[70] p-4" style="display: none;" x-transition.opacity>
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-5 space-y-4 text-center" @click.away="customAlertOpen = false" x-transition.scale>
            <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mx-auto text-xl font-bold">
                ℹ️
            </div>
            <div class="space-y-1">
                <h4 class="font-bold text-gray-800 text-sm" x-text="customAlertTitle"></h4>
                <p class="text-xs text-gray-500 leading-relaxed" x-text="customAlertMessage"></p>
            </div>
            
            <div class="flex justify-center gap-2 pt-2">
                <button type="button" @click="customAlertOpen = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold">Batal</button>
                <button type="button" @click="handleAlertConfirm()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>
</div>
@endsection