@extends('layouts.admin')

@section('title', 'Form Pelaporan Lapangan')
@section('header', 'Pelaporan Realisasi Pekerjaan')

@section('content')
<div class="space-y-6" x-data="{ modalTambah: {{ $errors->any() ? 'true' : 'false' }}, modalDetail: false, activeLaporan: {} }">
    
    <!-- Bagian Header Tombol Aksi -->
    <div class="flex justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h3 class="text-base font-bold text-gray-800">Daftar Laporan Lapangan</h3>
            <p class="text-xs text-gray-500 mt-0.5">Kelola pelaporan realisasi pekerjaan Anda.</p>
        </div>
        <button @click="modalTambah = true" class="bg-[#005A9C] hover:bg-[#004070] text-white text-sm font-semibold px-4 py-2 rounded-lg shadow transition-colors flex items-center gap-2">
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

    <!-- Tabel Data Laporan -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-600 font-semibold">
                        <th class="p-4 text-center w-16">No</th>
                        <th class="p-4">Tanggal Lapor</th>
                        <th class="p-4">Pekerjaan & Wilayah</th>
                        <th class="p-4 text-center">Capaian</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($laporan as $index => $item)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="p-4 text-center font-medium text-gray-500">{{ $index + 1 }}</td>
                        <td class="p-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($item->tanggal_lapor)->format('d/m/Y') }}</td>
                        <td class="p-4">
                            <span class="font-bold text-gray-800">{{ $item->target->proses->nama_proses ?? '-' }}</span><br>
                            <span class="text-xs text-gray-500 font-normal">{{ $item->target->wilayah->nama_wilayah ?? '-' }}</span>
                        </td>
                        <td class="p-4 text-center font-bold text-[#005A9C]">
                            {{ $item->realisasi_kuantiti }} {{ $item->target->proses->satuan_target ?? '' }}
                        </td>
                        <td class="p-4 text-center">
                            @if($item->status_laporan == 'pending')
                                <span class="bg-amber-50 text-amber-600 border border-amber-200 font-bold px-3 py-1 rounded-full text-[10px] uppercase">Menunggu</span>
                            @elseif($item->status_laporan == 'approved')
                                <span class="bg-emerald-50 text-emerald-600 border border-emerald-200 font-bold px-3 py-1 rounded-full text-[10px] uppercase">Disetujui</span>
                            @else
                                <span class="bg-red-50 text-red-600 border border-red-200 font-bold px-3 py-1 rounded-full text-[10px] uppercase">Ditolak</span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button @click="activeLaporan = {{ json_encode($item) }}; modalDetail = true" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs px-3 py-1.5 rounded shadow transition-colors">
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
    <div x-show="modalTambah" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4" style="display: none;" x-transition.opacity>
        <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full p-6 space-y-4" @click.away="modalTambah = false" x-transition.scale>
            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                <h3 class="text-base font-bold text-gray-800">Kirim Laporan Baru</h3>
                <button @click="modalTambah = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form action="{{ route('pelaporan.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Target Kegiatan Wilayah</label>
                    <select name="id_target" required class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#005A9C]">
                        <option value="">-- Pilih Pekerjaan --</option>
                        @foreach($targets as $t)
                            <option value="{{ $t->id_target }}" @selected(old('id_target') == $t->id_target)>{{ $t->wilayah->nama_wilayah ?? '' }} - {{ $t->proses->nama_proses ?? '' }} (Target: {{ $t->target_kuantiti }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Lapor</label>
                    <input type="date" name="tanggal_lapor" required value="{{ old('tanggal_lapor', date('Y-m-d')) }}" class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#005A9C]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Realisasi Kuantiti</label>
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

    <!-- Modal Detail Laporan -->
    <div x-show="modalDetail" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4" style="display: none;" x-transition.opacity>
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 space-y-4" @click.away="modalDetail = false" x-transition.scale>
            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                <h3 class="text-base font-bold text-gray-800">Detail Laporan</h3>
                <button @click="modalDetail = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div class="space-y-3 text-sm">
                <div>
                    <span class="block text-xs text-gray-400 font-semibold">Tanggal Lapor</span>
                    <span class="font-bold text-gray-800" x-text="activeLaporan.tanggal_lapor"></span>
                </div>
                <div>
                    <span class="block text-xs text-gray-400 font-semibold">Pekerjaan & Wilayah</span>
                    <span class="text-gray-800 font-bold block" x-text="activeLaporan.target?.proses?.nama_proses || '-'"></span>
                    <span class="text-xs text-gray-500" x-text="activeLaporan.target?.wilayah?.nama_wilayah || '-'"></span>
                </div>
                <div>
                    <span class="block text-xs text-gray-400 font-semibold">Capaian (Realisasi)</span>
                    <span class="font-bold text-[#005A9C]" x-text="activeLaporan.realisasi_kuantiti + ' ' + (activeLaporan.target?.proses?.satuan_target || '')"></span>
                </div>
                <div>
                    <span class="block text-xs text-gray-400 font-semibold">Tautan Bukti</span>
                    <template x-if="activeLaporan.link_bukti">
                        <a :href="activeLaporan.link_bukti" target="_blank" class="text-blue-600 hover:underline break-all" x-text="activeLaporan.link_bukti"></a>
                    </template>
                    <template x-if="!activeLaporan.link_bukti">
                        <span class="text-gray-500 italic">Tidak ada tautan</span>
                    </template>
                </div>
                <div>
                    <span class="block text-xs text-gray-400 font-semibold">File Bukti</span>
                    <template x-if="activeLaporan.file_bukti">
                        <a :href="'/' + activeLaporan.file_bukti" target="_blank" class="text-blue-600 hover:underline">Lihat Dokumen / Gambar</a>
                    </template>
                    <template x-if="!activeLaporan.file_bukti">
                        <span class="text-gray-500 italic">Tidak ada file lampiran</span>
                    </template>
                </div>
                <div>
                    <span class="block text-xs text-gray-400 font-semibold">Catatan Verifikator</span>
                    <span class="text-gray-700 italic block p-2 bg-gray-50 rounded border border-gray-100" x-text="activeLaporan.catatan_verifikator || 'Belum ada catatan.'"></span>
                </div>
            </div>
            <div class="flex justify-end pt-4 border-t border-gray-100">
                <button type="button" @click="modalDetail = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold">Tutup</button>
            </div>
        </div>
    </div>

</div>
@endsection