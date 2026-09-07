@extends('layouts.admin')

@section('title', 'Master Data: Role')
@section('header', 'Master Data Role')

@section('content')
<div class="space-y-6" x-data="{ modalTambah: false }">
    <div class="flex justify-between items-center card-container">
        <h3 class="text-lg font-bold text-gray-800">Daftar Role Pengguna</h3>
        <button @click="modalTambah = true" class="btn-teal">+ Tambah Role</button>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" class="alert-success">
            {{ session('success') }}
        </div>
    @endif

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
                    <th class="p-3 w-16 text-center">No</th>
                    <th class="p-3">Nama Role</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $index => $item)
                <tr class="border-b hover:bg-gray-50" x-data="{ modalDetail: false, modalEdit: false, modalAkses: false, modalHapus: false }">
                    <td class="p-3 text-center">{{ $index + 1 }}</td>
                    <td class="p-3 font-semibold text-gray-800">{{ $item->nama_role }}</td>
                    <td class="p-3 text-center whitespace-nowrap">
                        
                        <!-- Tombol Atur Akses -->
                        <button @click="modalAkses = true" class="bg-indigo-600 text-white px-3 py-1 rounded text-xs font-bold mr-1 hover:bg-indigo-700 transition-colors">Atur Akses</button>
                        
                        <!-- Tombol Detail -->
                        <button @click="modalDetail = true" class="bg-blue-500 text-white px-3 py-1 rounded text-xs font-bold mr-1 hover:bg-blue-600 transition-colors">Detail</button>
                        <button @click="modalEdit = true" class="btn-action-edit mr-1">Edit</button>
                        <button @click="modalHapus = true" class="btn-action-delete">Hapus</button>

                        <!-- Modal Atur Akses Hak Izin (Permissions) -->
                        <div x-show="modalAkses" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 text-left" style="display: none;">
                            <div class="card-container max-w-lg w-full shadow-xl" @click.away="modalAkses = false">
                                <h3 class="text-lg font-bold text-gray-800 mb-2 border-b pb-2">Atur Hak Akses: {{ $item->nama_role }}</h3>
                                <p class="text-xs text-gray-500 mb-4">Pilih menu dan fitur yang diizinkan untuk diakses atau dikelola oleh role ini.</p>
                                
                                <form action="{{ route('admin.role.permissions', $item->id_role) }}" method="POST" class="space-y-4">
                                    @csrf
                                    <div class="space-y-2 max-h-64 overflow-y-auto pr-2 border p-3 rounded-md bg-gray-50 text-xs">
                                        @foreach($permissions as $perm)
                                            <label class="flex items-center gap-2 p-2 hover:bg-white rounded cursor-pointer">
                                                <input type="checkbox" name="permissions[]" value="{{ $perm->id_permission }}" 
                                                    {{ $item->permissions->contains('id_permission', $perm->id_permission) ? 'checked' : '' }}
                                                    class="w-4 h-4 text-[#14B8A6] rounded border-gray-300">
                                                <span class="font-semibold text-gray-700">{{ $perm->display_name }}</span>
                                            </label>
                                        @endforeach
                                    </div>

                                    <div class="flex justify-end gap-2 mt-6">
                                        <button type="button" @click="modalAkses = false" class="bg-gray-200 px-4 py-2 rounded text-sm font-bold text-gray-700">Batal</button>
                                        <button type="submit" class="btn-teal px-4 py-2">Simpan Hak Akses</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Modal Detail -->
                        <div x-show="modalDetail" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 text-left" style="display: none;">
                            <div class="card-container max-w-sm w-full shadow-xl" @click.away="modalDetail = false">
                                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Detail Role</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="form-label text-gray-500">ID Role</label>
                                        <div class="form-input bg-gray-50 font-semibold">{{ $item->id_role }}</div>
                                    </div>
                                    <div>
                                        <label class="form-label text-gray-500">Nama Role</label>
                                        <div class="form-input bg-gray-50 font-semibold">{{ $item->nama_role }}</div>
                                    </div>
                                </div>
                                <div class="flex justify-end mt-6">
                                    <button type="button" @click="modalDetail = false" class="bg-gray-800 text-white px-4 py-2 rounded text-sm font-bold hover:bg-gray-700">Tutup</button>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Edit -->
                        <div x-show="modalEdit" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 text-left" style="display: none;">
                            <div class="card-container max-w-sm w-full shadow-xl" @click.away="modalEdit = false">
                                <h3 class="text-lg font-bold text-gray-800 mb-4">Edit Role</h3>
                                <form action="{{ route('admin.role.update', $item->id_role) }}" method="POST" class="space-y-4">
                                    @csrf @method('PUT')
                                    <div>
                                        <label class="form-label text-xs font-bold text-gray-700">Nama Role</label>
                                        <input type="text" name="nama_role" value="{{ $item->nama_role }}" required class="form-input">
                                    </div>
                                    <div class="flex justify-end gap-2 mt-6">
                                        <button type="button" @click="modalEdit = false" class="bg-gray-200 px-4 py-2 rounded text-sm font-bold">Batal</button>
                                        <button type="submit" class="btn-action-edit px-4 py-2">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Modal Hapus -->
                        <div x-show="modalHapus" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 text-center" style="display: none;">
                            <div class="card-container max-w-sm w-full shadow-xl" @click.away="modalHapus = false">
                                <h3 class="text-lg font-bold text-gray-800 mb-2 mt-2">Konfirmasi Hapus</h3>
                                <p class="text-sm text-gray-600 mb-6">Hapus role ini?</p>
                                <form action="{{ route('admin.role.destroy', $item->id_role) }}" method="POST" class="flex justify-center gap-2">
                                    @csrf @method('DELETE')
                                    <button type="button" @click="modalHapus = false" class="bg-gray-200 px-4 py-2 rounded text-sm font-bold">Batal</button>
                                    <button type="submit" class="btn-action-delete px-4 py-2">Ya, Hapus!</button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="p-6 text-center text-gray-400">Belum ada data role.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah -->
    <div x-show="modalTambah" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 text-left" style="display: none;">
        <div class="card-container max-w-lg w-full shadow-xl" @click.away="modalTambah = false">
            <h3 class="text-lg font-bold text-gray-800 mb-2 border-b pb-2">Tambah Role Baru</h3>
            <p class="text-xs text-gray-500 mb-4">Masukkan nama role dan tentukan hak akses awalnya di bawah ini.</p>
            
            <form action="{{ route('admin.role.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="form-label text-xs font-bold text-gray-700">Nama Role</label>
                    <input type="text" name="nama_role" required class="form-input mt-1" placeholder="Contoh: Admin Kabupaten...">
                </div>

                <div>
                    <label class="form-label text-xs font-bold text-gray-700 mb-1 block">Pilih Hak Akses (Permissions)</label>
                    <div class="space-y-2 max-h-48 overflow-y-auto pr-2 border p-3 rounded-md bg-gray-50 text-xs">
                        @foreach($permissions as $perm)
                            <label class="flex items-center gap-2 p-2 hover:bg-white rounded cursor-pointer">
                                <input type="checkbox" name="permissions[]" value="{{ $perm->id_permission }}" 
                                    class="w-4 h-4 text-[#14B8A6] rounded border-gray-300">
                                <span class="font-semibold text-gray-700">{{ $perm->display_name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="modalTambah = false" class="bg-gray-200 px-4 py-2 rounded text-sm font-bold text-gray-700">Batal</button>
                    <button type="submit" class="btn-teal px-4 py-2">Simpan Role</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection