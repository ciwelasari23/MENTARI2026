@extends('layouts.admin')

@section('title', 'Master Data: Pengguna')
@section('header', 'Master Data Pengguna')

@section('content')
<div class="space-y-6" x-data="{ modalTambah: false, selected: [], selectAll: false }">
    
    <div class="flex flex-wrap gap-4 justify-between items-center card-container">
        <h3 class="text-lg font-bold text-gray-800">Daftar Pengguna Sistem</h3>
        
        <div class="flex items-center gap-2">
            <!-- Form Pencarian -->
            <form action="{{ route('admin.user.index') }}" method="GET" class="flex gap-2 mr-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/email/NIP..." class="form-input w-48">
                <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-gray-700 shadow-sm">Cari</button>
                @if(request('search'))
                    <a href="{{ route('admin.user.index') }}" class="bg-gray-200 text-gray-700 px-3 py-2 rounded-lg font-bold text-sm hover:bg-gray-300 flex items-center">Reset</a>
                @endif
            </form>

            <!-- Tombol Hapus Massal -->
            <form action="{{ route('admin.user.bulkDestroy') }}" method="POST" x-show="selected.length > 0" onsubmit="return confirm('Yakin ingin menghapus pengguna yang dicentang?');" style="display: none;">
                @csrf @method('DELETE')
                <template x-for="id in selected"><input type="hidden" name="ids[]" :value="id"></template>
                <button type="submit" class="btn-action-delete flex items-center gap-2 px-4 py-2">
                    Hapus Terpilih (<span x-text="selected.length"></span>)
                </button>
            </form>

            <button @click="modalTambah = true" class="btn-teal">
                + Tambah Pengguna
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
                    <th class="p-3 w-10 text-center"><input type="checkbox" x-model="selectAll" @change="selected = selectAll ? {{ json_encode($users->map(fn($item) => (string)$item->getKey())) }} : []" class="w-4 h-4 text-[#14B8A6] border-gray-300 rounded cursor-pointer"></th>
                    <th class="p-3 w-12 text-center">No</th>
                    <th class="p-3">Nama Lengkap</th>
                    <th class="p-3">NIP / Email</th>
                    <th class="p-3">Role / Hak Akses</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $index => $item)
                <tr class="border-b hover:bg-gray-50" x-data="{ modalEdit: false, modalHapus: false }">
                    <td class="p-3 text-center"><input type="checkbox" x-model="selected" value="{{ $item->getKey() }}" class="w-4 h-4 text-[#14B8A6] border-gray-300 rounded cursor-pointer"></td>
                    <td class="p-3 text-center">{{ $index + 1 }}</td>
                    <td class="p-3 font-semibold text-gray-800">{{ $item->nama_lengkap }}</td>
                    <td class="p-3 text-gray-600 text-xs">
                        <div>NIP: {{ $item->nip ?? '-' }}</div>
                        <div class="text-gray-500">{{ $item->email }}</div>
                    </td>
                    <td class="p-3">
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $item->role == 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-teal-100 text-teal-700' }}">
                            {{ ucfirst($item->role) }}
                        </span>
                    </td>
                    <td class="p-3 text-center whitespace-nowrap">
                        <button @click="modalEdit = true" class="btn-action-edit mr-1">Edit</button>
                        <button @click="modalHapus = true" class="btn-action-delete">Hapus</button>

                        <!-- Modal Edit -->
                        <div x-show="modalEdit" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 text-left" style="display: none;">
                            <div class="card-container max-w-lg w-full shadow-xl" @click.away="modalEdit = false">
                                <h3 class="text-lg font-bold text-gray-800 mb-4">Edit Pengguna</h3>
                                <form action="{{ route('admin.user.update', $item->getKey()) }}" method="POST" class="space-y-4">
                                    @csrf @method('PUT')
                                    <div>
                                        <label class="form-label">Nama Lengkap</label>
                                        <input type="text" name="nama_lengkap" value="{{ $item->nama_lengkap }}" required class="form-input">
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="form-label">NIP</label>
                                            <input type="text" name="nip" value="{{ $item->nip }}" class="form-input">
                                        </div>
                                        <div>
                                            <label class="form-label">Email</label>
                                            <input type="email" name="email" value="{{ $item->email }}" required class="form-input">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="form-label">Role</label>
                                            <select name="role" required class="form-input">
                                                <option value="admin" {{ $item->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                                <option value="petugas" {{ $item->role == 'petugas' ? 'selected' : '' }}>Petugas / Mitra</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="form-label">Password Baru (Opsional)</label>
                                            <input type="password" name="password" placeholder="Kosongkan jika tidak diubah" class="form-input">
                                        </div>
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
                                <p class="text-sm text-gray-600 mb-6">Apakah Anda yakin ingin menghapus pengguna ini?</p>
                                <form action="{{ route('admin.user.destroy', $item->getKey()) }}" method="POST" class="flex justify-center gap-2">
                                    @csrf @method('DELETE')
                                    <button type="button" @click="modalHapus = false" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-bold">Batal</button>
                                    <button type="submit" class="btn-action-delete">Ya, Hapus!</button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="p-6 text-center text-gray-400">Belum ada data pengguna.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah -->
    <div x-show="modalTambah" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" style="display: none;">
        <div class="card-container max-w-lg w-full shadow-xl" @click.away="modalTambah = false">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Tambah Pengguna Baru</h3>
            <form action="{{ route('admin.user.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" required class="form-input" placeholder="Masukkan nama lengkap...">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">NIP</label>
                        <input type="text" name="nip" class="form-input" placeholder="Opsional...">
                    </div>
                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" name="email" required class="form-input" placeholder="email@bps.go.id...">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Role</label>
                        <select name="role" required class="form-input">
                            <option value="admin">Admin</option>
                            <option value="petugas">Petugas / Mitra</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Password</label>
                        <input type="password" name="password" required class="form-input" placeholder="Minimal 6 karakter...">
                    </div>
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