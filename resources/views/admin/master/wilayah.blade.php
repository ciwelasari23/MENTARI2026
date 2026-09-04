@extends('layouts.app')

@section('title', 'Master Data: Wilayah')
@section('header', 'Kelola Master Data Wilayah')

@section('content')
<div class="space-y-6" x-data="{ modalTambah: false, selected: [], selectAll: false }">
    
    <div class="flex flex-wrap gap-4 justify-between items-center card-container">
        <h3 class="text-lg font-bold text-gray-800">Daftar Wilayah</h3>
        
        <div class="flex items-center gap-2">
            <!-- Form Pencarian -->
            <form action="{{ route('admin.wilayah.index') }}" method="GET" class="flex gap-2 mr-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari wilayah..." class="form-input w-48">
                <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-gray-700 shadow-sm">Cari</button>
                @if(request('search'))
                    <a href="{{ route('admin.wilayah.index') }}" class="bg-gray-200 text-gray-700 px-3 py-2 rounded-lg font-bold text-sm hover:bg-gray-300 flex items-center">Reset</a>
                @endif
            </form>

            <!-- Tombol Hapus Massal -->
            <form action="{{ route('admin.wilayah.bulkDestroy') }}" method="POST" x-show="selected.length > 0" onsubmit="return confirm('Yakin ingin menghapus wilayah yang dicentang?');" style="display: none;">
                @csrf @method('DELETE')
                <template x-for="id in selected"><input type="hidden" name="ids[]" :value="id"></template>
                <button type="submit" class="btn-action-delete flex items-center gap-2 px-4 py-2">
                    Hapus Terpilih (<span x-text="selected.length"></span>)
                </button>
            </form>

            <button @click="modalTambah = true" class="btn-teal">
                + Tambah Wilayah
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
                    <th class="p-3 w-10 text-center"><input type="checkbox" x-model="selectAll" @change="selected = selectAll ? {{ json_encode($wilayahs->pluck('id_wilayah')->map(fn($id) => (string)$id)) }} : []" class="w-4 h-4 text-[#14B8A6] border-gray-300 rounded cursor-pointer"></th>
                    <th class="p-3 w-16 text-center">No</th>
                    <th class="p-3">ID Wilayah</th>
                    <th class="p-3">Kode Wilayah</th>
                    <th class="p-3">Nama Wilayah</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
 <tbody>
                @forelse($wilayahs as $index => $item)
                <tr class="border-b hover:bg-gray-50" x-data="{ modalDetail: false, modalEdit: false, modalHapus: false }">
                    <td class="p-3 text-center"><input type="checkbox" x-model="selected" value="{{ $item->id_wilayah }}" class="w-4 h-4 text-[#14B8A6] border-gray-300 rounded cursor-pointer"></td>
                    <td class="p-3 text-center">{{ $index + 1 }}</td>
                    <td class="p-3 font-medium text-gray-600">{{ $item->id_wilayah }}</td>
                    <td class="p-3 font-medium text-gray-600">{{ $item->kode_wilayah }}</td>
                    <td class="p-3 font-semibold text-gray-800">{{ $item->nama_wilayah }}</td>
                    <td class="p-3 text-center whitespace-nowrap">
                        <button @click="modalDetail = true" class="bg-blue-500 text-white px-3 py-1 rounded text-xs font-bold mr-1 hover:bg-blue-600">Detail</button>
                        <button @click="modalEdit = true" class="btn-action-edit mr-1">Edit</button>
                        <button @click="modalHapus = true" class="btn-action-delete">Hapus</button>

                        <!-- Modal Detail (Read) -->
                        <div x-show="modalDetail" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 text-left" style="display: none;">
                            <div class="card-container max-w-md w-full shadow-xl" @click.away="modalDetail = false">
                                <h3 class="text-lg font-bold text-gray-800 mb-4">Detail Wilayah</h3>
                                <div class="space-y-3 text-sm">
                                    <div>
                                        <span class="text-gray-500 font-medium block">ID Wilayah</span>
                                        <p class="text-gray-800 font-semibold">{{ $item->id_wilayah }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 font-medium block">Kode Wilayah</span>
                                        <p class="text-gray-800 font-semibold">{{ $item->kode_wilayah }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 font-medium block">Nama Wilayah</span>
                                        <p class="text-gray-800 font-semibold">{{ $item->nama_wilayah }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 font-medium block">Level Wilayah</span>
                                        <p class="text-gray-800 font-semibold">{{ $item->level_wilayah }}</p>
                                    </div>
                                </div>
                                <div class="flex justify-end mt-6">
                                    <button type="button" @click="modalDetail = false" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-bold hover:bg-gray-300">Tutup</button>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Edit -->
                        <div x-show="modalEdit" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 text-left" style="display: none;">
                            <div class="card-container max-w-md w-full shadow-xl" @click.away="modalEdit = false">
                                <h3 class="text-lg font-bold text-gray-800 mb-4">Edit Wilayah</h3>
                                <form action="{{ route('admin.wilayah.update', $item->id_wilayah) }}" method="POST" class="space-y-4">
                                    @csrf @method('PUT')
                                    <div>
                                        <label class="form-label">ID Wilayah</label>
                                        <input type="text" name="id_wilayah" value="{{ $item->id_wilayah }}" required class="form-input">
                                    </div>
                                    <div>
                                        <label class="form-label">Kode Wilayah</label>
                                        <input type="text" name="kode_wilayah" value="{{ $item->kode_wilayah }}" required class="form-input">
                                    </div>
                                    <div>
                                        <label class="form-label">Nama Wilayah</label>
                                        <input type="text" name="nama_wilayah" value="{{ $item->nama_wilayah }}" required class="form-input">
                                    </div>
                                    <div>
                                        <label class="form-label">Level Wilayah</label>
                                        <input type="number" name="level_wilayah" value="{{ $item->level_wilayah }}" required class="form-input">
                                    </div>
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
                                <h3 class="text-lg font-bold text-gray-800 mb-2 mt-2">Konfirmasi Hapus</h3>
                                <p class="text-sm text-gray-600 mb-6">Apakah Anda yakin ingin menghapus wilayah ini?</p>
                                <form action="{{ route('admin.wilayah.destroy', $item->id_wilayah) }}" method="POST" class="flex justify-center gap-2">
                                    @csrf @method('DELETE')
                                    <button type="button" @click="modalHapus = false" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-bold">Batal</button>
                                    <button type="submit" class="btn-action-delete">Ya, Hapus!</button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="p-6 text-center text-gray-400">Belum ada data wilayah.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah -->
    <div x-show="modalTambah" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" style="display: none;">
        <div class="card-container max-w-md w-full shadow-xl" @click.away="modalTambah = false">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Tambah Wilayah</h3>
            <form action="{{ route('admin.wilayah.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="form-label">ID Wilayah</label>
                    <input type="text" name="id_wilayah" required class="form-input" placeholder="Contoh: 1400...">
                </div>
                <div>
                    <label class="form-label">Kode Wilayah</label>
                    <input type="text" name="kode_wilayah" required class="form-input" placeholder="Contoh: 1471...">
                </div>
                <div>
                    <label class="form-label">Nama Wilayah</label>
                    <input type="text" name="nama_wilayah" required class="form-input" placeholder="Contoh: Kota Pekanbaru...">
                </div>
                <div>
                    <label class="form-label">Level Wilayah</label>
                    <input type="number" name="level_wilayah" required class="form-input" placeholder="Contoh: 1 / 2 / 3">
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