@extends('layouts.admin')

@section('title', 'Form Pelaporan Lapangan')
@section('header', 'Pelaporan Realisasi Pekerjaan')

@section('content')
<div class="space-y-6" x-data="{ 
    modalTambah: {{ $errors->any() ? 'true' : 'false' }}, 
    modalDetail: false, 
    activeLaporan: {},

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
    
    get filteredTargets() {
        if(this.searchCreateTarget === '') return this.targetData;
        return this.targetData.filter(t => t.label.toLowerCase().includes(this.searchCreateTarget.toLowerCase()));
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

    <!-- Notifikasi Sukses -->
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
                        <th class="p-4">Tanggal Lapor</th>
                        <th class="p-4">Pekerjaan & Wilayah</th>
                        <th class="p-4 text-center">Capaian</th>
                        <th class="p-4 text-center">Status & Catatan</th>
                        <th class="p-4 text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($laporan as $index => $item)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="p-4 text-center font-medium text-gray-500">{{ $index + 1 }}</td>
                        <td class="p-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($item->tanggal_lapor)->format('d/m/Y') }}</td>
                        <td class="p-4">
                            <span class="font-bold text-gray-800">{{ $item->targetWilayah->proses->nama_proses ?? '-' }}</span><br>
                            <span class="text-xs text-gray-500 font-normal">{{ $item->targetWilayah->wilayah->nama_provinsi ?? '-' }} {{ $item->targetWilayah->wilayah->kode_nama_kabkota ?? '-' }}</span>
                        </td>
                        <td class="p-4 text-center font-bold text-[#005A9C]">
                            {{ $item->realisasi_saat_ini }} {{ $item->targetWilayah->proses->satuan_target ?? '' }}
                        </td>
                        <td class="p-4 text-center align-top">
                            @if($item->status_laporan == 'pending')
                                <span class="bg-amber-50 text-amber-600 border border-amber-200 font-bold px-3 py-1 rounded-full text-[10px] uppercase inline-block mx-auto mt-2">Diajukan</span>
                            @elseif($item->status_laporan == 'approved')
                                <span class="bg-emerald-50 text-emerald-600 border border-emerald-200 font-bold px-3 py-1 rounded-full text-[10px] uppercase inline-block mx-auto mt-2">Disetujui</span>
                            @elseif($item->status_laporan == 'revision')
                                <span class="bg-blue-50 text-blue-600 border border-blue-200 font-bold px-3 py-1 rounded-full text-[10px] uppercase inline-block mx-auto mb-2 mt-1">Perlu Revisi</span>
                                @if($item->catatan_verifikator)
                                    <div class="text-[11px] text-blue-700 bg-blue-50/80 p-2 rounded text-left leading-tight border border-blue-100 shadow-sm w-full mx-auto">
                                        <span class="font-bold block mb-0.5">Catatan Verifikator:</span> 
                                        {{ $item->catatan_verifikator }}
                                    </div>
                                @endif
                            @else
                                <span class="bg-red-50 text-red-600 border border-red-200 font-bold px-3 py-1 rounded-full text-[10px] uppercase inline-block mx-auto mb-2 mt-1">Ditolak</span>
                                @if($item->catatan_verifikator)
                                    <div class="text-[11px] text-red-700 bg-red-50/80 p-2 rounded text-left leading-tight border border-red-100 shadow-sm w-full mx-auto">
                                        <span class="font-bold block mb-0.5">Catatan Verifikator:</span> 
                                        {{ $item->catatan_verifikator }}
                                    </div>
                                @endif
                            @endif
                        </td>
                        <td class="p-4 text-center align-top pt-5">
                            <div class="flex items-center justify-center gap-1">
                                @php
                                    $fileUrl = '';
                                    if ($item->file_bukti) {
                                        $fileNameOnly = basename($item->file_bukti);
                                        $fileUrl = route('laporan.file', ['filename' => $fileNameOnly]);
                                    }
                                @endphp

                                <!-- Tombol Aksi untuk Membuka Modal Detail yang Memuat Catatan -->
                                <button @click="
                                    activeLaporan = {{ json_encode($item) }}; 
                                    activeLaporan.file_url = '{{ $fileUrl }}';
                                    modalDetail = true;
                                " class="text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition-colors flex items-center gap-1 text-xs font-bold" title="Lihat Detail & Catatan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Detail
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-400 italic">
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
                
                <!-- Dropdown Pencarian Interaktif -->
                <div class="md:col-span-2 relative" @click.away="openCreateTarget = false">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Target Kegiatan Wilayah</label>
                    <div @click="openCreateTarget = !openCreateTarget" class="w-full border border-gray-200 rounded-lg p-2.5 text-sm bg-white cursor-pointer flex justify-between items-center focus:ring-2 focus:ring-[#005A9C]">
                        <span x-text="selectedCreateTargetLabel" :class="{'text-gray-400': !selectedCreateTargetId, 'text-gray-800 font-medium': selectedCreateTargetId}"></span>
                        <svg class="w-4 h-4 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                    
                    <input type="hidden" name="id_target_wilayah" x-model="selectedCreateTargetId" required>
                    
                    <!-- Kotak Dropdown -->
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

                <!-- Input Otomatis (Target Daerah) -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Target Daerah</label>
                    <input type="text" x-model="selectedTargetDaerah" readonly placeholder="Terisi otomatis..." class="w-full border border-gray-200 rounded-lg p-2.5 text-sm bg-gray-100 cursor-not-allowed text-gray-500 font-semibold focus:outline-none">
                </div>

                <!-- Tanggal Lapor (Terkunci Penuh) -->
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

    <!-- Modal Detail Laporan & Catatan Verifikator -->
    <div x-show="modalDetail" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4" style="display: none;" x-transition.opacity>
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 space-y-4" @click.away="modalDetail = false" x-transition.scale>
            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                <h3 class="text-base font-bold text-gray-800">Detail Laporan & Catatan</h3>
                <button @click="modalDetail = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
            </div>
            
            <div class="space-y-3 text-sm">
                <div>
                    <span class="block text-xs text-gray-400 font-semibold">Tanggal Lapor</span>
                    <span class="font-bold text-gray-800" x-text="activeLaporan.tanggal_lapor"></span>
                </div>
                <div>
                    <span class="block text-xs text-gray-400 font-semibold">Pekerjaan & Wilayah</span>
                    <span class="text-gray-800 font-bold block" x-text="activeLaporan.target_wilayah?.proses?.nama_proses || '-'"></span>
                    <span class="text-xs text-gray-500" x-text="(activeLaporan.target_wilayah?.wilayah?.nama_provinsi || '') + ' ' + (activeLaporan.target_wilayah?.wilayah?.kode_nama_kabkota || '')"></span>
                </div>
                <div>
                    <span class="block text-xs text-gray-400 font-semibold">Capaian (Realisasi)</span>
                    <span class="font-bold text-[#005A9C]" x-text="activeLaporan.realisasi_saat_ini + ' ' + (activeLaporan.target_wilayah?.proses?.satuan_target || '')"></span>
                </div>
                
                <!-- Tautan External -->
                <div>
                    <span class="block text-xs text-gray-400 font-semibold">Tautan Bukti</span>
                    <template x-if="activeLaporan.link_bukti">
                        <a :href="activeLaporan.link_bukti.startsWith('http') ? activeLaporan.link_bukti : 'https://' + activeLaporan.link_bukti" target="_blank" class="text-blue-600 hover:underline break-all flex items-center gap-1 mt-1">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                            <span x-text="activeLaporan.link_bukti"></span>
                        </a>
                    </template>
                    <template x-if="!activeLaporan.link_bukti">
                        <span class="text-gray-500 italic">Tidak ada tautan external</span>
                    </template>
                </div>
                
                <!-- File System Lokal -->
                <div>
                    <span class="block text-xs text-gray-400 font-semibold">File Bukti Terlampir</span>
                    <template x-if="activeLaporan.file_bukti">
                        <a :href="activeLaporan.file_url" target="_blank" class="text-[#005A9C] font-semibold hover:underline inline-flex items-center gap-1 mt-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Lihat Dokumen / Gambar
                        </a>
                    </template>
                    <template x-if="!activeLaporan.file_bukti">
                        <span class="text-gray-500 italic">Tidak ada file lampiran</span>
                    </template>
                </div>
                
                <!-- Catatan Verifikator -->
                <div>
                    <span class="block text-xs text-gray-400 font-semibold mb-1">Catatan Verifikator</span>
                    <div :class="{
                        'bg-red-50 border border-red-200 text-red-700': activeLaporan.status_laporan === 'revision' || activeLaporan.status_laporan === 'rejected', 
                        'bg-gray-50 border border-gray-100 text-gray-600': activeLaporan.status_laporan !== 'revision' && activeLaporan.status_laporan !== 'rejected'
                    }" class="p-3 rounded-lg italic text-sm">
                       <span x-text="activeLaporan.catatan_verifikasi || activeLaporan.catatan_verifikator || activeLaporan.catatan || activeLaporan.catatan_revisi || 'Belum ada catatan dari verifikator.'"></span>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end pt-4 border-t border-gray-100">
                <button type="button" @click="modalDetail = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold">Tutup</button>
            </div>
        </div>
    </div>

</div>
@endsection