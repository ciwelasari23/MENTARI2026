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

        <form action="{{ route('pelaporan.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                <p class="text-xs text-gray-500 mt-1">*Opsional, isi jika bukti berupa link/tautan</p>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Upload Bukti Dukung (File)</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-[#005A9C] transition-colors relative bg-gray-50">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600 justify-center items-center">
                            <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-[#005A9C] hover:text-[#004070] focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-[#005A9C] px-2 py-1">
                                <span>Klik untuk upload</span>
                                <input id="file-upload" name="file_bukti" type="file" class="sr-only" accept=".jpg,.jpeg,.png,.pdf">
                            </label>
                            <p class="pl-1">atau seret file ke sini</p>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            Format JPG, PNG, PDF Maks 1MB
                        </p>
                        <p id="file-name-display" class="text-xs text-[#005A9C] font-semibold mt-2 hidden"></p>
                    </div>
                </div>
                @error('file_bukti')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
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
<script>
    document.getElementById('file-upload').addEventListener('change', function(e) {
        var fileName = e.target.files[0] ? e.target.files[0].name : '';
        var display = document.getElementById('file-name-display');
        if(fileName) {
            display.textContent = 'File terpilih: ' + fileName;
            display.classList.remove('hidden');
        } else {
            display.classList.add('hidden');
        }
    });
</script>
@endsection