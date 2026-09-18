@extends('layouts.admin')

@section('title', 'Verifikasi Laporan Petugas')
@section('header', 'Verifikasi Laporan Lapangan')

@section('content')
<div class="space-y-6" x-data="{ modalOpen: false, currentId: null, currentStatus: 'approved', currentCatatan: '' }">
    
    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-3 rounded text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 overflow-x-auto">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Daftar Masuk Laporan Petugas / Mitra</h3>
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-gray-50 border-b text-gray-600">
                    <th class="p-3">Tanggal</th>
                    <th class="p-3">Petugas</th>
                    <th class="p-3">Pekerjaan & Wilayah</th>
                    <th class="p-3">Realisasi</th>
                    <th class="p-3">Bukti</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($laporans as $item)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3 whitespace-nowrap">{{ $item->tanggal_lapor }}</td>
                    <td class="p-3 font-medium text-gray-800">{{ $item->pelapor->nama_lengkap ?? '-' }}</td>
                    <td class="p-3">
                        <span class="font-semibold text-gray-800">{{ $item->targetWilayah->proses->nama_proses ?? '-' }}</span><br>
                        <span class="text-xs text-gray-500">{{ $item->targetWilayah->wilayah->nama_provinsi ?? '-' }} {{ $item->targetWilayah->wilayah->kode_nama_kabkota ?? '-' }}</span>
                    </td>
                    <td class="p-3 font-semibold text-[#14B8A6]">{{ $item->realisasi_saat_ini }} {{ $item->targetWilayah->proses->satuan_target ?? '' }}</td>
                    <td class="p-3">
                        @if($item->link_bukti)
                            <a href="{{ $item->link_bukti }}" target="_blank" class="text-blue-600 hover:underline font-medium text-xs block">Lihat Tautan</a>
                        @endif
                        @if($item->file_bukti)
                            <a href="{{ asset('uploads/bukti/' . $item->file_bukti) }}" target="_blank" class="text-emerald-600 hover:underline font-medium text-xs block">Lihat File</a>
                        @endif
                        @if(!$item->link_bukti && !$item->file_bukti)
                            <span class="text-gray-400 text-xs">Tidak ada</span>
                        @endif
                    </td>
                    <td class="p-3">
                        @if($item->status_laporan == 'pending')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs font-bold uppercase">Diajukan</span>
                        @elseif($item->status_laporan == 'approved')
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold uppercase">Disetujui</span>
                        @elseif($item->status_laporan == 'revision')
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-bold uppercase">Perlu Revisi</span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-bold uppercase">Ditolak</span>
                        @endif
                    </td>
                    <td class="p-3 text-center">
                        <button @click="modalOpen = true; currentId = '{{ $item->id_laporan }}'; currentStatus = '{{ $item->status_laporan }}'; currentCatatan = '{{ $item->catatan_verifikasi }}';" 
                            class="flex items-center justify-center gap-1 text-teal-600 hover:text-teal-800 bg-teal-50 hover:bg-teal-100 px-3 py-1.5 rounded-md transition-colors text-xs font-bold mx-auto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Verifikasi
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-4 text-center text-gray-400">Belum ada laporan masuk dari lapangan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Verifikasi -->
    <div x-show="modalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" style="display: none;">
        <div class="bg-white p-6 rounded-lg max-w-md w-full space-y-4">
            <h3 class="text-lg font-bold text-gray-800">Proses Verifikasi Laporan</h3>
            
            <form x-bind:action="currentId ? '{{ route('admin.verifikasi.update', ['id' => '__ID__']) }}'.replace('__ID__', currentId) : '#'" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Status Laporan</label>
                    <select name="status_laporan" x-model="currentStatus" required class="w-full px-3 py-2 border rounded-lg outline-none text-sm">
                        <option value="pending">Diajukan (Pending)</option>
                        <option value="approved">Disetujui (Approved)</option>
                        <option value="revision">Perlu Revisi (Revision)</option>
                        <option value="rejected">Ditolak (Rejected)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Catatan Verifikator</label>
                    <textarea name="catatan_verifikator" x-model="currentCatatan" rows="3" placeholder="Berikan catatan jika diperlukan..." class="w-full px-3 py-2 border rounded-lg outline-none text-sm"></textarea>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="modalOpen = false" class="bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm font-bold">Batal</button>
                    <button type="submit" class="bg-[#14B8A6] text-white px-4 py-2 rounded text-sm font-bold hover:bg-teal-600">Simpan Verifikasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection