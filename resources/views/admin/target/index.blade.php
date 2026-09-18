@extends('layouts.admin')

@section('title', 'Target Wilayah')
@section('header', 'Pengelolaan Target Wilayah')

@section('content')
<div class="space-y-6" x-data="{ 
    modalTambah: false, 
    modalEdit: false, 
    modalDetail: false, 
    editData: {}, 
    detailData: {},
    searchQuery: '',
    searchInput: '',
    selected: [],
    selectAll: false,
    allIds: {{ json_encode(collect($targets)->pluck('id_target_wilayah')) }},
    toggleAll() {
        this.selected = this.selectAll ? this.allIds : [];
    }
}">

    <div class="flex flex-col xl:flex-row gap-4 justify-between items-center card-container">
        
        <h3 class="text-lg font-bold text-gray-800 w-full xl:w-auto">Daftar Target Wilayah</h3>

        <div class="flex flex-col sm:flex-row items-center gap-3 w-full xl:w-auto justify-end">

            <div class="relative w-full sm:w-auto flex gap-2">
                <input type="text" x-model="searchInput" @keydown.enter.prevent="searchQuery = searchInput" class="form-input block w-full sm:w-64 px-4 py-2 text-sm border rounded-lg bg-white" placeholder="Cari wilayah atau proses...">
                <button type="button" @click="searchQuery = searchInput" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold text-sm shadow-sm flex items-center justify-center transition-colors" title="Cari">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
                <button type="button" x-show="searchQuery !== ''" @click="searchInput = ''; searchQuery = ''" class="bg-gray-200 text-gray-700 px-3 py-2 rounded-lg font-bold text-sm hover:bg-gray-300 flex items-center" style="display: none;">Reset</button>
            </div>

            <div x-show="selected.length > 0" x-transition style="display: none;">
                <form action="{{ route('admin.target.bulkDestroy') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ' + selected.length + ' data terpilih?')">
                    @csrf
                    @method('DELETE')
                    <template x-for="id in selected">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                    <button type="submit" class="btn-action-delete text-sm px-4 py-2 rounded-lg flex items-center justify-center gap-2 whitespace-nowrap w-full sm:w-auto">
                        Hapus Terpilih (<span x-text="selected.length"></span>)
                    </button>
                </form>
            </div>

            <button @click="modalTambah = true" class="bg-[#10b981] hover:bg-emerald-600 text-white px-5 py-2 rounded-md text-sm font-semibold transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Target Wilayah
            </button>
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition.duration.500ms class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-container overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-gray-50 border-b text-gray-600">
                    <th class="p-3 text-center w-12">
                        <input type="checkbox" x-model="selectAll" @change="toggleAll" class="w-4 h-4 text-teal-600 bg-white border-gray-300 rounded focus:ring-teal-500">
                    </th>
                    <th class="p-3 w-16 text-center">No</th>
                    <th class="p-3">Nama Proses (Kegiatan)</th>
                    <th class="p-3">Wilayah</th>
                    <th class="p-3 text-center">Target Daerah</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($targets as $index => $item)
                <tr class="border-b hover:bg-gray-50" 
                    x-data="{ modalHapus: false }"
                    x-show="searchQuery === '' || $el.textContent.toLowerCase().includes(searchQuery.toLowerCase())">
                    
                    <td class="p-3 text-center">
                        <input type="checkbox" :value="{{ $item->id_target_wilayah }}" x-model="selected" class="w-4 h-4 text-teal-600 bg-white border-gray-300 rounded focus:ring-teal-500">
                    </td>

                    <td class="p-3 text-center">{{ $index + 1 }}</td>
                    <td class="p-3 font-medium text-gray-800">{{ $item->proses->nama_proses ?? 'Proses tidak ditemukan' }}</td>
                    <td class="p-3 font-medium text-gray-600">
                        @if($item->wilayah)
                            {{ $item->wilayah->nama_provinsi }} | 
                            {{ $item->wilayah->kode_nama_kabkota }} | 
                            {{ $item->wilayah->kode_nama_kecamatan }} | 
                            {{ $item->wilayah->kode_nama_desa }} 
                            (SLS: {{ $item->wilayah->kode_nama_sls }})
                        @else
                            Wilayah tidak ditemukan
                        @endif
                    </td>
                    <td class="p-3 font-semibold text-gray-800 text-center">{{ $item->target_daerah }}</td>
                    <td class="p-3 text-center whitespace-nowrap">
                        <div class="flex items-center justify-center gap-1">
                            <button @click="detailData = {
                                nama_proses: '{{ $item->proses->nama_proses ?? '-' }}',
                                provinsi: '{{ $item->wilayah->nama_provinsi ?? '-' }}',
                                kabkota: '{{ $item->wilayah->kode_nama_kabkota ?? '-' }}',
                                kecamatan: '{{ $item->wilayah->kode_nama_kecamatan ?? '-' }}',
                                desa: '{{ $item->wilayah->kode_nama_desa ?? '-' }}',
                                sls: '{{ $item->wilayah->kode_nama_sls ?? '-' }}',
                                target_daerah: '{{ $item->target_daerah }}',
                                created_at: '{{ $item->created_at ? $item->created_at->format('d-m-Y H:i') : '-' }}'
                            }; modalDetail = true" class="text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 p-1.5 rounded-md transition-colors" title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </button>

                            <button @click="editData = {
                                id: '{{ $item->id_target_wilayah }}',
                                id_proses: '{{ $item->id_proses }}',
                                id_wilayah: '{{ $item->id_wilayah }}',
                                target_daerah: '{{ $item->target_daerah }}'
                            }; modalEdit = true" class="text-amber-500 hover:text-amber-700 bg-amber-50 hover:bg-amber-100 p-1.5 rounded-md transition-colors" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>

                            <button @click="modalHapus = true" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-1.5 rounded-md transition-colors" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>

                        <div x-show="modalHapus" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 text-center whitespace-normal" style="display: none;">
                            <div class="card-container max-w-sm w-full shadow-xl" @click.away="modalHapus = false">
                                <h3 class="text-lg font-bold text-gray-800 mb-2 mt-2">Konfirmasi Hapus</h3>
                                <p class="text-sm text-gray-600 mb-6">Apakah Anda yakin ingin menghapus target wilayah ini?</p>
                                <form action="{{ route('admin.target.destroy', $item->id_target_wilayah) }}" method="POST" class="flex justify-center gap-2">
                                    @csrf @method('DELETE')
                                    <button type="button" @click="modalHapus = false" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-bold">Batal</button>
                                    <button type="submit" class="btn-action-delete text-[0.875rem] px-4 py-2 rounded-lg">Ya, Hapus!</button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="p-6 text-center text-gray-400">Belum ada data target wilayah.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Detail Target -->
    <div x-show="modalDetail" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" style="display: none;">
        <div class="card-container max-w-md w-full shadow-xl space-y-4" @click.away="modalDetail = false">
            <h3 class="text-lg font-bold text-gray-800 border-b pb-2">Detail Target Wilayah</h3>
            <div class="space-y-2 text-sm text-gray-700">
                <div>
                    <span class="font-semibold text-gray-500 block text-xs">Nama Proses Kegiatan:</span>
                    <span class="font-medium text-gray-800" x-text="detailData.nama_proses"></span>
                </div>
                <div>
                    <span class="font-semibold text-gray-500 block text-xs">Provinsi:</span>
                    <span x-text="detailData.provinsi"></span>
                </div>
                <div>
                    <span class="font-semibold text-gray-500 block text-xs">Kabupaten/Kota:</span>
                    <span x-text="detailData.kabkota"></span>
                </div>
                <div>
                    <span class="font-semibold text-gray-500 block text-xs">Kecamatan:</span>
                    <span x-text="detailData.kecamatan"></span>
                </div>
                <div>
                    <span class="font-semibold text-gray-500 block text-xs">Desa / Kelurahan:</span>
                    <span x-text="detailData.desa"></span>
                </div>
                <div>
                    <span class="font-semibold text-gray-500 block text-xs">SLS:</span>
                    <span x-text="detailData.sls"></span>
                </div>
                <div>
                    <span class="font-semibold text-gray-500 block text-xs">Target Daerah:</span>
                    <span class="font-bold text-teal-600 text-base" x-text="detailData.target_daerah"></span>
                </div>
                <div>
                    <span class="font-semibold text-gray-500 block text-xs">Dibuat Pada:</span>
                    <span x-text="detailData.created_at"></span>
                </div>
            </div>
            <div class="flex justify-end pt-2">
                <button type="button" @click="modalDetail = false" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-bold">Tutup</button>
            </div>
        </div>
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
                        @foreach($wilayahs as $wilayah)
                            <option value="{{ $wilayah->id_wilayah }}">
                                {{ $wilayah->nama_provinsi }} | {{ $wilayah->kode_nama_kabkota }} | {{ $wilayah->kode_nama_kecamatan }} | {{ $wilayah->kode_nama_desa }} (SLS: {{ $wilayah->kode_nama_sls }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label text-xs font-bold text-gray-700">Target Daerah</label>
                    <input type="number" name="target_daerah" required min="1" class="form-input w-full px-3 py-2 border rounded-md" placeholder="Contoh: 100">
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="modalTambah = false" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-bold">Batal</button>
                    <button type="submit" class="btn-teal">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Target -->
    <div x-show="modalEdit" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" style="display: none;">
        <div class="card-container max-w-md w-full shadow-xl" @click.away="modalEdit = false">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Edit Target Wilayah</h3>
            <form :action="'{{ url('admin/target') }}/' + editData.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="form-label text-xs font-bold text-gray-700">Proses Kegiatan</label>
                    <select name="id_proses" x-model="editData.id_proses" required class="form-input w-full px-3 py-2 border rounded-md">
                        <option value="">-- Pilih Proses --</option>
                        @foreach($prosesList as $proses)
                            <option value="{{ $proses->id_proses }}">{{ $proses->nama_proses }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label text-xs font-bold text-gray-700">Wilayah</label>
                    <select name="id_wilayah" x-model="editData.id_wilayah" required class="form-input w-full px-3 py-2 border rounded-md">
                        <option value="">-- Pilih Wilayah --</option>
                        @foreach($wilayahs as $wilayah)
                            <option value="{{ $wilayah->id_wilayah }}">
                                {{ $wilayah->nama_provinsi }} | {{ $wilayah->kode_nama_kabkota }} | {{ $wilayah->kode_nama_kecamatan }} | {{ $wilayah->kode_nama_desa }} (SLS: {{ $wilayah->kode_nama_sls }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label text-xs font-bold text-gray-700">Target Daerah</label>
                    <input type="number" name="target_daerah" x-model="editData.target_daerah" required min="1" class="form-input w-full px-3 py-2 border rounded-md">
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="modalEdit = false" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-bold">Batal</button>
                    <button type="submit" class="btn-teal">Perbarui</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection


