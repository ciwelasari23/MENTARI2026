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
                    <td class="p-3 font-medium text-gray-800">{{ $item->user->nama_lengkap ?? '-' }}</td>
                    <td class="p-3">
                        <span class="font-semibold text-gray-800">{{ $item->target->proses->nama_proses ?? '-' }}</span><br>
                        <span class="text-xs text-gray-500">{{ $item->target->wilayah->nama_wilayah ?? '-' }}</span>
                    </td>
                    <td class="p-3 font-semibold text-[#14B8A6]">{{ $item->realisasi_kuantiti }} {{ $item->target->proses->satuan_target ?? '' }}</td>
                    <td class="p-3">
                        @if($item->link_bukti)
                            <a href="{{ $item->link_bukti }}" target="_blank" class="text-blue-600 hover:underline font-medium text-xs">Lihat Link</a>
                        @else
                            <span class="text-gray-400 text-xs">Tidak ada</span>
                        @endif
                    </td>
                    <td class="p-3">
                        @if($item->status_laporan == 'pending')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs font-bold uppercase">Pending</span>
                        @elseif($item->status_laporan == 'approved')
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold uppercase">Disetujui</span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-bold uppercase">Ditolak</span>
                        @endif
                    </td>
                    <td class="p-3 text-center">
                        <button @click="modalOpen = true; currentId = '{{ $item->id_laporan }}'; currentStatus = '{{ $item->status_laporan }}'; currentCatatan = '{{ $item->catatan_verifikator }}';" 
                            class="bg-gray-800 text-white px-3 py-1 rounded text-xs font-semibold hover:bg-gray-700">
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
            
            <form :action="'/admin/verifikasi-laporan/' + currentId" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Status Laporan</label>
                    <select name="status_laporan" x-model="currentStatus" required class="w-full px-3 py-2 border rounded-lg outline-none text-sm">
                        <option value="approved">Setujui (Approved)</option>
                        <option value="rejected">Tolak (Rejected)</option>
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