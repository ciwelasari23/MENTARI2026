@extends('layouts.admin')

@section('title', 'Target Wilayah')
@section('header', 'Manajemen Target Wilayah')

@section('content')
<div class="space-y-6" x-data="{ modalTambah: false, modalEdit: false, modalDetail: false, activeTarget: {} }">
    
    <!-- Bagian Header Tombol Aksi -->
    <div class="flex justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h3 class="text-base font-bold text-gray-800">Daftar Target Beban Wilayah (Kabupaten/Kota)</h3>
            <p class="text-xs text-gray-500 mt-0.5">Kelola penetapan target beban kegiatan untuk masing-masing wilayah.</p>
        </div>
        <button @click="modalTambah = true" class="bg-[#005A9C] hover:bg-[#004070] text-white text-sm font-semibold px-4 py-2 rounded-lg shadow transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tetapkan Target
        </button>
    </div>

    <!-- Notifikasi Sukses -->
    @if(session('success'))
        <div x-data="{ show: true }" 
             x-init="setTimeout(() => show = false, 3000)" 
             x-show="show" 
             x-transition.duration.500ms
             class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm p-4 rounded-xl shadow-sm flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button @click="show = false" class="text-emerald-600 hover:text-emerald-800 font-bold">&times;</button>
        </div>
    @endif

    <!-- Tabel Data Target Wilayah -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-600 font-semibold">
                        <th class="p-4 text-center w-16">No</th>
                        <th class="p-4">Wilayah (Kab/Kota)</th>
                        <th class="p-4">Proses Kegiatan (Level 4)</th>
                        <th class="p-4 text-center">Target Daerah</th>
                        <th class="p-4 text-center">Ditetapkan Oleh</th>
                        <th class="p-4 text-center w-48">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($targets as $index => $target)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="p-4 text-center font-medium text-gray-500">{{ $index + 1 }}</td>
                        <td class="p-4 font-bold text-gray-800">
                            {{ $target->wilayah->nama_wilayah ?? '-' }}
                        </td>
                        <td class="p-4">
                            <span class="text-gray-800 font-medium">{{ $target->proses->nama_proses ?? '-' }}</span>
                            <div class="text-xs text-gray-400">Satuan: {{ $target->proses->satuan_target ?? '-' }}</div>
                        </td>
                        <td class="p-4 text-center">
                            <span class="bg-blue-50 text-[#005A9C] font-bold px-3 py-1 rounded-full text-xs">
                                {{ number_format($target->target_daerah, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="p-4 text-center text-xs text-gray-500">
                            {{ $target->pembuat->nama_lengkap ?? 'Administrator' }}
                        </td>
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <!-- Tombol Detail -->
                                <button @click="activeTarget = {{ json_encode($target) }}; modalDetail = true" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs px-3 py-1.5 rounded shadow transition-colors">
                                    Detail
                                </button>
                                <!-- Tombol Edit -->
                                <button @click="activeTarget = {{ json_encode($target) }}; modalEdit = true" class="bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs px-3 py-1.5 rounded shadow transition-colors">
                                    Edit
                                </button>
                                <!-- Tombol Hapus -->
                                <form action="{{ route('admin.target.destroy', $target->id_target_wilayah) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus target wilayah ini?')" class="inline-block">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-semibold text-xs px-3 py-1.5 rounded shadow transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-400 italic">
                            Belum ada data target wilayah yang ditetapkan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form Tambah Target -->
    <div x-show="modalTambah" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4" style="display: none;" x-transition.opacity>
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 space-y-4" @click.away="modalTambah = false" x-transition.scale>
            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                <h3 class="text-base font-bold text-gray-800">Tetapkan Target Wilayah Baru</h3>
                <button @click="modalTambah = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form action="{{ route('admin.target.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Wilayah (Kabupaten/Kota)</label>
                    <select name="id_wilayah" required class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#005A9C]">
                        <option value="">-- Pilih Kabupaten/Kota --</option>
                        @foreach($wilayahs as $w)
                            <option value="{{ $w->id_wilayah }}">{{ $w->nama_wilayah }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Proses Kegiatan (Level 4)</label>
                    <select name="id_proses" required class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#005A9C]">
                        <option value="">-- Pilih Proses Kegiatan --</option>
                        @foreach($prosesList as $p)
                            <option value="{{ $p->id_proses }}">{{ $p->nama_proses }} (Satuan: {{ $p->satuan_target }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Jumlah Target Daerah</label>
                    <input type="number" name="target_daerah" min="1" required class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#005A9C]" placeholder="Masukkan jumlah target, cth: 100">
                </div>
                <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                    <button type="button" @click="modalTambah = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-[#005A9C] hover:bg-[#004070] text-white rounded-lg text-sm font-semibold shadow">Simpan Target</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Form Edit Target -->
    <div x-show="modalEdit" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4" style="display: none;" x-transition.opacity>
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 space-y-4" @click.away="modalEdit = false" x-transition.scale>
            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                <h3 class="text-base font-bold text-gray-800">Edit Target Wilayah</h3>
                <button @click="modalEdit = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form :action="'/admin/target/' + activeTarget.id_target_wilayah" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Wilayah (Kabupaten/Kota)</label>
                    <select name="id_wilayah" x-model="activeTarget.id_wilayah" required class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#005A9C]">
                        <option value="">-- Pilih Kabupaten/Kota --</option>
                        @foreach($wilayahs as $w)
                            <option value="{{ $w->id_wilayah }}">{{ $w->nama_wilayah }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Proses Kegiatan (Level 4)</label>
                    <select name="id_proses" x-model="activeTarget.id_proses" required class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#005A9C]">
                        <option value="">-- Pilih Proses Kegiatan --</option>
                        @foreach($prosesList as $p)
                            <option value="{{ $p->id_proses }}">{{ $p->nama_proses }} (Satuan: {{ $p->satuan_target }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Jumlah Target Daerah</label>
                    <input type="number" name="target_daerah" x-model="activeTarget.target_daerah" min="1" required class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#005A9C]">
                </div>
                <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                    <button type="button" @click="modalEdit = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-sm font-semibold shadow">Perbarui Target</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail Target -->
    <div x-show="modalDetail" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4" style="display: none;" x-transition.opacity>
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 space-y-4" @click.away="modalDetail = false" x-transition.scale>
            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                <h3 class="text-base font-bold text-gray-800">Detail Target Wilayah</h3>
                <button @click="modalDetail = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div class="space-y-3 text-sm">
                <div>
                    <span class="block text-xs text-gray-400 font-semibold">Wilayah (Kabupaten/Kota)</span>
                    <span class="font-bold text-gray-800" x-text="activeTarget.wilayah ? activeTarget.wilayah.nama_wilayah : '-'"></span>
                </div>
                <div>
                    <span class="block text-xs text-gray-400 font-semibold">Proses Kegiatan (Level 4)</span>
                    <span class="font-bold text-gray-800" x-text="activeTarget.proses ? activeTarget.proses.nama_proses : '-'"></span>
                </div>
                <div>
                    <span class="block text-xs text-gray-400 font-semibold">Satuan Target</span>
                    <span class="text-gray-700" x-text="activeTarget.proses ? activeTarget.proses.satuan_target : '-'"></span>
                </div>
                <div>
                    <span class="block text-xs text-gray-400 font-semibold">Jumlah Target Daerah</span>
                    <span class="font-bold text-[#005A9C]" x-text="activeTarget.target_daerah"></span>
                </div>
                <div>
                    <span class="block text-xs text-gray-400 font-semibold">Ditetapkan Oleh</span>
                    <span class="text-gray-700" x-text="activeTarget.pembuat ? activeTarget.pembuat.nama_lengkap : 'Administrator'"></span>
                </div>
            </div>
            <div class="flex justify-end pt-4 border-t border-gray-100">
                <button type="button" @click="modalDetail = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold">Tutup</button>
            </div>
        </div>
    </div>

</div>
@endsection