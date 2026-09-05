@extends('layouts.admin')

@section('title', 'Level 2: Kegiatan')
@section('header', 'Kelola Kegiatan Level 2')

@section('content')
<div class="space-y-6" x-data="{ modalTambah: false, selected: [], selectAll: false }">
    
    <div class="flex flex-wrap gap-4 justify-between items-center card-container">
        <h3 class="text-lg font-bold text-gray-800">Daftar Kegiatan (Level 2)</h3>
        
        <div class="flex items-center gap-2">
            <!-- Form Pencarian -->
            <form action="{{ route('admin.level2.index') }}" method="GET" class="flex gap-2 mr-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kegiatan..." class="form-input w-48">
                <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-gray-700 shadow-sm">Cari</button>
                @if(request('search'))
                    <a href="{{ route('admin.level2.index') }}" class="bg-gray-200 text-gray-700 px-3 py-2 rounded-lg font-bold text-sm hover:bg-gray-300 flex items-center">Reset</a>
                @endif
            </form>

            <!-- Tombol Hapus Massal -->
            <form action="{{ route('admin.level2.bulkDestroy') }}" method="POST" x-show="selected.length > 0" onsubmit="return confirm('Yakin ingin menghapus semua data yang dicentang? Semua data di bawahnya (Level 3 & 4) bisa ikut terhapus.');" style="display: none;">
                @csrf @method('DELETE')
                <template x-for="id in selected"><input type="hidden" name="ids[]" :value="id"></template>
                <button type="submit" class="btn-action-delete flex items-center gap-2 px-4 py-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Hapus Terpilih (<span x-text="selected.length"></span>)
                </button>
            </form>

            <button @click="modalTambah = true" class="btn-teal">
                + Tambah Kegiatan
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
                    <th class="p-3 w-10 text-center"><input type="checkbox" x-model="selectAll" @change="selected = selectAll ? {{ json_encode($kegiatan->pluck('id_kegiatan')->map(fn($id) => (string)$id)) }} : []" class="w-4 h-4 text-[#14B8A6] border-gray-300 rounded cursor-pointer"></th>
                    <th class="p-3 w-16 text-center">No</th>
                    <th class="p-3">Output Induk (Level 1)</th>
                    <th class="p-3">Nama Kegiatan (Level 2)</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kegiatan as $index => $item)
                <tr class="border-b hover:bg-gray-50" x-data="{ modalEdit: false, modalDetail: false, modalHapus: false }">
                    <td class="p-3 text-center"><input type="checkbox" x-model="selected" value="{{ $item->id_kegiatan }}" class="w-4 h-4 text-[#14B8A6] border-gray-300 rounded cursor-pointer"></td>
                    <td class="p-3 text-center">{{ $index + 1 }}</td>
                    <td class="p-3 text-gray-600">{{ $item->output->nama_output ?? '-' }}</td>
                    <td class="p-3 font-semibold text-gray-800">{{ $item->nama_kegiatan }}</td>
                    <td class="p-3 text-center whitespace-nowrap">
                        <button @click="modalDetail = true" class="btn-action-detail mr-1">Detail</button>
                        <button @click="modalEdit = true" class="btn-action-edit mr-1">Edit</button>
                        <button @click="modalHapus = true" class="btn-action-delete">Hapus</button>

                        <!-- Modal Detail -->
                        <div x-show="modalDetail" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 text-left" style="display: none;">
                            <div class="card-container max-w-md w-full shadow-xl" @click.away="modalDetail = false">
                                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Detail Kegiatan Level 2</h3>
                                <div class="space-y-4">
                                    <div><label class="form-label">Output Induk</label><div class="form-input bg-gray-50">{{ $item->output->nama_output ?? '-' }}</div></div>
                                    <div><label class="form-label">Nama Kegiatan</label><div class="form-input bg-gray-50">{{ $item->nama_kegiatan }}</div></div>
                                </div>
                                <div class="flex justify-end mt-6"><button type="button" @click="modalDetail = false" class="bg-gray-800 text-white px-4 py-2 rounded text-sm font-bold hover:bg-gray-700">Tutup</button></div>
                            </div>
                        </div>

                        <!-- Modal Edit -->
                        <div x-show="modalEdit" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 text-left" style="display: none;">
                            <div class="card-container max-w-md w-full shadow-xl" @click.away="modalEdit = false">
                                <h3 class="text-lg font-bold text-gray-800 mb-4">Edit Kegiatan Level 2</h3>
                                <form action="{{ route('admin.level2.update', $item->id_kegiatan) }}" method="POST" class="space-y-4">
                                    @csrf @method('PUT')
                                    <div><label class="form-label">Pilih Output Induk</label>
                                        <select name="id_output" required class="form-input">
                                            @foreach($outputs as $out)
                                                <option value="{{ $out->id_output }}" {{ $item->id_output == $out->id_output ? 'selected' : '' }}>{{ $out->nama_output }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div><label class="form-label">Nama Kegiatan</label><input type="text" name="nama_kegiatan" value="{{ $item->nama_kegiatan }}" required class="form-input"></div>
                                    <div class="flex justify-end gap-2 mt-6">
                                        <button type="button" @click="modalEdit = false" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-bold">Batal</button>
                                        <button type="submit" class="btn-action-edit">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Modal Hapus -->
                        <div x-show="modalHapus" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 text-center whitespace-normal" style="display: none;">
                            <div class="card-container max-w-sm w-full shadow-xl" @click.away="modalHapus = false">
                                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-800 mb-2">Konfirmasi Hapus</h3>
                                <p class="text-sm text-gray-600 mb-6">Apakah Anda yakin ingin menghapus data ini? Semua data di bawahnya (Level 3 & 4) bisa ikut terhapus.</p>
                                <form action="{{ route('admin.level2.destroy', $item->id_kegiatan) }}" method="POST" class="flex justify-center gap-2">
                                    @csrf @method('DELETE')
                                    <button type="button" @click="modalHapus = false" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-bold">Batal</button>
                                    <button type="submit" class="btn-action-delete">Ya, Hapus!</button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-6 text-center text-gray-400">@if(request('search')) Pencarian tidak ditemukan. @else Belum ada data. @endif</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah -->
    <div x-show="modalTambah" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" style="display: none;">
        <div class="card-container max-w-md w-full shadow-xl" @click.away="modalTambah = false">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Tambah Kegiatan Level 2</h3>
            <form action="{{ route('admin.level2.store') }}" method="POST" class="space-y-4">
                @csrf
                <div><label class="form-label">Pilih Output Induk</label>
                    <select name="id_output" required class="form-input">
                        <option value="">-- Pilih Output --</option>
                        @foreach($outputs as $out)
                            <option value="{{ $out->id_output }}">{{ $out->nama_output }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="form-label">Nama Kegiatan</label><input type="text" name="nama_kegiatan" required class="form-input"></div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="modalTambah = false" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-bold">Batal</button>
                    <button type="submit" class="btn-teal">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection