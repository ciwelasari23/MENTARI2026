@extends('layouts.admin')

@section('title', 'Master Data Wilayah')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-[#1e293b]">Master Data Wilayah</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <h2 class="text-lg font-bold text-gray-800">Daftar Wilayah</h2>
        
        <div class="flex items-center gap-3">
            <div id="bulkDeleteContainer" class="hidden">
                <form id="bulkDeleteForm" action="{{ route('admin.wilayah.bulkDestroy') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="ids" id="selectedIds">
                    <button type="button" onclick="confirmBulkDelete()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm font-semibold transition">
                        Hapus Terpilih (<span id="selectedCount">0</span>)
                    </button>
                </form>
            </div>

            <!-- FORM PENCARIAN TEKS -->
            <form action="{{ route('admin.wilayah.index') }}" method="GET" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari wilayah..." class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-[#1e293b] hover:bg-slate-800 text-white px-4 py-1.5 rounded-md text-sm font-semibold transition">
                    Cari
                </button>
            </form>

            <button onclick="openModal('modalTambah')" class="bg-[#10b981] hover:bg-emerald-600 text-white px-5 py-2 rounded-md text-sm font-semibold transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Wilayah
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-box bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 text-sm transition-opacity duration-500">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert-box bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-sm transition-opacity duration-500">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-x-auto border border-gray-100 rounded-lg mt-2 min-h-[300px]">
        <table class="w-full text-left border-collapse text-sm whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50 border-b text-gray-600 font-semibold">
                    <th class="p-4 w-12 text-center">
                        <input type="checkbox" id="checkAll" class="rounded border-gray-300 text-blue-600 cursor-pointer">
                    </th>
                    <th class="p-4 text-center">No</th>
                    <th class="p-4">ID</th>
                    <th class="p-4">Provinsi</th>
                    
                    <!-- Filter Kab/Kota -->
                    <th class="p-4 relative">
                        <div class="flex items-center justify-between gap-2">
                            <span>Kab/Kota</span>
                            <button type="button" onclick="toggleDropdown(event, 'filterKabkota')" class="text-gray-500 hover:text-blue-600 focus:outline-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                        </div>
                        <div id="filterKabkota" class="hidden absolute left-0 mt-2 w-56 bg-white border border-gray-200 rounded-lg shadow-lg z-20 p-2 max-h-48 overflow-y-auto">
                            <a href="{{ route('admin.wilayah.index') }}" class="block px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 rounded">Pilih Semua</a>
                            @if(isset($listKabkota))
                                @foreach($listKabkota as $kab)
                                    @if(!empty($kab->kode_nama_kabkota))
                                        <a href="{{ route('admin.wilayah.index', ['kabkota' => $kab->kode_nama_kabkota]) }}" class="block px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 rounded">
                                            {{ $kab->kode_nama_kabkota }}
                                        </a>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </th>

                    <!-- Filter Kecamatan -->
                    <th class="p-4 relative">
                        <div class="flex items-center justify-between gap-2">
                            <span>Kecamatan</span>
                            <button type="button" onclick="toggleDropdown(event, 'filterKecamatan')" class="text-gray-500 hover:text-blue-600 focus:outline-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                        </div>
                        <div id="filterKecamatan" class="hidden absolute left-0 mt-2 w-56 bg-white border border-gray-200 rounded-lg shadow-lg z-20 p-2 max-h-48 overflow-y-auto">
                            <a href="{{ route('admin.wilayah.index') }}" class="block px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 rounded">Pilih Semua</a>
                            @if(isset($listKecamatan))
                                @foreach($listKecamatan as $kec)
                                    @if(!empty($kec->kode_nama_kecamatan))
                                        <a href="{{ route('admin.wilayah.index', ['kecamatan' => $kec->kode_nama_kecamatan]) }}" class="block px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 rounded">
                                            {{ $kec->kode_nama_kecamatan }}
                                        </a>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </th>

                    <!-- Filter Desa/Kel -->
                    <th class="p-4 relative">
                        <div class="flex items-center justify-between gap-2">
                            <span>Desa/Kel</span>
                            <button type="button" onclick="toggleDropdown(event, 'filterDesa')" class="text-gray-500 hover:text-blue-600 focus:outline-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                        </div>
                        <div id="filterDesa" class="hidden absolute left-0 mt-2 w-56 bg-white border border-gray-200 rounded-lg shadow-lg z-20 p-2 max-h-48 overflow-y-auto">
                            <a href="{{ route('admin.wilayah.index') }}" class="block px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 rounded">Pilih Semua</a>
                            @if(isset($listDesa))
                                @foreach($listDesa as $ds)
                                    @if(!empty($ds->kode_nama_desa))
                                        <a href="{{ route('admin.wilayah.index', ['desa' => $ds->kode_nama_desa]) }}" class="block px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 rounded">
                                            {{ $ds->kode_nama_desa }}
                                        </a>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </th>

                    <!-- Filter SLS -->
                    <th class="p-4 relative">
                        <div class="flex items-center justify-between gap-2">
                            <span>SLS/RT/RW</span>
                            <button type="button" onclick="toggleDropdown(event, 'filterSls')" class="text-gray-500 hover:text-blue-600 focus:outline-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                        </div>
                        <div id="filterSls" class="hidden absolute left-0 mt-2 w-56 bg-white border border-gray-200 rounded-lg shadow-lg z-20 p-2 max-h-48 overflow-y-auto">
                            <a href="{{ route('admin.wilayah.index') }}" class="block px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 rounded">Pilih Semua</a>
                            @if(isset($listSls))
                                @foreach($listSls as $sls)
                                    @if(!empty($sls->kode_nama_sls))
                                        <a href="{{ route('admin.wilayah.index', ['sls' => $sls->kode_nama_sls]) }}" class="block px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 rounded">
                                            {{ $sls->kode_nama_sls }}
                                        </a>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </th>

                    <!-- Filter Sub SLS -->
                    <th class="p-4 relative">
                        <div class="flex items-center justify-between gap-2">
                            <span>Sub SLS</span>
                            <button type="button" onclick="toggleDropdown(event, 'filterSubSls')" class="text-gray-500 hover:text-blue-600 focus:outline-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                        </div>
                        <div id="filterSubSls" class="hidden absolute left-0 mt-2 w-56 bg-white border border-gray-200 rounded-lg shadow-lg z-20 p-2 max-h-48 overflow-y-auto">
                            <a href="{{ route('admin.wilayah.index') }}" class="block px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 rounded">Pilih Semua</a>
                            @if(isset($listSubSls))
                                @foreach($listSubSls as $sub)
                                    @if(!empty($sub->kode_nama_sub_sls))
                                        <a href="{{ route('admin.wilayah.index', ['sub_sls' => $sub->kode_nama_sub_sls]) }}" class="block px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 rounded">
                                            {{ $sub->kode_nama_sub_sls }}
                                        </a>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </th>

                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($wilayahs as $index =>$item)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-4 text-center">
                        <input type="checkbox" class="row-checkbox rounded border-gray-300 cursor-pointer" value="{{ $item->id_wilayah }}">
                    </td>
                    <td class="p-4 text-center font-medium">{{ $wilayahs->firstItem() +$index }}</td>
                    <td class="p-4 text-gray-500 font-semibold">{{ $item->id_wilayah }}</td>
                    <td class="p-4 font-semibold text-gray-800">{{ $item->nama_provinsi ?? '-' }}</td>
                    <td class="p-4 text-gray-700">{{ $item->kode_nama_kabkota ?? '-' }}</td>
                    <td class="p-4 text-gray-600">{{ $item->kode_nama_kecamatan ?? '-' }}</td>
                    <td class="p-4 text-gray-600">{{ $item->kode_nama_desa ?? '-' }}</td>
                    <td class="p-4 text-gray-600 font-mono">{{ $item->kode_nama_sls ?? '-' }}</td>
                    <td class="p-4 text-gray-600 font-mono">{{ $item->kode_nama_sub_sls ?? '-' }}</td>
                    <td class="p-4 flex items-center justify-center gap-2">
                        <button type="button" class="bg-[#3b82f6] hover:bg-blue-600 text-white px-3 py-1.5 rounded text-xs font-semibold btn-detail" data-item='@json($item)'>Detail</button>
                        <button type="button" class="bg-[#eab308] hover:bg-yellow-500 text-white px-3 py-1.5 rounded text-xs font-semibold btn-edit" data-item='@json($item)'>Edit</button>
                        <form action="{{ route('admin.wilayah.destroy', $item->id_wilayah) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus data wilayah ini?');">
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
    
    <div class="mt-4">
        {{ $wilayahs->links() }}
    </div>
</div>

<!-- MODAL TAMBAH DATA -->
<div id="modalTambah" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-bold mb-4">Tambah Master Wilayah</h3>
        <form action="{{ route('admin.wilayah.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <label class="block font-medium mb-1">ID Wilayah *</label>
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
                    <input type="text" name="kode_nama_sls" placeholder="0001" class="w-full border rounded p-2 font-mono">
                </div>
                <div>
                    <label class="block font-medium mb-1">Kode Sub SLS</label>
                    <input type="text" name="kode_nama_sub_sls" placeholder="00" class="w-full border rounded p-2 font-mono">
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeModal('modalTambah')" class="px-4 py-2 border rounded text-gray-600">Batal</button>
                <button type="submit" class="px-4 py-2 bg-[#10b981] text-white rounded font-semibold">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT DATA -->
<div id="modalEdit" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-bold mb-4">Edit Master Wilayah</h3>
        <form id="formEdit" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-4 text-sm">
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
                    <label class="block font-medium mb-1">Kode SLS / RT / RW</label>
                    <input type="text" id="edit_kode_nama_sls" name="kode_nama_sls" class="w-full border rounded p-2 font-mono">
                </div>
                <div>
                    <label class="block font-medium mb-1">Kode Sub SLS</label>
                    <input type="text" id="edit_kode_nama_sub_sls" name="kode_nama_sub_sls" class="w-full border rounded p-2 font-mono">
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeModal('modalEdit')" class="px-4 py-2 border rounded text-gray-600">Batal</button>
                <button type="submit" class="px-4 py-2 bg-[#eab308] text-white rounded font-semibold">Perbarui</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL DETAIL DATA -->
<div id="modalDetail" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl max-w-xl w-full p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-bold mb-4">Detail Master Wilayah</h3>
        <div class="space-y-3 text-sm">
            <div class="flex border-b pb-2">
                <span class="w-40 font-medium text-gray-500">ID Wilayah</span>
                <span id="detail_id_wilayah" class="font-semibold text-gray-800">-</span>
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
    setTimeout(function() {
        const alertBoxes = document.querySelectorAll('.alert-box');
        alertBoxes.forEach(alertBox => {
            alertBox.style.opacity = '0';
            setTimeout(() => alertBox.remove(), 500);
        });
    }, 3000);

    function toggleDropdown(e, id) {
        e.stopPropagation();
        const dropdown = document.getElementById(id);
        document.querySelectorAll('[id^="filter"]').forEach(el => {
            if (el.id !== id) el.classList.add('hidden');
        });
        dropdown.classList.toggle('hidden');
    }

    window.addEventListener('click', function() {
        document.querySelectorAll('[id^="filter"]').forEach(el => {
            el.classList.add('hidden');
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Event Listener untuk Tombol Edit
        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function() {
                const data = JSON.parse(this.getAttribute('data-item'));
                openEditModal(data);
            });
        });

        // Event Listener untuk Tombol Detail
        document.querySelectorAll('.btn-detail').forEach(button => {
            button.addEventListener('click', function() {
                const data = JSON.parse(this.getAttribute('data-item'));
                openDetailModal(data);
            });
        });

        const checkAll = document.getElementById('checkAll');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const bulkDeleteContainer = document.getElementById('bulkDeleteContainer');
        const selectedCountSpan = document.getElementById('selectedCount');

        if(checkAll) {
            checkAll.addEventListener('change', function() {
                rowCheckboxes.forEach(cb => {
                    cb.checked = this.checked;
                });
                updateBulkDeleteButton();
            });
        }

        rowCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                updateBulkDeleteButton();
                const allChecked = Array.from(rowCheckboxes).every(c => c.checked);
                const someChecked = Array.from(rowCheckboxes).some(c => c.checked);
                checkAll.checked = allChecked;
                checkAll.indeterminate = someChecked && !allChecked;
            });
        });

        function updateBulkDeleteButton() {
            const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
            if (checkedBoxes.length > 0) {
                bulkDeleteContainer.classList.remove('hidden');
                bulkDeleteContainer.classList.add('block');
                selectedCountSpan.textContent = checkedBoxes.length;
            } else {
                bulkDeleteContainer.classList.add('hidden');
                bulkDeleteContainer.classList.remove('block');
            }
        }
    });

    function confirmBulkDelete() {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        if (checkedBoxes.length === 0) return;

        if (confirm(`Yakin ingin menghapus ${checkedBoxes.length} data terpilih secara permanen?`)) {
            const ids = Array.from(checkedBoxes).map(cb => cb.value);
            document.getElementById('selectedIds').value = ids.join(',');
            document.getElementById('bulkDeleteForm').submit();
        }
    }

    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.getElementById(id).classList.add('flex');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.getElementById(id).classList.remove('flex');
    }

    function openEditModal(data) {
        let updateUrl = "{{ route('admin.wilayah.update', ':id') }}";
        updateUrl = updateUrl.replace(':id', data.id_wilayah);
        
        document.getElementById('formEdit').action = updateUrl;
        document.getElementById('edit_nama_provinsi').value = data.nama_provinsi ?? '';
        document.getElementById('edit_kode_nama_kabkota').value = data.kode_nama_kabkota ?? '';
        document.getElementById('edit_kode_nama_kecamatan').value = data.kode_nama_kecamatan ?? '';
        document.getElementById('edit_kode_nama_desa').value = data.kode_nama_desa ?? '';
        document.getElementById('edit_kode_nama_sls').value = data.kode_nama_sls ?? '';
        document.getElementById('edit_kode_nama_sub_sls').value = data.kode_nama_sub_sls ?? '';
        
        openModal('modalEdit');
    }

    function openDetailModal(data) {
        document.getElementById('detail_id_wilayah').innerText = data.id_wilayah ?? '-';
        document.getElementById('detail_nama_provinsi').innerText = data.nama_provinsi ?? '-';
        document.getElementById('detail_kode_nama_kabkota').innerText = data.kode_nama_kabkota ?? '-';
        document.getElementById('detail_kode_nama_kecamatan').innerText = data.kode_nama_kecamatan ?? '-';
        document.getElementById('detail_kode_nama_desa').innerText = data.kode_nama_desa ?? '-';
        document.getElementById('detail_kode_nama_sls').innerText = data.kode_nama_sls ?? '-';
        document.getElementById('detail_kode_nama_sub_sls').innerText = data.kode_nama_sub_sls ?? '-';
        
        openModal('modalDetail');
    }
</script>
@endsection