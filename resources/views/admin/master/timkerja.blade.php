@extends('layouts.admin')

@section('title', 'Master Data: Tim Kerja')
@section('header', 'Master Data Tim Kerja')

@section('content')
<div class="space-y-6" x-data="{ modalTambah: false, selected: [], selectAll: false }">
    
    <div class="flex flex-wrap gap-4 justify-between items-center card-container">
        <h3 class="text-lg font-bold text-gray-800">Daftar Tim Kerja</h3>
        
        <div class="flex items-center gap-2">
            <form action="{{ route('admin.timkerja.index') }}" method="GET" class="flex gap-2 mr-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari tim kerja..." class="form-input w-48">
                <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-gray-700 shadow-sm">Cari</button>
                @if(request('search'))
                    <a href="{{ route('admin.timkerja.index') }}" class="bg-gray-200 text-gray-700 px-3 py-2 rounded-lg font-bold text-sm hover:bg-gray-300 flex items-center">Reset</a>
                @endif
            </form>

            <form action="{{ route('admin.timkerja.bulkDestroy') }}" method="POST" x-show="selected.length > 0" onsubmit="return confirm('Yakin ingin menghapus tim kerja yang dicentang?');" style="display: none;">
                @csrf @method('DELETE')
                <template x-for="id in selected"><input type="hidden" name="ids[]" :value="id"></template>
                <button type="submit" class="btn-action-delete flex items-center gap-2 px-4 py-2">
                    Hapus Terpilih (<span x-text="selected.length"></span>)
                </button>
            </form>

            <button @click="modalTambah = true" class="btn-teal">
                + Tambah Tim Kerja
            </button>
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition.duration.500ms class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- PENANGKAP ERROR VALIDASI -->
    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded-md shadow-sm">
            <div class="flex">
                <div class="ml-3">
                    <h3 class="text-sm font-bold text-red-800">Gagal Menyimpan Data:</h3>
                    <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="card-container overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-gray-50 border-b text-gray-600">
                    <th class="p-3 w-10 text-center"><input type="checkbox" x-model="selectAll" @change="selected = selectAll ? {{ json_encode($teams->pluck('id_team')->map(fn($id) => (string)$id)) }} : []" class="w-4 h-4 text-[#14B8A6] border-gray-300 rounded cursor-pointer"></th>
                    <th class="p-3 w-16 text-center">No</th>
                    <th class="p-3">Nama Tim Kerja</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($teams as $index => $item)
                <!-- Tambahkan modalDetail: false di x-data -->
                <tr class="border-b hover:bg-gray-50" x-data="{ modalDetail: false, modalEdit: false, modalHapus: false }">
                    <td class="p-3 text-center"><input type="checkbox" x-model="selected" value="{{ $item->id_team }}" class="w-4 h-4 text-[#14B8A6] border-gray-300 rounded cursor-pointer"></td>
                    <td class="p-3 text-center">{{ $index + 1 }}</td>
                    <td class="p-3 font-semibold text-gray-800">{{ $item->nama_team }}</td>
                    <td class="p-3 text-center whitespace-nowrap">
                        <!-- Tombol Detail -->
                        <button @click="modalDetail = true" class="bg-blue-500 text-white px-3 py-1 rounded text-xs font-bold mr-1 hover:bg-blue-600 transition-colors">Detail</button>
                        <button @click="modalEdit = true" class="btn-action-edit mr-1">Edit</button>
                        <button @click="modalHapus = true" class="btn-action-delete">Hapus</button>

                        <!-- Modal Detail (Aksi Read) -->
                        <div x-show="modalDetail" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 text-left" style="display: none;">
                            <div class="card-container max-w-md w-full shadow-xl" @click.away="modalDetail = false">
                                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Detail Tim Kerja</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="form-label text-gray-500">ID Tim Kerja</label>
                                        <div class="form-input bg-gray-50 font-semibold">{{ $item->id_team }}</div>
                                    </div>
                                    <div>
                                        <label class="form-label text-gray-500">Nama Tim Kerja</label>
                                        <div class="form-input bg-gray-50 font-semibold">{{ $item->nama_team }}</div>
                                    </div>
                                </div>
                                <div class="flex justify-end mt-6">
                                    <button type="button" @click="modalDetail = false" class="bg-gray-800 text-white px-4 py-2 rounded text-sm font-bold hover:bg-gray-700">Tutup</button>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Edit -->
                        <div x-show="modalEdit" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 text-left" style="display: none;">
                            <div class="card-container max-w-md w-full shadow-xl" @click.away="modalEdit = false">
                                <h3 class="text-lg font-bold text-gray-800 mb-4">Edit Tim Kerja</h3>
                                <form action="{{ route('admin.timkerja.update', $item->id_team) }}" method="POST" class="space-y-4">
                                    @csrf @method('PUT')
                                    <div>
                                        <label class="form-label">Nama Tim Kerja</label>
                                        <input type="text" name="nama_team" value="{{ $item->nama_team }}" required class="form-input">
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
                                <p class="text-sm text-gray-600 mb-6">Apakah Anda yakin ingin menghapus tim kerja ini?</p>
                                <form action="{{ route('admin.timkerja.destroy', $item->id_team) }}" method="POST" class="flex justify-center gap-2">
                                    @csrf @method('DELETE')
                                    <button type="button" @click="modalHapus = false" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-bold">Batal</button>
                                    <button type="submit" class="btn-action-delete">Ya, Hapus!</button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="p-6 text-center text-gray-400">Belum ada data tim kerja.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah -->
    <div x-show="modalTambah" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" style="display: none;">
        <div class="card-container max-w-md w-full shadow-xl" @click.away="modalTambah = false">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Tambah Tim Kerja</h3>
            <form action="{{ route('admin.timkerja.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="form-label">Nama Tim Kerja</label>
                    <input type="text" name="nama_team" required class="form-input" placeholder="Contoh: Tim IPDS...">
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