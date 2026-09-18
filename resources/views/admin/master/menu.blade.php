@extends('layouts.admin')

@section('title', 'Master Data: Menu')
@section('header', 'Manajemen Menu & Modul Sistem')

@section('content')
<div class="space-y-6" x-data="{ modalTambah: false }">
    <div class="flex justify-between items-center card-container">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Daftar Menu Aplikasi</h3>
            <p class="text-xs text-gray-500">Menu yang ditambahkan akan otomatis menjadi modul Hak Akses.</p>
        </div>
        <button @click="modalTambah = true" class="bg-[#10b981] hover:bg-emerald-600 text-white px-5 py-2 rounded-md text-sm font-semibold transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Menu
        </button>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-lg shadow-sm text-sm break-words relative pr-8">
            {{ session('success') }}
            <button @click="show = false" class="absolute top-4 right-4 font-bold text-green-900">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded-md shadow-sm break-words">
            <h3 class="text-sm font-bold text-red-800">Gagal:</h3>
            <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-container overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-gray-50 border-b text-gray-600">
                    <th class="p-3 w-16 text-center">No</th>
                    <th class="p-3">Nama Menu / Modul</th>
                    <th class="p-3">Induk Menu (Parent)</th>
                    <th class="p-3">Tautan (URL)</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($menus as $index => $item)
                <tr class="border-b hover:bg-gray-50" x-data="{ modalDetail: false, modalEdit: false, modalHapus: false }">
                    <td class="p-3 text-center">{{ $index + 1 }}</td>
                    <td class="p-3 font-semibold text-gray-800">
                        @if($item->parent_id)
                            <span class="text-gray-400 mr-1">â†³</span>
                        @endif
                        {{ $item->nama_menu }}
                    </td>
                    <td class="p-3 text-gray-500">
                        @if($item->parent)
                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-semibold">{{ $item->parent->nama_menu }}</span>
                        @else
                            <span class="text-gray-400 italic text-xs">Menu Utama</span>
                        @endif
                    </td>
                    <td class="p-3 text-gray-500 italic">{{ $item->url }}</td>
                    <td class="p-3 text-center whitespace-nowrap">
                        <div class="flex items-center justify-center gap-1">
                            <button @click="modalDetail = true" class="text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 p-1.5 rounded-md transition-colors" title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </button>
                            <button @click="modalEdit = true" class="text-amber-500 hover:text-amber-700 bg-amber-50 hover:bg-amber-100 p-1.5 rounded-md transition-colors" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <button @click="modalHapus = true" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-1.5 rounded-md transition-colors" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>

                        <!-- Modal Detail -->
                        <div x-show="modalDetail" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 text-left" style="display: none;">
                            <div class="card-container max-w-sm w-full shadow-xl" @click.away="modalDetail = false">
                                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Detail Menu</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="form-label text-gray-500 text-xs">Nama Menu</label>
                                        <div class="form-input bg-gray-50 font-semibold">{{ $item->nama_menu }}</div>
                                    </div>
                                    <div>
                                        <label class="form-label text-gray-500 text-xs">Induk Menu (Parent)</label>
                                        <div class="form-input bg-gray-50">{{ $item->parent ? $item->parent->nama_menu : 'Tidak ada (Menu Utama)' }}</div>
                                    </div>
                                    <div>
                                        <label class="form-label text-gray-500 text-xs">Path URL</label>
                                        <div class="form-input bg-gray-50 text-blue-600">{{ $item->url }}</div>
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
                                <h3 class="text-lg font-bold text-gray-800 mb-2 border-b pb-2">Edit Menu</h3>
                                <form action="{{ route('admin.menu.update', $item->id_menu ?? $item->id) }}" method="POST" class="space-y-4">
                                    @csrf @method('PUT')
                                    <div>
                                        <label class="form-label text-xs font-bold text-gray-700">Nama Menu (Modul Hak Akses)</label>
                                        <input type="text" name="nama_menu" value="{{ $item->nama_menu }}" required class="form-input mt-1">
                                    </div>
                                    <div>
                                        <label class="form-label text-xs font-bold text-gray-700">Tautan (URL) / Path</label>
                                        <input type="text" name="url" value="{{ $item->url }}" class="form-input mt-1" placeholder="Contoh: /admin/target">
                                    </div>
                                    <div>
                                        <label class="form-label text-xs font-bold text-gray-700">Jadikan Submenu Dari: (Opsional)</label>
                                        <select name="parent_id" class="form-input mt-1">
                                            <option value="">-- Menu Utama --</option>
                                            @foreach($parentMenus as $parent)
                                                @if($parent->id_menu != ($item->id_menu ?? $item->id))
                                                    <option value="{{ $parent->id_menu }}" {{ $item->parent_id == $parent->id_menu ? 'selected' : '' }}>
                                                        {{ $parent->nama_menu }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex justify-end gap-2 mt-6">
                                        <button type="button" @click="modalEdit = false" class="bg-gray-200 px-4 py-2 rounded text-sm font-bold text-gray-700">Batal</button>
                                        <button type="submit" class="btn-action-edit px-4 py-2">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Modal Hapus -->
                        <div x-show="modalHapus" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 text-center" style="display: none;">
                            <div class="card-container max-w-sm w-full shadow-xl" @click.away="modalHapus = false">
                                <h3 class="text-lg font-bold text-gray-800 mb-2">Konfirmasi Hapus</h3>
                                <p class="text-sm text-gray-600 mb-6">Hapus menu ini? Hak akses terkait pada Role juga akan hilang.</p>
                                <form action="{{ route('admin.menu.destroy', $item->id_menu ?? $item->id) }}" method="POST" class="flex justify-center gap-2">
                                    @csrf @method('DELETE')
                                    <button type="button" @click="modalHapus = false" class="bg-gray-200 px-4 py-2 rounded text-sm font-bold">Batal</button>
                                    <button type="submit" class="btn-action-delete px-4 py-2">Ya, Hapus!</button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-6 text-center text-gray-400">Belum ada menu yang didaftarkan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah -->
    <div x-show="modalTambah" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 text-left" style="display: none;">
        <div class="card-container max-w-md w-full shadow-xl" @click.away="modalTambah = false">
            <h3 class="text-lg font-bold text-gray-800 mb-2 border-b pb-2">Tambah Menu Baru</h3>
            
            <form action="{{ route('admin.menu.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="form-label text-xs font-bold text-gray-700">Nama Menu (Modul Hak Akses)</label>
                    <input type="text" name="nama_menu" required class="form-input mt-1" placeholder="Contoh: Laporan Tahunan">
                </div>
                <div>
                    <label class="form-label text-xs font-bold text-gray-700">Tautan (URL) / Path</label>
                    <input type="text" name="url" class="form-input mt-1" placeholder="Contoh: /laporan-tahunan">
                </div>
                <div>
                    <label class="form-label text-xs font-bold text-gray-700">Jadikan Submenu Dari: (Opsional)</label>
                    <select name="parent_id" class="form-input mt-1">
                        <option value="">-- Berdiri Sendiri (Menu Utama) --</option>
                        @foreach($parentMenus as $parent)
                            <option value="{{ $parent->id_menu }}">{{ $parent->nama_menu }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="modalTambah = false" class="bg-gray-200 px-4 py-2 rounded text-sm font-bold text-gray-700">Batal</button>
                    <button type="submit" class="btn-teal px-4 py-2">Simpan Menu</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


