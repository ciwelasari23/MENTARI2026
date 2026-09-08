@extends('layouts.admin')

@section('title', 'Form Pelaporan Lapangan')
@section('header', 'Pelaporan Realisasi Pekerjaan')

@section('content')
<div class="space-y-6">
    <!-- Form Input Laporan -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Kirim Laporan Baru</h3>
        
        @if(session('success'))
            <div class="bg-green-50 text-green-700 p-3 rounded mb-4 text-sm">{{ session('success') }}</div>
        @endif

        <form action="{{ route('pelaporan.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Pilih Target Kegiatan Wilayah</label>
                <select name="id_target" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-[#005A9C] outline-none text-sm">
                    <option value="">-- Pilih Pekerjaan --</option>
                    @foreach($targets as $t)
                        <option value="{{ $t->id_target }}">{{ $t->wilayah->nama_wilayah ?? '' }} - {{ $t->proses->nama_proses ?? '' }} (Target: {{ $t->target_kuantiti }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tanggal Lapor</label>
                <input type="date" name="tanggal_lapor" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-[#005A9C] outline-none text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Realisasi Kuantiti</label>
                <input type="number" name="realisasi_kuantiti" min="1" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-[#005A9C] outline-none text-sm">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tautan Bukti Dukung (G-Drive / Link)</label>
                <input type="url" name="link_bukti" placeholder="https://..." class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-[#005A9C] outline-none text-sm">
            </div>
            <div class="md:col-span-2 flex justify-end">
                <button type="submit" class="bg-[#005A9C] text-white px-5 py-2 rounded-lg font-bold text-sm hover:bg-[#004070] transition-colors shadow">
                    Kirim Laporan
                </button>
            </div>
        </form>
    </div>

    <!-- Riwayat Laporan -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 overflow-x-auto">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Riwayat Laporan Anda</h3>
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-gray-50 border-b text-gray-600">
                    <th class="p-3">Tanggal</th>
                    <th class="p-3">Pekerjaan & Wilayah</th>
                    <th class="p-3">Capaian</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Catatan Verifikator</th>
                </tr>
            </thead>
            <tbody>
                @forelse($laporan as $item)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3 whitespace-nowrap">{{ $item->tanggal_lapor }}</td>
                    <td class="p-3 font-semibold text-gray-800">
                        {{ $item->target->proses->nama_proses ?? '-' }}<br>
                        <span class="text-xs text-gray-500 font-normal">{{ $item->target->wilayah->nama_wilayah ?? '-' }}</span>
                    </td>
                    <td class="p-3 font-medium text-[#005A9C]">{{ $item->realisasi_kuantiti }} {{ $item->target->proses->satuan_target ?? '' }}</td>
                    <td class="p-3">
                        @if($item->status_laporan == 'pending')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs font-bold uppercase">Pending</span>
                        @elseif($item->status_laporan == 'approved')
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold uppercase">Disetujui</span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-bold uppercase">Ditolak</span>
                        @endif
                    </td>
                    <td class="p-3 text-gray-600 text-xs italic">{{ $item->catatan_verifikator ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-4 text-center text-gray-400">Belum ada riwayat laporan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection