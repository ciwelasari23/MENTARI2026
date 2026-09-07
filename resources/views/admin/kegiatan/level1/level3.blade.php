@extends('layouts.admin')

@section('title', 'Level 3: Detail Kegiatan')
@section('header', 'Kelola Detail Kegiatan Level 3')

@section('content')
<div class="space-y-6" x-data="{ modalTambah: false, selected: [], selectAll: false }">
    
    <div class="flex flex-wrap gap-4 justify-between items-center card-container">
        <h3 class="text-lg font-bold text-gray-800">Daftar Detail Kegiatan (Level 3)</h3>
        
        <div class="flex items-center gap-2">
            <form action="{{ route('admin.level3.index') }}" method="GET" class="flex gap-2 mr-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama detail..." class="form-input w-48">
                <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-gray-700 shadow-sm">Cari</button>
                @if(request('search'))
                    <a href="{{ route('admin.level3.index') }}" class="bg-gray-200 text-gray-700 px-3 py-2 rounded-lg font-bold text-sm hover:bg-gray-300 flex items-center">Reset</a>
                @endif
            </form>

            <form action="{{ route('admin.level3.bulkDestroy') }}" method="POST" x-show="selected.length > 0" onsubmit="return confirm('Yakin ingin menghapus semua data yang dicentang? Data Level 4 di bawahnya juga bisa ikut terhapus.');" style="display: none;">
                @csrf @method('DELETE')
                <template x-for="id in selected"><input type="hidden" name="ids[]" :value="id"></template>
                <button type="submit" class="btn-action-delete flex items-center gap-2 px-4 py-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Hapus Terpilih (<span x-text="selected.length"></span>)
                </button>
            </form>

            <button @click="modalTambah = true" class="btn-teal">
                + Tambah Detail
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
                    <th class="p-3 w-10 text-center"><input type="checkbox" x-model="selectAll" @change="selected = selectAll ? {{ json_encode($details->pluck('id_keg_detail')->map(fn($id) => (string)$id)) }} : []" class="w-4 h-4 text-[#14B8A6] border-gray-300 rounded cursor-pointer"></th>
                    <th class="p-3 w-12 text-center">No</th>
                    <th class="p-3">Kegiatan Induk (Level 2)</th>
                    <th class="p-3">Detail Kegiatan (Level 3)</th>
                    <th class="p-3">Jadwal Pelaksanaan</th>
                    <th class="p-3">Target</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($details as $index => $item)
                <tr class="border-b hover:bg-gray-50" x-data="{ modalEdit: false, modalDetail: false, modalHapus: false }">
                    <td class="p-3 text-center"><input type="checkbox" x-model="selected" value="{{ $item->id_keg_detail }}" class="w-4 h-4 text-[#14B8A6] border-gray-300 rounded cursor-pointer"></td>
                    <td class="p-3 text-center">{{ $index + 1 }}</td>
                    <td class="p-3 text-gray-600">{{ $item->kegiatan->nama_kegiatan ?? '-' }}</td>
                    <td class="p-3 font-semibold text-gray-800">{{ $item->nama_keg_detail }}</td>
                    <td class="p-3 text-xs text-gray-600">
                        {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }} - 
                        {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}
                    </td>
                    <td class="p-3 font-medium text-[#14B8A6]">
                        {{ number_format($item->target_total, 0, ',', '.') }} <span class="text-gray-500 text-xs ml-1">{{ $item->satuan_target }}</span>
                    </td>
                    <td class="p-3 text-center whitespace-nowrap">
                        
                        <!-- Tombol Detail -->
                        <button @click="modalDetail = true" class="bg-blue-500 text-white px-3 py-1 rounded text-xs font-bold mr-1 hover:bg-blue-600 transition-colors">Detail</button>
                        <button @click="modalEdit = true" class="btn-action-edit mr-1">Edit</button>
                        <button @click="modalHapus = true" class="btn-action-delete">Hapus</button>

                        <!-- Modal Detail -->
                        <div x-show="modalDetail" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 text-left" style="display: none;">
                            <div class="card-container max-w-2xl w-full shadow-xl" @click.away="modalDetail = false">
                                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Informasi Detail Kegiatan Level 3</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="form-label text-gray-500">Kegiatan Induk (Level 2)</label>
                                        <div class="form-input bg-gray-50 font-semibold">{{ $item->kegiatan->nama_kegiatan ?? 'Tidak ada data' }}</div>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="form-label text-gray-500">Nama Detail Kegiatan</label>
                                        <div class="form-input bg-gray-50 font-semibold">{{ $item->nama_keg_detail }}</div>
                                    </div>
                                    <div>
                                        <label class="form-label text-gray-500">Tanggal Mulai</label>
                                        <div class="form-input bg-gray-50 font-semibold">{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d F Y') }}</div>
                                    </div>
                                    <div>
                                        <label class="form-label text-gray-500">Tanggal Selesai</label>
                                        <div class="form-input bg-gray-50 font-semibold">{{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d F Y') }}</div>
                                    </div>
                                    <div>
                                        <label class="form-label text-gray-500">Target Total</label>
                                        <div class="form-input bg-gray-50 font-bold text-[#14B8A6]">{{ number_format($item->target_total, 0, ',', '.') }} {{ $item->satuan_target }}</div>
                                    </div>
                                </div>
                                <div class="flex justify-end mt-6">
                                    <button type="button" @click="modalDetail = false" class="bg-gray-800 text-white px-4 py-2 rounded text-sm font-bold hover:bg-gray-700">Tutup</button>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Edit -->
                        <div x-show="modalEdit" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 text-left" style="display: none;">
                            <div class="card-container max-w-2xl w-full shadow-xl" @click.away="modalEdit = false">
                                <h3 class="text-lg font-bold text-gray-800 mb-4">Edit Detail Kegiatan Level 3</h3>
                                <form action="{{ route('admin.level3.update', $item->id_keg_detail) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="md:col-span-2">
                                            <label class="form-label">Pilih Kegiatan Induk (Level 2)</label>
                                            <select name="id_kegiatan" required class="form-input">
                                                @foreach($kegiatanList as $keg)
                                                    <option value="{{ $keg->id_kegiatan }}" {{ $item->id_kegiatan == $keg->id_kegiatan ? 'selected' : '' }}>{{ $keg->nama_kegiatan }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="form-label">Nama Detail Kegiatan (Level 3)</label>
                                            <input type="text" name="nama_keg_detail" value="{{ $item->nama_keg_detail }}" required class="form-input">
                                        </div>
                                        <div>
                                            <label class="form-label">Tanggal Mulai</label>
                                            <input type="date" name="tanggal_mulai" value="{{ $item->tanggal_mulai }}" required class="form-input">
                                        </div>
                                        <div>
                                            <label class="form-label">Tanggal Selesai</label>
                                            <input type="date" name="tanggal_selesai" value="{{ $item->tanggal_selesai }}" required class="form-input">
                                        </div>
                                        <div>
                                            <label class="form-label">Target Total</label>
                                            <input type="number" name="target_total" min="1" value="{{ $item->target_total }}" required class="form-input">
                                        </div>
                                        <div>
                                            <label class="form-label">Satuan Target</label>
                                            <select name="satuan_target" required class="form-input">
                                                <option value="Dokumen" {{ $item->satuan_target == 'Dokumen' ? 'selected' : '' }}>Dokumen</option>
                                                <option value="Laporan" {{ $item->satuan_target == 'Laporan' ? 'selected' : '' }}>Laporan</option>
                                                <option value="Desa / Kelurahan" {{ $item->satuan_target == 'Desa / Kelurahan' ? 'selected' : '' }}>Desa / Kelurahan</option>
                                                <option value="Kecamatan" {{ $item->satuan_target == 'Kecamatan' ? 'selected' : '' }}>Kecamatan</option>
                                                <option value="Kabupaten / Kota" {{ $item->satuan_target == 'Kabupaten / Kota' ? 'selected' : '' }}>Kabupaten / Kota</option>
                                                <option value="Perusahaan / Usaha" {{ $item->satuan_target == 'Perusahaan / Usaha' ? 'selected' : '' }}>Perusahaan / Usaha</option>
                                                <option value="Rumah Tangga" {{ $item->satuan_target == 'Rumah Tangga' ? 'selected' : '' }}>Rumah Tangga</option>
                                                <option value="Responden" {{ $item->satuan_target == 'Responden' ? 'selected' : '' }}>Responden</option>
                                                <option value="Kegiatan" {{ $item->satuan_target == 'Kegiatan' ? 'selected' : '' }}>Kegiatan</option>
                                                <option value="Rekomendasi" {{ $item->satuan_target == 'Rekomendasi' ? 'selected' : '' }}>Rekomendasi</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="flex justify-end gap-2 mt-6">
                                        <button type="button" @click="modalEdit = false" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-bold hover:bg-gray-300">Batal</button>
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
                                <p class="text-sm text-gray-600 mb-6">Apakah Anda yakin ingin menghapus data ini? Data Level 4 di bawahnya juga bisa ikut terhapus.</p>
                                <form action="{{ route('admin.level3.destroy', $item->id_keg_detail) }}" method="POST" class="flex justify-center gap-2">
                                    @csrf @method('DELETE')
                                    <button type="button" @click="modalHapus = false" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-bold hover:bg-gray-300">Batal</button>
                                    <button type="submit" class="btn-action-delete">Ya, Hapus!</button>
                                </form>
                            </div>
                        </div>

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-6 text-center text-gray-400">
                        @if(request('search'))
                            Pencarian "{{ request('search') }}" tidak ditemukan.
                        @else
                            Belum ada data detail kegiatan (Level 3).
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah -->
    <div x-show="modalTambah" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" style="display: none;">
        <div class="card-container max-w-2xl w-full shadow-xl" @click.away="modalTambah = false">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Tambah Detail Kegiatan Level 3</h3>
            <form action="{{ route('admin.level3.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="form-label">Pilih Kegiatan Induk (Level 2)</label>
                        <select name="id_kegiatan" required class="form-input">
                            <option value="">-- Pilih Kegiatan Level 2 --</option>
                            @foreach($kegiatanList as $keg)
                                <option value="{{ $keg->id_kegiatan }}">{{ $keg->nama_kegiatan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="form-label">Nama Detail Kegiatan (Level 3)</label>
                        <input type="text" name="nama_keg_detail" required class="form-input" placeholder="Contoh: Pencacahan Lapangan Sensus...">
                    </div>
                    <div>
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" required class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" required class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Target Total</label>
                        <input type="number" name="target_total" min="1" required class="form-input" placeholder="Masukkan jumlah angka...">
                    </div>
                    <div>
                        <label class="form-label">Satuan Target</label>
                        <select name="satuan_target" required class="form-input">
                            <option value="">-- Pilih Satuan Target --</option>
                            <option value="Dokumen">Dokumen</option>
                            <option value="Laporan">Laporan</option>
                            <option value="Desa / Kelurahan">Desa / Kelurahan</option>
                            <option value="Kecamatan">Kecamatan</option>
                            <option value="Kabupaten / Kota">Kabupaten / Kota</option>
                            <option value="Perusahaan / Usaha">Perusahaan / Usaha</option>
                            <option value="Rumah Tangga">Rumah Tangga</option>
                            <option value="Responden">Responden</option>
                            <option value="Kegiatan">Kegiatan</option>
                            <option value="Rekomendasi">Rekomendasi</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="modalTambah = false" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-bold hover:bg-gray-300">Batal</button>
                    <button type="submit" class="btn-teal">Simpan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection