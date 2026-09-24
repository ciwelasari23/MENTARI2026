@extends('layouts.admin')

@section('title', 'Form Pelaporan Lapangan')
@section('header', 'Pelaporan Realisasi Pekerjaan')

@section('content')
<div class="space-y-6" x-data="{ 
    modalTambah: {{ $errors->any() ? 'true' : 'false' }}, 
    modalDetail: false, 
    activeTargetId: null,
    activeRiwayatList: [],

    /* --- Data Alpine JS untuk Dropdown Interaktif --- */
    openCreateTarget: false,
    searchCreateTarget: '',
    selectedCreateTargetId: '{{ old('id_target_wilayah') }}',
    selectedCreateTargetLabel: '-- Pilih Pekerjaan --',
    selectedTargetDaerah: '',
    
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
        return [
            'id_target_wilayah' => $h->id_target_wilayah,
            'tanggal_lapor' => \Carbon\Carbon::parse($h->tanggal_lapor)->format('d/m/Y'),
            'realisasi_saat_ini' => $h->realisasi_saat_ini,
            'status_laporan' => $h->status_laporan,
            'catatan_verifikator' => $h->catatan_verifikator ?? '-',
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

    openHistoryModal(targetId) {
        this.activeTargetId = targetId;
        this.activeRiwayatList = this.allHistoryData.filter(h => h.id_target_wilayah == targetId);
        this.modalDetail = true;
    }
}">
    
    <div class="flex justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h3 class="text-base font-bold text-gray-800">Daftar Laporan Lapangan</h3>
            <p class="text-xs text-gray-500 mt-0.5">Kelola pelaporan realisasi pekerjaan Anda.</p>
        </div>
        <button @click="modalTambah = true" class="bg-[#10b981] hover:bg-emerald-600 text-white px-5 py-2 rounded-md text-sm font-semibold transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Buat Laporan
        </button>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" 
             x-init="setTimeout(() => show = false, 3000)" 
             x-show="show" 
             x-transition.duration.500ms
             class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm p-4 rounded-xl shadow-sm flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button @click="show = false" class="text-emerald-600 hover:text-emerald-800 font-bold">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 text-sm p-4 rounded-xl shadow-sm">
            <p class="font-semibold">Laporan belum dapat dikirim:</p>
            <ul class="list-disc list-inside mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-600 font-semibold">
                        <th class="p-4 text-center w-16">No</th>
                        <th class="p-4">Pekerjaan & Wilayah</th>
                        <th class="p-4 text-center">Total Akumulasi Capaian</th>
                        <th class="p-4 text-center">Update Terakhir</th>
                        <th class="p-4 text-center w-28">Log History</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($laporanGrouped as $index => $group)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="p-4 text-center font-medium text-gray-500">{{ $index + 1 }}</td>
                        
                        <!-- Pekerjaan & Wilayah: Nama Proses, Nama Detail Kegiatan (Level 3), lalu Nama Kab/Kota -->
                        <td class="p-4">
                            <span class="font-bold text-gray-800 block">{{ $group->targetWilayah->proses->nama_proses ?? '-' }}</span>
                            <!-- Ubah nama_detail_kegiatan menjadi nama_keg_detail -->
                            <span class="text-xs text-gray-600 font-medium block mt-0.5">{{ $group->targetWilayah->proses->detail->nama_keg_detail ?? '-' }}</span>
                            <span class="text-xs text-gray-400 block mt-0.5">
                                {{ $group->targetWilayah->wilayah->kode_nama_kabkota ?? '-' }}
                            </span>
                        </td>

                        <td class="p-4 text-center font-bold text-[#005A9C]">
                            {{ $group->total_capaian }} {{ $group->targetWilayah->proses->satuan_target ?? '' }}
                        </td>
                        <td class="p-4 text-center text-xs text-gray-500 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($group->tanggal_terakhir)->format('d/m/Y') }}
                        </td>
                        
                        <!-- Kolom Log History -->
                        <td class="p-4 text-center whitespace-nowrap">
                            <button @click="openHistoryModal('{{ $group->id_target_wilayah }}')" 
                                    class="text-blue-600 hover:text-white bg-blue-50 hover:bg-blue-600 p-2.5 rounded-lg transition-all duration-200 inline-flex items-center justify-center shadow-sm mx-auto" 
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
                        <td colspan="5" class="p-8 text-center text-gray-400 italic">
                            Belum ada riwayat laporan Anda.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form Tambah Laporan -->
    <div x-show="modalTambah" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4" style="display: none;" x-transition.opacity>
        <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full p-6 space-y-4 relative z-10" @click.stop x-transition.scale>
            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                <h3 class="text-base font-bold text-gray-800">Kirim Laporan Baru</h3>
                <button type="button" @click="modalTambah = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
            </div>
            
            <form action="{{ route('pelaporan.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                
                <div class="md:col-span-2 relative" @click.away="openCreateTarget = false">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Target Kegiatan Wilayah</label>
                    <div @click="openCreateTarget = !openCreateTarget" class="w-full border border-gray-200 rounded-lg p-2.5 text-sm bg-white cursor-pointer flex justify-between items-center focus:ring-2 focus:ring-[#005A9C]">
                        <span x-text="selectedCreateTargetLabel" :class="{'text-gray-400': !selectedCreateTargetId, 'text-gray-800 font-medium': selectedCreateTargetId}"></span>
                        <svg class="w-4 h-4 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                    
                    <input type="hidden" name="id_target_wilayah" x-model="selectedCreateTargetId" required>
                    
                    <div x-show="openCreateTarget" class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg p-2 max-h-60 overflow-y-auto" style="display: none;">
                        <input type="text" x-model="searchCreateTarget" placeholder="Ketik nama pekerjaan atau kabupaten..." class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm mb-2 focus:outline-none focus:ring-1 focus:ring-[#005A9C]">
                        <ul>
                            <template x-for="t in filteredTargets" :key="t.id">
                                <li @click="selectedCreateTargetId = t.id; selectedCreateTargetLabel = t.label; selectedTargetDaerah = t.target_daerah; openCreateTarget = false; searchCreateTarget = ''" 
                                    class="px-3 py-2 hover:bg-blue-50 hover:text-[#005A9C] rounded cursor-pointer text-sm text-gray-800 flex items-center justify-between border-b border-gray-50">
                                    <span x-text="t.label"></span>
                                    <span x-show="selectedCreateTargetId == t.id" class="text-[#005A9C] font-bold">✓</span>
                                </li>
                            </template>
                            <li x-show="filteredTargets.length === 0" class="px-3 py-2 text-sm text-gray-400 text-center">Pekerjaan tidak ditemukan</li>
                        </ul>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Target Daerah</label>
                    <input type="text" x-model="selectedTargetDaerah" readonly placeholder="Terisi otomatis..." class="w-full border border-gray-200 rounded-lg p-2.5 text-sm bg-gray-100 cursor-not-allowed text-gray-500 font-semibold focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Lapor</label>
                    <input type="text" value="{{ \Carbon\Carbon::now()->format('d/m/Y') }}" disabled class="w-full border border-gray-200 rounded-lg p-2.5 text-sm bg-gray-100 cursor-not-allowed text-gray-500 font-semibold focus:outline-none">
                    <input type="hidden" name="tanggal_lapor" value="{{ date('Y-m-d') }}">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Realisasi Kuantiti (Jumlah yang Dikerjakan)</label>
                    <input type="number" name="realisasi_kuantiti" min="1" required value="{{ old('realisasi_kuantiti') }}" class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#005A9C]">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Tautan Bukti Dukung (G-Drive / Link) - Opsional</label>
                    <input type="url" name="link_bukti" placeholder="https://..." value="{{ old('link_bukti') }}" class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#005A9C]">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Upload Bukti Dukung (File JPG/PNG/PDF)</label>
                    <input type="file" name="file_bukti" accept=".jpg,.jpeg,.png,.pdf" class="w-full border border-gray-200 rounded-lg p-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#005A9C]">
                </div>
                
                <div class="md:col-span-2 flex justify-end gap-2 pt-4 border-t border-gray-100">
                    <button type="button" @click="modalTambah = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-[#005A9C] hover:bg-[#004070] text-white rounded-lg text-sm font-semibold shadow">Kirim Laporan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Log History / Detail Riwayat Pelaporan -->
    <div x-show="modalDetail" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4" style="display: none;" x-transition.opacity>
        <div class="bg-white rounded-2xl shadow-xl max-w-xl w-full p-6 space-y-4 max-h-[90vh] overflow-y-auto" @click.away="modalDetail = false" x-transition.scale>
            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                <h3 class="text-base font-bold text-gray-800">Riwayat Log Pelaporan</h3>
                <button @click="modalDetail = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
            </div>
            
            <div class="space-y-4">
                <template x-for="(item, index) in activeRiwayatList" :key="index">
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-3 shadow-sm">
                        <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                            <span class="text-xs font-bold text-gray-500" x-text="'Laporan ke-' + (index + 1) + ' — Tanggal: ' + item.tanggal_lapor"></span>
                            <div>
                                <template x-if="item.status_laporan == 'pending'">
                                    <span class="bg-amber-50 text-amber-600 border border-amber-200 font-bold px-2.5 py-0.5 rounded-full text-[10px] uppercase">Diajukan</span>
                                </template>
                                <template x-if="item.status_laporan == 'approved'">
                                    <span class="bg-emerald-50 text-emerald-600 border border-emerald-200 font-bold px-2.5 py-0.5 rounded-full text-[10px] uppercase">Disetujui</span>
                                </template>
                                <template x-if="item.status_laporan == 'revision'">
                                    <span class="bg-blue-50 text-blue-600 border border-blue-200 font-bold px-2.5 py-0.5 rounded-full text-[10px] uppercase">Perlu Revisi</span>
                                </template>
                                <template x-if="item.status_laporan == 'rejected'">
                                    <span class="bg-red-50 text-red-600 border border-red-200 font-bold px-2.5 py-0.5 rounded-full text-[10px] uppercase">Ditolak</span>
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

                        <div>
                            <span class="block text-[11px] font-bold text-gray-500 mb-1">Catatan Verifikator:</span>
                            <div class="bg-white border border-gray-200 p-2.5 rounded-lg text-xs italic text-gray-600" x-text="item.catatan_verifikator"></div>
                        </div>
                    </div>
                </template>
            </div>
            
            <div class="flex justify-end pt-4 border-t border-gray-100">
                <button type="button" @click="modalDetail = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold">Tutup</button>
            </div>
        </div>
    </div>

</div>
@endsection