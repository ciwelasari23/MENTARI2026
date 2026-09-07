@extends('layouts.admin')

@section('title', 'Target Wilayah')
@section('header', 'Pengelolaan Target Wilayah')

@section('content')
<div class="space-y-6" x-data="{ modalTambah: false, selected: [], selectAll: false }">
    
    <div class="flex flex-wrap gap-4 justify-between items-center card-container">
        <h3 class="text-lg font-bold text-gray-800">Daftar Target Wilayah</h3>
        
        <div class="flex items-center gap-2">
            <!-- Tombol Tambah -->
            <button @click="modalTambah = true" class="btn-teal">
                + Tambah Target Wilayah
            </button>
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition.duration.500ms class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card-container overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-gray-50 border-b text-gray-600">
                    <th class="p-3 w-16 text-center">No</th>
                    <th class="p-3">Nama Proses (Kegiatan)</th>
                    <th class="p-3">Wilayah</th>
                    <th class="p-3 text-center">Target Kuantiti</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($targets as $index => $item)
                <tr class="border-b hover:bg-gray-50" x-data="{ modalHapus: false }">
                    <td class="p-3 text-center">{{ $index + 1 }}</td>
                    <td class="p-3 font-medium text-gray-800">{{ $item->proses->nama_proses ?? 'Proses tidak ditemukan' }}</td>
                    <td class="p-3 font-medium text-gray-600">{{ $item->wilayah->nama_wilayah ?? 'Wilayah tidak ditemukan' }}</td>
                    <td class="p-3 font-semibold text-gray-800 text-center">{{ $item->target_kuantiti }}</td>
                    <td class="p-3 text-center whitespace-nowrap">
                        <button @click="modalHapus = true" class="btn-action-delete">Hapus</button>

                        <!-- Modal Hapus -->
                        <div x-show="modalHapus" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 text-center whitespace-normal" style="display: none;">
                            <div class="card-container max-w-sm w-full shadow-xl" @click.away="modalHapus = false">
                                <h3 class="text-lg font-bold text-gray-800 mb-2 mt-2">Konfirmasi Hapus</h3>
                                <p class="text-sm text-gray-600 mb-6">Apakah Anda yakin ingin menghapus target wilayah ini?</p>
                                <form action="{{ route('admin.target.destroy', $item->id_target) }}" method="POST" class="flex justify-center gap-2">
                                    @csrf @method('DELETE')
                                    <button type="button" @click="modalHapus = false" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-bold">Batal</button>
                                    <button type="submit" class="btn-action-delete text-[0.875rem] px-4 py-2 rounded-lg">Ya, Hapus!</button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-6 text-center text-gray-400">Belum ada data target wilayah.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah Target -->
    <div x-show="modalTambah" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" style="display: none;">
        <div class="card-container max-w-md w-full shadow-xl" @click.away="modalTambah = false">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Tambah Target Wilayah</h3>
            <form action="{{ route('admin.target.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="form-label text-xs font-bold text-gray-700">Proses Kegiatan</label>
                    <select name="id_proses" required class="form-input w-full px-3 py-2 border rounded-md">
                        <option value="">-- Pilih Proses --</option>
                        @foreach($prosesList as $proses)
                            <option value="{{ $proses->id_proses }}">{{ $proses->nama_proses }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label text-xs font-bold text-gray-700">Wilayah</label>
                    <select name="id_wilayah" required class="form-input w-full px-3 py-2 border rounded-md">
                        <option value="">-- Pilih Wilayah --</option>
                        @foreach($wilayahList as $wilayah)
                            <option value="{{ $wilayah->id_wilayah }}">{{ $wilayah->nama_wilayah }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label text-xs font-bold text-gray-700">Target Kuantiti</label>
                    <input type="number" name="target_kuantiti" required min="1" class="form-input w-full px-3 py-2 border rounded-md" placeholder="Contoh: 100">
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="modalTambah = false" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-bold">Batal</button>
                    <button type="submit" class="btn-teal">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
