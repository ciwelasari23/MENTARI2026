@extends('layouts.admin')

@section('title', 'Form Pelaporan Lapangan')
@section('header', 'Pelaporan Realisasi Pekerjaan')

@section('content')
<div class="space-y-6" x-data="{ 
    modalTambah: {{ $errors->any() ? 'true' : 'false' }}, 
    modalDetail: false, 
    modalKonfirmasiKurang: false,
    modalWarningMelebihi: false,
    isSubmitting: false,
    activeTargetId: null,
    activeRiwayatList: [],

    /* --- Data Alpine JS untuk Dropdown Interaktif --- */
    openCreateTarget: false,
    searchCreateTarget: '',
    selectedCreateTargetId: '{{ old('id_target_wilayah') }}',
    selectedCreateTargetLabel: '-- Pilih Pekerjaan --',
    selectedTargetDaerah: 0,
    realisasiKuantiti: '{{ old('realisasi_kuantiti') }}',
    
    targetData: {{ json_encode($targets->map(function($t) {
        return [
            'id' => $t->id_target_wilayah,
            'label' => ($t->wilayah->nama_provinsi ?? '') . ' ' . ($t->wilayah->kode_nama_kabkota ?? '') . ' - ' . ($t->proses->nama_proses ?? ''),
            'target_daerah' => $t->target_daerah
        ];
    })) }},

    allHistoryData: {{ json_encode($laporanHistory->map(function($h) {
        $fileNameOnly = $h->file_bukti ? basename($h->file_bukti) : null;
        $fileUrl = $fileNameOnly ? route('laporan.file', ['filename' => $fileNameOnly]) : null;
        $catatanVerif = $h->catatan_verifikasi ?? ($h->catatan_verifikator ?? '-');
        return [
            'id_target_wilayah' => $h->id_target_wilayah,
            'tanggal_lapor' => \Carbon\Carbon::parse($h->tanggal_lapor)->format('d/m/Y'),
            'realisasi_saat_ini' => $h->realisasi_saat_ini,
            'status_laporan' => $h->status_laporan,
            'catatan_verifikator' => $catatanVerif,
            'link_bukti' => $h->link_bukti,
            'file_bukti' => $h->file_bukti,
            'file_url' => $fileUrl,
            'satuan' => $h->targetWilayah->proses->satuan_target ?? ''
        ];
    })) }},
    
    get filteredTargets() {
        if(this.searchCreateTarget === '') return this.targetData;
        return this.targetData.filter(t => t.label.toLowerCase().includes(this.searchCreateTarget.toLowerCase()));
    },

    get isMelebihiTarget() {
        return this.selectedTargetDaerah > 0 && Number(this.realisasiKuantiti) > Number(this.selectedTargetDaerah);
    },

    get isKurangTarget() {
        return this.selectedTargetDaerah > 0 && this.realisasiKuantiti !== '' && Number(this.realisasiKuantiti) < Number(this.selectedTargetDaerah);
    },

    openHistoryModal(targetId) {
        this.activeTargetId = targetId;
        this.activeRiwayatList = this.allHistoryData.filter(h => h.id_target_wilayah == targetId);
        this.modalDetail = true;
    },

    cekFormSubmit(event) {
        event.preventDefault();

        if (this.isMelebihiTarget) {
            this.modalWarningMelebihi = true;
            return;
        }

        if (this.isKurangTarget) {
            this.modalKonfirmasiKurang = true;
            return;
        }

        this.triggerSuccessAndSubmit();
    },

    triggerSuccessAndSubmit() {
        this.isSubmitting = true;
        setTimeout(() => {
            document.getElementById('form-tambah-laporan').submit();
        }, 1300);
    }
}">

    <div x-show="isSubmitting" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm" style="display: none;" x-transition.opacity>
        <div class="bg-white rounded-2xl p-8 shadow-2xl flex flex-col items-center space-y-4 max-w-xs w-full mx-4 text-center" x-data="{ showSpinner: true }" x-init="setTimeout(() => showSpinner = false, 800)">
            <div x-show="showSpinner" class="w-16 h-16 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin"></div>
            <div x-show="!showSpinner" x-transition.scale class="text-emerald-500 text-6xl font-bold leading-none">✓</div>
            <div class="pt-1">
                <h4 class="font-bold text-gray-800 text-base" x-text="showSpinner ? 'Mohon tunggu sebentar...' : 'Laporan berhasil dikirim'"></h4>
            </div>
        </div>
    </div>

    <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
            <h3 class="text-base font-bold text-gray-800">Daftar Laporan Lapangan</h3>
            <p class="text-xs text-gray-500 mt-0.5">Kelola pelaporan realisasi pekerjaan Anda.</p>
        </div>
        <button @click="modalTambah = true" class="bg-[#10b981] hover:bg-emerald-600 text-white px-5 py-2.5 rounded-xl text-xs font-semibold transition flex items-center gap-2 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Buat Laporan
        </button>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" 
             x-init="setTimeout(() => show = false, 3000)" 
             x-show="show" 
             x-transition.duration.500ms
             class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs p-4 rounded-2xl shadow-sm flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button @click="show = false" class="text-emerald-600 hover:text-emerald-800 font-bold">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 text-xs p-4 rounded-2xl shadow-sm">
            <p class="font-semibold">Laporan belum dapat dikirim:</p>
            <ul class="list-disc list-inside mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
        <!-- Form Entries per Page -->
        <form id="form-entries" action="{{ route('pelaporan.index') }}" method="GET" class="flex items-center gap-2 text-xs text-gray-600">
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

        <div class="overflow-x-auto border border-gray-100 rounded-xl">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/70 border-b border-gray-100 text-gray-500 text-[11px] uppercase tracking-wider font-semibold">
                        <th class="py-3.5 px-4 text-center w-12 font-bold">No</th>
                        <th class="py-3.5 px-4 font-bold">Pekerjaan & Wilayah</th>
                        <th class="py-3.5 px-4 text-center font-bold">Realisasi Terbaru</th>
                        <th class="py-3.5 px-4 text-center font-bold">Status Laporan</th>
                        <th class="py-3.5 px-4 text-center font-bold">Update Terakhir</th>
                        <th class="py-3.5 px-4 text-center font-bold w-28">Log History</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                    @forelse($laporanGrouped as $index => $group)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="py-3.5 px-4 text-center font-medium text-gray-400 text-xs">
                            {{ method_exists($laporanGrouped, 'firstItem') ? $laporanGrouped->firstItem() + $index : $index + 1 }}
                        </td>
                        
                        <td class="py-3.5 px-4">
                            <span class="font-bold text-gray-800 block text-sm">{{ $group->targetWilayah->proses->nama_proses ?? '-' }}</span>
                            <span class="text-xs text-gray-600 font-medium block mt-0.5">{{ $group->targetWilayah->proses->detail->nama_keg_detail ?? '-' }}</span>
                            <span class="text-xs text-gray-400 block mt-0.5">
                                {{ $group->targetWilayah->wilayah->kode_nama_kabkota ?? '-' }}
                            </span>
                        </td>

                        <!-- Angka Realisasi Terbaru -->
                        <td class="py-3.5 px-4 text-center font-bold text-[#005A9C] text-sm">
                            {{ number_format($group->realisasi_saat_ini) }} {{ $group->targetWilayah->proses->satuan_target ?? '' }}
                        </td>

                        <!-- Kolom Status Laporan Terbaru -->
                        <td class="py-3.5 px-4 text-center">
                            @if($group->is_selesai)
                                <span class="bg-purple-100 text-purple-700 font-bold px-3 py-1 rounded-xl text-xs uppercase tracking-wide">Selesai</span>
                            @elseif($group->status_laporan == 'pending')
                                <span class="bg-amber-100 text-amber-700 font-bold px-3 py-1 rounded-xl text-xs uppercase tracking-wide">Diajukan</span>
                            @elseif($group->status_laporan == 'approved')
                                <span class="bg-emerald-100 text-emerald-700 font-bold px-3 py-1 rounded-xl text-xs uppercase tracking-wide">Disetujui</span>
                            @elseif($group->status_laporan == 'revision')
                                <span class="bg-blue-100 text-blue-700 font-bold px-3 py-1 rounded-xl text-xs uppercase tracking-wide">Revisi</span>
                            @elseif($group->status_laporan == 'rejected')
                                <span class="bg-red-100 text-red-700 font-bold px-3 py-1 rounded-xl text-xs uppercase tracking-wide">Ditolak</span>
                            @else
                                <span class="bg-gray-100 text-gray-700 font-bold px-3 py-1 rounded-xl text-xs uppercase tracking-wide">-</span>
                            @endif
                        </td>

                        <td class="py-3.5 px-4 text-center text-xs text-gray-500 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($group->tanggal_lapor)->format('d/m/Y') }}
                        </td>
                        
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <button @click="openHistoryModal('{{ $group->id_target_wilayah }}')" 
                                    class="text-blue-600 hover:text-white bg-blue-50 hover:bg-blue-600 p-2 rounded-xl transition-all duration-200 inline-flex items-center justify-center shadow-sm mx-auto" 
                                    title="Lihat Log History">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 px-4 text-center text-gray-400 italic text-xs">
                            Belum ada riwayat laporan Anda.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($laporanGrouped, 'links'))
        <div class="flex flex-col sm:flex-row justify-between items-center text-xs text-gray-500 pt-3 gap-3">
            <div>
                Menampilkan <span class="font-semibold text-gray-700">{{ $laporanGrouped->firstItem() ?? 0 }}</span> ke <span class="font-semibold text-gray-700">{{ $laporanGrouped->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-gray-700">{{ number_format($laporanGrouped->total(), 0, ',', '.') }}</span> entri
            </div>
            
            <div class="flex items-center gap-1.5">
                @if ($laporanGrouped->onFirstPage())
                    <span class="px-3.5 py-2 border border-gray-200 rounded-xl bg-gray-50 text-gray-300 cursor-not-allowed flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </span>
                @else
                    <a href="{{ $laporanGrouped->previousPageUrl() }}" class="px-3.5 py-2 border border-gray-300 rounded-xl bg-white text-gray-700 hover:bg-gray-50 flex items-center justify-center transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </a>
                @endif

                <span class="px-4 py-2 border border-emerald-500 bg-emerald-500 text-white font-bold rounded-xl text-xs shadow-sm">
                    {{ $laporanGrouped->currentPage() }}
                </span>

                @if ($laporanGrouped->hasMorePages())
                    <a href="{{ $laporanGrouped->nextPageUrl() }}" class="px-3.5 py-2 border border-gray-300 rounded-xl bg-white text-gray-700 hover:bg-gray-50 flex items-center justify-center transition shadow-sm">
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

    <!-- Modal Form Tambah Laporan -->
    <div x-show="modalTambah" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4" style="display: none;" x-transition.opacity>
        <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full p-6 space-y-4 relative z-10" @click.stop x-transition.scale>
            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                <h3 class="text-sm font-bold text-gray-800">Kirim Laporan Baru</h3>
                <button type="button" @click="modalTambah = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold">&times;</button>
            </div>
            
            <form id="form-tambah-laporan" action="{{ route('pelaporan.store') }}" method="POST" enctype="multipart/form-data" @submit="cekFormSubmit(event)" class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                @csrf
                
                <div class="md:col-span-2 relative" @click.away="openCreateTarget = false">
                    <label class="block font-bold text-gray-700 mb-1">Pilih Target Kegiatan Wilayah</label>
                    <div @click="openCreateTarget = !openCreateTarget" class="w-full border border-gray-200 rounded-xl p-3 text-xs bg-white cursor-pointer flex justify-between items-center focus:ring-2 focus:ring-[#005A9C] shadow-sm">
                        <span x-text="selectedCreateTargetLabel" :class="{'text-gray-400': !selectedCreateTargetId, 'text-gray-800 font-medium': selectedCreateTargetId}"></span>
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                    
                    <input type="hidden" name="id_target_wilayah" x-model="selectedCreateTargetId" required>
                    
                    <div x-show="openCreateTarget" class="absolute z-50 mt-1.5 w-full bg-white border border-gray-200 rounded-xl shadow-xl p-2.5 max-h-60 overflow-y-auto" style="display: none;">
                        <input type="text" x-model="searchCreateTarget" placeholder="Ketik nama pekerjaan atau kabupaten..." class="w-full px-3.5 py-2 border border-gray-200 rounded-lg text-xs mb-2 focus:outline-none focus:ring-1 focus:ring-[#005A9C]">
                        <ul>
                            <template x-for="t in filteredTargets" :key="t.id">
                                <li @click="selectedCreateTargetId = t.id; selectedCreateTargetLabel = t.label; selectedTargetDaerah = t.target_daerah; openCreateTarget = false; searchCreateTarget = ''" 
                                    class="px-3 py-2 hover:bg-blue-50 hover:text-[#005A9C] rounded-lg cursor-pointer text-xs text-gray-800 flex items-center justify-between border-b border-gray-50">
                                    <span x-text="t.label"></span>
                                    <span x-show="selectedCreateTargetId == t.id" class="text-[#005A9C] font-bold">✓</span>
                                </li>
                            </template>
                            <li x-show="filteredTargets.length === 0" class="px-3 py-2 text-xs text-gray-400 text-center">Pekerjaan tidak ditemukan</li>
                        </ul>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Target Daerah</label>
                    <input type="text" x-model="selectedTargetDaerah" readonly placeholder="Terisi otomatis..." class="w-full border border-gray-200 rounded-xl p-3 text-xs bg-gray-100 cursor-not-allowed text-gray-500 font-semibold focus:outline-none shadow-sm">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Tanggal Lapor</label>
                    <input type="text" value="{{ \Carbon\Carbon::now()->format('d/m/Y') }}" disabled class="w-full border border-gray-200 rounded-xl p-3 text-xs bg-gray-100 cursor-not-allowed text-gray-500 font-semibold focus:outline-none shadow-sm">
                    <input type="hidden" name="tanggal_lapor" value="{{ date('Y-m-d') }}">
                </div>

                <div class="md:col-span-2">
                    <label class="block font-bold text-gray-700 mb-1">Realisasi Kuantiti (Jumlah yang Dikerjakan)</label>
                    <input type="number" name="realisasi_kuantiti" x-model.number="realisasiKuantiti" min="1" required class="w-full border border-gray-200 rounded-xl p-3 text-xs focus:outline-none focus:ring-2 focus:ring-[#005A9C] shadow-sm">
                </div>

                <div class="md:col-span-2">
                    <label class="block font-bold text-gray-700 mb-1">Tautan Bukti Dukung (G-Drive / Link) - Opsional</label>
                    <input type="url" name="link_bukti" placeholder="https://..." value="{{ old('link_bukti') }}" class="w-full border border-gray-200 rounded-xl p-3 text-xs focus:outline-none focus:ring-2 focus:ring-[#005A9C] shadow-sm">
                </div>

                <div class="md:col-span-2">
                    <label class="block font-bold text-gray-700 mb-1">Upload Bukti Dukung (File JPG/PNG/PDF) <span class="text-red-500">*</span></label>
                    <input type="file" name="file_bukti" accept=".jpg,.jpeg,.png,.pdf" required class="w-full border border-gray-200 rounded-xl p-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#005A9C] file:mr-3 file:py-2 file:px-3.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 shadow-sm">
                    <p class="text-[10px] text-gray-500 mt-1">Laporan tidak dapat dikirim jika file bukti dukung belum dilampirkan.</p>
                </div>
                
                <div class="md:col-span-2 flex justify-end gap-2 pt-4 border-t border-gray-100">
                    <button type="button" @click="modalTambah = false" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold">Batal</button>
                    <button type="submit" class="px-4 py-2.5 bg-[#005A9C] hover:bg-[#004070] text-white rounded-xl text-xs font-bold shadow transition">
                        Kirim Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="modalWarningMelebihi" class="fixed inset-0 z-[70] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" style="display: none;" x-transition.opacity>
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 space-y-4 text-center" @click.away="modalWarningMelebihi = false" x-transition.scale>
            <div class="flex justify-center text-amber-500">
                <svg class="w-14 h-14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            
            <div class="space-y-1.5">
                <h4 class="font-extrabold text-red-600 text-xl">Warning!</h4>
                <p class="text-xs text-gray-600 leading-relaxed">
                    Realisasi melebihi target (<span class="font-bold text-red-600" x-text="realisasiKuantiti"></span> dari <span class="font-bold" x-text="selectedTargetDaerah"></span>).
                </p>
            </div>
            
            <div class="pt-1 flex justify-center">
                <button type="button" @click="modalWarningMelebihi = false; realisasiKuantiti = '';" class="w-12 h-12 inline-flex items-center justify-center bg-[#005A9C] hover:bg-[#004070] text-white rounded-xl shadow-md transition transform hover:scale-105" title="Kembali">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div x-show="modalKonfirmasiKurang" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4" style="display: none;" x-transition.opacity>
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 space-y-4 text-center" @click.away="modalKonfirmasiKurang = false" x-transition.scale>
            <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center mx-auto text-xl font-bold">⚠️</div>
            <div class="space-y-1">
                <h4 class="font-bold text-gray-800 text-sm">Konfirmasi Pengiriman Laporan</h4>
                <p class="text-xs text-gray-500 leading-relaxed">
                    Realisasi kurang dari total target (<span x-text="realisasiKuantiti"></span> dari <span x-text="selectedTargetDaerah"></span>). Apakah Anda yakin untuk mengirimkan laporan?
                </p>
            </div>
            
            <div class="flex justify-center gap-2 pt-3">
                <button type="button" @click="modalKonfirmasiKurang = false" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold">Batal</button>
                <button type="button" @click="modalKonfirmasiKurang = false; triggerSuccessAndSubmit();" class="px-4 py-2.5 bg-[#005A9C] hover:bg-[#004070] text-white rounded-xl text-xs font-bold shadow-sm">Kirim</button>
            </div>
        </div>
    </div>

    <!-- Modal Log History -->
    <div x-show="modalDetail" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4" style="display: none;" x-transition.opacity>
        <div class="bg-white rounded-2xl shadow-xl max-w-xl w-full p-6 space-y-4 max-h-[90vh] overflow-y-auto" @click.away="modalDetail = false" x-transition.scale>
            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                <h3 class="text-sm font-bold text-gray-800">Riwayat Log Pelaporan</h3>
                <button @click="modalDetail = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold">&times;</button>
            </div>
            
            <div class="space-y-4">
                <template x-for="(item, index) in activeRiwayatList" :key="index">
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-3 shadow-sm text-xs">
                        <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                            <span class="font-bold text-gray-500" x-text="'Laporan ke-' + (index + 1) + ' — Tanggal: ' + item.tanggal_lapor"></span>
                            <div>
                                <template x-if="item.status_laporan == 'pending'">
                                    <span class="bg-amber-50 text-amber-600 border border-amber-200 font-bold px-3 py-1 rounded-xl text-[10px] uppercase">Diajukan</span>
                                </template>
                                <template x-if="item.status_laporan == 'approved'">
                                    <span class="bg-emerald-50 text-emerald-600 border border-emerald-200 font-bold px-3 py-1 rounded-xl text-[10px] uppercase">Disetujui</span>
                                </template>
                                <template x-if="item.status_laporan == 'revision'">
                                    <span class="bg-blue-50 text-blue-600 border border-blue-200 font-bold px-3 py-1 rounded-xl text-[10px] uppercase">Perlu Revisi</span>
                                </template>
                                <template x-if="item.status_laporan == 'rejected'">
                                    <span class="bg-red-50 text-red-600 border border-red-200 font-bold px-3 py-1 rounded-xl text-[10px] uppercase">Ditolak</span>
                                </template>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <span class="font-semibold text-gray-400 block">Realisasi Capaian</span>
                                <span class="font-bold text-[#005A9C] text-sm" x-text="item.realisasi_saat_ini + ' ' + item.satuan"></span>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-400 block">Lampiran Bukti</span>
                                <template x-if="item.file_bukti">
                                    <a :href="item.file_url" target="_blank" class="text-blue-600 hover:underline font-semibold inline-flex items-center gap-1 mt-0.5">Lihat File</a>
                                </template>
                                <template x-if="!item.file_bukti">
                                    <span class="text-gray-400 italic">Tidak ada file</span>
                                </template>
                            </div>
                        </div>

                        <!-- Catatan Verifikator di dalam Log History -->
                        <div class="mt-2 p-3 rounded-xl text-xs" :class="{
                            'bg-blue-50 border border-blue-200 text-blue-900': item.status_laporan == 'revision',
                            'bg-red-50 border border-red-200 text-red-900': item.status_laporan == 'rejected',
                            'bg-white border border-gray-200 text-gray-700': item.status_laporan != 'revision' && item.status_laporan != 'rejected'
                        }">
                            <span class="block font-bold mb-1" :class="{
                                'text-blue-700': item.status_laporan == 'revision',
                                'text-red-700': item.status_laporan == 'rejected',
                                'text-gray-500': item.status_laporan != 'revision' && item.status_laporan != 'rejected'
                            }">Catatan Verifikator:</span>
                            <p class="italic" x-text="item.catatan_verifikator && item.catatan_verifikator !== '-' ? item.catatan_verifikator : 'Tidak ada catatan.'"></p>
                        </div>
                    </div>
                </template>
            </div>
            
            <div class="flex justify-end pt-4 border-t border-gray-100">
                <button type="button" @click="modalDetail = false" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold">Tutup</button>
            </div>
        </div>
    </div>

</div>
@endsection