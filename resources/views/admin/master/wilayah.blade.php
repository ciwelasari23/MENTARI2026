@extends('layouts.admin')

@section('title', 'Master Data Wilayah')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-[#1e293b]">Master Data Wilayah</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
    <!-- Navbar / Top Controls -->
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <h2 class="text-lg font-bold text-gray-800">Daftar Wilayah</h2>
        
        <div class="flex items-center gap-3">
            <form action="{{ route('admin.wilayah.index') }}" method="GET" class="flex items-center gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari wilayah..." 
                       class="border border-gray-300 rounded-md px-4 py-2 w-64 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                
                <button type="submit" class="bg-[#1e293b] hover:bg-gray-800 text-white px-5 py-2 rounded-md text-sm font-semibold transition">
                    Cari
                </button>
            </form>
            
            <button onclick="openModal('modalTambah')" class="bg-[#10b981] hover:bg-emerald-600 text-white px-5 py-2 rounded-md text-sm font-semibold transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Wilayah
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div id="alert-box" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 text-sm transition-opacity duration-500">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div id="alert-box" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-sm transition-opacity duration-500">
            {{ session('error') }}
        </div>
    @endif

    <!-- Table Container -->
    <div class="overflow-x-auto border border-gray-100 rounded-lg">
        <table class="w-full text-left border-collapse text-sm whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50 border-b text-gray-600 font-semibold">
                    <th class="p-4 w-12 text-center"><input type="checkbox" class="rounded border-gray-300 text-blue-600"></th>
                    <th class="p-4 text-center">No</th>
                    <th class="p-4">ID</th>
                    <th class="p-4">Provinsi</th>
                    <th class="p-4">Kab/Kota</th>
                    <th class="p-4">Kecamatan</th>
                    <th class="p-4">Desa/Kel</th>
                    <th class="p-4">SLS/RT/RW</th>
                    <th class="p-4">Sub SLS</th>
                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($wilayahs as $index => $item)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-4 text-center"><input type="checkbox" class="rounded border-gray-300"></td>
                    <td class="p-4 text-center font-medium">{{ $wilayahs->firstItem() + $index }}</td>
                    <td class="p-4 text-gray-500 font-semibold">{{ $item->idsubsls ?? $item->id_sub_sls ?? $item->id_wilayah ?? $item->id }}</td>
                    <td class="p-4 font-semibold text-gray-800">{{ $item->nama_provinsi ?? '-' }}</td>
                    <td class="p-4 text-gray-700">{{ $item->kode_nama_kabkota ?? '-' }}</td>
                    <td class="p-4 text-gray-600">{{ $item->kode_nama_kecamatan ?? '-' }}</td>
                    <td class="p-4 text-gray-600">{{ $item->kode_nama_desa ?? '-' }}</td>
                    <td class="p-4 text-gray-600 font-mono">{{ $item->kode_nama_sls ?? $item->kode_sls ?? '-' }}</td>
                    <td class="p-4 text-gray-600 font-mono">{{ $item->kode_nama_sub_sls ?? $item->kode_sub_sls ?? '-' }}</td>
                    <td class="p-4 flex items-center justify-center gap-2">
                        <button data-item="{{ json_encode($item) }}" onclick="openDetailModal(JSON.parse(this.dataset.item))" class="bg-[#3b82f6] hover:bg-blue-600 text-white px-3 py-1.5 rounded text-xs font-semibold">Detail</button>
                        <button data-item="{{ json_encode($item) }}" onclick="openEditModal(JSON.parse(this.dataset.item))" class="bg-[#eab308] hover:bg-yellow-500 text-white px-3 py-1.5 rounded text-xs font-semibold">Edit</button>
                        <form action="{{ route('admin.wilayah.destroy', $item->id_wilayah ?? $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus data wilayah ini?');">
                            @csrf 
                            @method('DELETE')
                            <button type="submit" class="bg-[#ef4444] hover:bg-red-600 text-white px-3 py-1.5 rounded text-xs font-semibold">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="p-8 text-center text-gray-400">Belum ada data wilayah. Silahkan tambah data.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Paginasi -->
    <div class="mt-4">
        {{ $wilayahs->links() }}
    </div>
</div>

<!-- MODAL TAMBAH DATA WILAYAH -->
<div id="modalTambah" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-bold mb-4">Tambah Master Wilayah</h3>
        <form action="{{ route('admin.wilayah.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <label class="block font-medium mb-1">ID Wilayah / Sub SLS *</label>
                    <input type="text" name="id_wilayah" placeholder="1401010005000101" required class="w-full border rounded p-2">
                </div>
                <div>
                    <label class="block font-medium mb-1">Provinsi *</label>
                    <input type="text" name="nama_provinsi" placeholder="RIAU" required class="w-full border rounded p-2">
                </div>
                <div>
                    <label class="block font-medium mb-1">Kab/Kota</label>
                    <input type="text" name="kode_nama_kabkota" placeholder="KUANTAN SINGINGI" class="w-full border rounded p-2">
                </div>
                <div>
                    <label class="block font-medium mb-1">Kecamatan</label>
                    <input type="text" name="kode_nama_kecamatan" placeholder="KUANTAN MUDIK" class="w-full border rounded p-2">
                </div>
                <div>
                    <label class="block font-medium mb-1">Desa/Kel</label>
                    <input type="text" name="kode_nama_desa" placeholder="PANTAI" class="w-full border rounded p-2">
                </div>
                <div>
                    <label class="block font-medium mb-1">Kode SLS / RT / RW</label>
                    <input type="text" name="kode_nama_sls" inputmode="numeric" placeholder="0001" class="w-full border rounded p-2 font-mono">
                </div>
                <div>
                    <label class="block font-medium mb-1">Kode Sub SLS</label>
                    <input type="text" name="kode_nama_sub_sls" inputmode="numeric" placeholder="00" class="w-full border rounded p-2 font-mono">
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeModal('modalTambah')" class="px-4 py-2 border rounded text-gray-600">Batal</button>
                <button type="submit" class="px-4 py-2 bg-[#10b981] text-white rounded font-semibold">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT DATA WILAYAH -->
<div id="modalEdit" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-bold mb-4">Edit Master Wilayah</h3>
        <form id="formEdit" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <label class="block font-medium mb-1">ID Sub SLS (ID)</label>
                    <input type="text" id="edit_idsubsls" name="id_wilayah" class="w-full border rounded p-2">
                </div>
                <div>
                    <label class="block font-medium mb-1">Provinsi *</label>
                    <input type="text" id="edit_nama_provinsi" name="nama_provinsi" required class="w-full border rounded p-2">
                </div>
                <div>
                    <label class="block font-medium mb-1">Kab/Kota</label>
                    <input type="text" id="edit_kode_nama_kabkota" name="kode_nama_kabkota" class="w-full border rounded p-2">
                </div>
                <div>
                    <label class="block font-medium mb-1">Kecamatan</label>
                    <input type="text" id="edit_kode_nama_kecamatan" name="kode_nama_kecamatan" class="w-full border rounded p-2">
                </div>
                <div>
                    <label class="block font-medium mb-1">Desa/Kel</label>
                    <input type="text" id="edit_kode_nama_desa" name="kode_nama_desa" class="w-full border rounded p-2">
                </div>
                <div>
                    <label class="block font-medium mb-1">Kode SLS / RT / RW (Angka)</label>
                    <input type="text" id="edit_kode_nama_sls" name="kode_nama_sls" inputmode="numeric" placeholder="0001" class="w-full border rounded p-2 font-mono">
                </div>
                <div>
                    <label class="block font-medium mb-1">Kode Sub SLS (Angka)</label>
                    <input type="text" id="edit_kode_nama_sub_sls" name="kode_nama_sub_sls" inputmode="numeric" placeholder="00" class="w-full border rounded p-2 font-mono">
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeModal('modalEdit')" class="px-4 py-2 border rounded text-gray-600">Batal</button>
                <button type="submit" class="px-4 py-2 bg-[#eab308] text-white rounded font-semibold">Perbarui</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL DETAIL DATA WILAYAH -->
<div id="modalDetail" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl max-w-xl w-full p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-bold mb-4">Detail Master Wilayah</h3>
        <div class="space-y-3 text-sm">
            <div class="flex border-b pb-2">
                <span class="w-40 font-medium text-gray-500">ID Wilayah</span>
                <span id="detail_idsubsls" class="font-semibold text-gray-800">-</span>
            </div>
            <div class="flex border-b pb-2">
                <span class="w-40 font-medium text-gray-500">Provinsi</span>
                <span id="detail_nama_provinsi" class="font-semibold text-gray-800">-</span>
            </div>
            <div class="flex border-b pb-2">
                <span class="w-40 font-medium text-gray-500">Kab/Kota</span>
                <span id="detail_kode_nama_kabkota" class="text-gray-800">-</span>
            </div>
            <div class="flex border-b pb-2">
                <span class="w-40 font-medium text-gray-500">Kecamatan</span>
                <span id="detail_kode_nama_kecamatan" class="text-gray-800">-</span>
            </div>
            <div class="flex border-b pb-2">
                <span class="w-40 font-medium text-gray-500">Desa/Kel</span>
                <span id="detail_kode_nama_desa" class="text-gray-800">-</span>
            </div>
            <div class="flex border-b pb-2">
                <span class="w-40 font-medium text-gray-500">SLS / RT / RW</span>
                <span id="detail_kode_nama_sls" class="font-mono text-gray-800">-</span>
            </div>
            <div class="flex pb-2">
                <span class="w-40 font-medium text-gray-500">Sub SLS</span>
                <span id="detail_kode_nama_sub_sls" class="font-mono text-gray-800">-</span>
            </div>
        </div>
        <div class="flex justify-end mt-6">
            <button type="button" onclick="closeModal('modalDetail')" class="px-4 py-2 bg-gray-500 text-white rounded font-semibold">Tutup</button>
        </div>
    </div>
</div>

<script>
    // Auto hide alert setelah 3 detik
    setTimeout(function() {
        const alertBox = document.getElementById('alert-box');
        if (alertBox) {
            alertBox.style.opacity = '0';
            setTimeout(() => alertBox.remove(), 500);
        }
    }, 3000);

    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.getElementById(id).classList.add('flex');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.getElementById(id).classList.remove('flex');
    }

    function openEditModal(data) {
        const id = data.id_wilayah || data.id || data.idsubsls;
        
        let updateUrl = "{{ route('admin.wilayah.update', ':id') }}";
        updateUrl = updateUrl.replace(':id', id);
        
        document.getElementById('formEdit').action = updateUrl;
        document.getElementById('edit_idsubsls').value = data.idsubsls ?? data.id_sub_sls ?? data.id_wilayah ?? data.id ?? '';
        document.getElementById('edit_nama_provinsi').value = data.nama_provinsi ?? '';
        document.getElementById('edit_kode_nama_kabkota').value = data.kode_nama_kabkota ?? '';
        document.getElementById('edit_kode_nama_kecamatan').value = data.kode_nama_kecamatan ?? '';
        document.getElementById('edit_kode_nama_desa').value = data.kode_nama_desa ?? '';
        document.getElementById('edit_kode_nama_sls').value = data.kode_nama_sls ?? data.kode_sls ?? '';
        document.getElementById('edit_kode_nama_sub_sls').value = data.kode_nama_sub_sls ?? data.kode_sub_sls ?? '';
        
        openModal('modalEdit');
    }

    function openDetailModal(data) {
        document.getElementById('detail_idsubsls').innerText = data.idsubsls ?? data.id_sub_sls ?? data.id_wilayah ?? data.id ?? '-';
        document.getElementById('detail_nama_provinsi').innerText = data.nama_provinsi ?? '-';
        document.getElementById('detail_kode_nama_kabkota').innerText = data.kode_nama_kabkota ?? '-';
        document.getElementById('detail_kode_nama_kecamatan').innerText = data.kode_nama_kecamatan ?? '-';
        document.getElementById('detail_kode_nama_desa').innerText = data.kode_nama_desa ?? '-';
        document.getElementById('detail_kode_nama_sls').innerText = data.kode_nama_sls ?? data.kode_sls ?? '-';
        document.getElementById('detail_kode_nama_sub_sls').innerText = data.kode_nama_sub_sls ?? data.kode_sub_sls ?? '-';
        
        openModal('modalDetail');
    }
</script>
@endsection