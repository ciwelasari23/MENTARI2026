@extends('layouts.admin')

@section('title', 'Evaluasi Kegiatan - MENTARI')
@section('header', 'Evaluasi & Penilaian Kegiatan')

@section('content')
<div class="space-y-6">

    {{-- ===== KARTU RATING KESELURUHAN ===== --}}
    <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-gradient-to-br from-blue-50 to-blue-100 rounded-full opacity-50 blur-2xl"></div>
        
        <div class="flex flex-col sm:flex-row items-center gap-6 sm:gap-8 relative z-10">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100 text-blue-600 shadow-inner flex-shrink-0 border border-blue-100/50">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
            </div>
            
            <div class="flex-1 text-center sm:text-left">
                <h2 class="text-sm uppercase tracking-wider text-gray-500 font-semibold mb-2">Rata-rata Skor Keseluruhan</h2>
                <div class="flex flex-col sm:flex-row sm:items-end gap-3 sm:gap-6 mt-1 justify-center sm:justify-start">
                    <span class="text-5xl font-black text-gray-800 tracking-tight">{{ $rataPersentase ?? 0 }}<span class="text-2xl text-gray-400 font-medium">/100</span></span>
                    <div class="flex items-center gap-1 text-amber-400 pb-1">
                        @php
                            $avgVal = $rataPersentase ?? 0;
                            $starCount = $avgVal >= 90 ? 5 : ($avgVal >= 75 ? 4 : ($avgVal >= 60 ? 3 : ($avgVal >= 40 ? 2 : 1)));
                            if($avgVal == 0) $starCount = 0;
                        @endphp
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-5 h-5 {{ $i <= $starCount ? 'text-amber-400 fill-amber-400' : 'text-gray-200 fill-gray-200' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                </div>
                <p class="text-sm text-gray-500 mt-2">Berdasarkan penilaian manual <span class="font-semibold text-blue-600">Skala 0 - 100</span> (Bukti Dukung, Kualitas, & Ketepatan).</p>
            </div>
            
            <div class="flex-shrink-0 mt-4 sm:mt-0 w-full sm:w-auto">
                <a href="{{ route('evaluasi.export-pdf') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-red-600 text-white font-semibold text-sm rounded-xl hover:bg-red-700 transition shadow-sm hover:shadow group w-full">
                    <svg class="w-5 h-5 text-red-200 group-hover:text-white transition-colors" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM13 9V3.5L18.5 9H13z"/>
                    </svg>
                    Unduh Rekap PDF
                </a>
            </div>
        </div>
    </div>

    {{-- ===== LEGENDA SKORING MANUAL & BINTANG (0-100) ===== --}}
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <h3 class="text-sm font-bold text-gray-700 whitespace-nowrap">Rentang Skoring & Rating :</h3>
            <div class="flex flex-wrap gap-2.5">
                @foreach ([
                    ['90 - 100', 'bg-green-50 border-green-200 text-green-700', 'Sangat Baik', 5],
                    ['75 - 89', 'bg-teal-50 border-teal-200 text-teal-700', 'Baik', 4],
                    ['60 - 74', 'bg-blue-50 border-blue-200 text-blue-700', 'Cukup', 3],
                    ['40 - 59', 'bg-yellow-50 border-yellow-200 text-yellow-700', 'Kurang', 2],
                    ['< 40', 'bg-red-50 border-red-200 text-red-700', 'Sangat Kurang', 1],
                ] as [$range, $classes, $label, $stars])
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-full border {{ $classes }}">
                    <div class="flex text-amber-400">
                        @for($s = 1; $s <= 5; $s++)
                            <svg class="w-3 h-3 {{ $s <= $stars ? 'fill-amber-400 text-amber-400' : 'fill-gray-300 text-gray-300' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <span class="text-xs font-bold">{{ $range }}</span>
                    <span class="text-xs font-semibold">— {{ $label }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ===== TABEL EVALUASI & STATUS SELESAI ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-white">
            <h3 class="text-lg font-bold text-gray-800">Rekapitulasi Penilaian & Status Kegiatan</h3>
            <p class="text-sm text-gray-500 mt-1">Kelola status selesai dan input nilai skoring manual (0-100) berdasarkan target wilayah.</p>
        </div>

        @if(count($evaluasiData ?? []) > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50/80 border-b text-gray-500 text-xs uppercase tracking-wider font-semibold">
                        <th class="px-6 py-4">Kegiatan & Wilayah</th>
                        <th class="px-4 py-4 text-center">Target vs Realisasi</th>
                        <th class="px-4 py-4 text-center">Status Kegiatan</th>
                        <th class="px-4 py-4 text-center">Skor Manual (0-100)</th>
                        <th class="px-4 py-4 text-center">Rating Bintang</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @foreach($evaluasiData as $row)
                    @php
                        $statusKegiatan = $row['status_kegiatan'] ?? 'proses'; 
                        $skorManual = $row['skor_manual'] ?? null;
                        $targetKab = $row['total_target'] ?? 100;
                        $realisasiKab = $row['total_realisasi'] ?? 90;
                        $persenBar = min(100, ($targetKab > 0 ? ($realisasiKab / $targetKab) * 100 : 0));
                        
                        // Hitung jumlah bintang berdasarkan skor manual
                        $rowStars = 0;
                        $rowLabel = 'Belum Dinilai';
                        if($skorManual !== null) {
                            if($skorManual >= 90) { $rowStars = 5; $rowLabel = 'Sangat Baik'; }
                            elseif($skorManual >= 75) { $rowStars = 4; $rowLabel = 'Baik'; }
                            elseif($skorManual >= 60) { $rowStars = 3; $rowLabel = 'Cukup'; }
                            elseif($skorManual >= 40) { $rowStars = 2; $rowLabel = 'Kurang'; }
                            else { $rowStars = 1; $rowLabel = 'Sangat Kurang'; }
                        }
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        {{-- Kegiatan & Wilayah --}}
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-800 text-base mb-1">{{ $row['nama_kegiatan'] }}</div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                {{ $row['proses']->nama_proses ?? 'Proses' }}
                            </span>
                            <div class="text-xs font-medium text-blue-600 mt-1">
                                📍 {{ trim(str_ireplace(['RIAU', 'PROVINSI RIAU'], '', $row['wilayah'] ?? '')) }}
                            </div>
                        </td>

                        {{-- Target vs Realisasi --}}
                        <td class="px-4 py-4 text-center">
                            <div class="text-xs font-semibold text-gray-700">
                                Target: <span class="text-gray-900">{{ number_format($targetKab) }}</span> | 
                                Realisasi: <span class="text-blue-600 font-bold">{{ number_format($realisasiKab) }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5 mt-2 max-w-[150px] mx-auto overflow-hidden">
                                <div class="bg-blue-600 h-1.5 rounded-full" :style="'width: ' + {{ $persenBar }} + '%'"></div>
                            </div>
                        </td>

                        {{-- Status Kegiatan --}}
                        <td class="px-4 py-4 text-center">
                            @if($statusKegiatan == 'selesai')
                                <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold px-3 py-1 rounded-full text-[10px] uppercase">Selesai</span>
                            @else
                                <span class="bg-amber-50 text-amber-700 border border-amber-200 font-bold px-3 py-1 rounded-full text-[10px] uppercase">Dalam Proses</span>
                            @endif
                        </td>

                        {{-- Skor Manual (0-100) --}}
                        <td class="px-4 py-4 text-center">
                            @if($skorManual !== null)
                                <span class="text-base font-black text-gray-800 bg-gray-100 px-3 py-1 rounded-lg">{{ $skorManual }} / 100</span>
                            @else
                                <span class="text-xs text-gray-400 italic">Belum dinilai</span>
                            @endif
                        </td>

                        {{-- Rating Bintang --}}
                        <td class="px-4 py-4 text-center">
                            @if($skorManual !== null)
                                <div class="flex justify-center items-center gap-0.5 text-amber-400 mb-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $rowStars ? 'fill-amber-400 text-amber-400' : 'fill-gray-200 text-gray-200' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endfor
                                </div>
                                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">{{ $rowLabel }}</span>
                            @else
                                <span class="text-xs text-gray-400 italic">-</span>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="px-6 py-4 text-center whitespace-nowrap" x-data="{ openModalNilai: false }">
                            <div class="flex items-center justify-center gap-2">
                                @if($statusKegiatan !== 'selesai')
                                    <button type="button" 
                                        @click="
                                            let target = {{ $targetKab }};
                                            let realisasi = {{ $realisasiKab }};
                                            if(realisasi !== target) {
                                                if(confirm('Peringatan: Target wilayah belum tercapai sepenuhnya (Target: ' + target + ', Realisasi: ' + realisasi + '). Yakin ingin menandai kegiatan ini sebagai Selesai?')) {
                                                    alert('Status diubah jadi selesai. Silakan berikan nilai.');
                                                }
                                            } else {
                                                if(confirm('Target sudah sesuai. Tandai kegiatan sebagai Selesai?')) {
                                                    alert('Status selesai dikonfirmasi.');
                                                }
                                            }
                                        "
                                        class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition shadow-sm">
                                        Tandai Selesai
                                    </button>
                                @else
                                    <button @click="openModalNilai = true" class="bg-blue-600 hover:bg-blue-700 text-white px-3.5 py-1.5 rounded-lg text-xs font-bold transition shadow-sm inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        {{ $skorManual !== null ? 'Ubah Nilai' : 'Beri Nilai (0-100)' }}
                                    </button>
                                @endif
                            </div>

                            {{-- Modal Input Nilai Skoring Manual (0-100) --}}
                            <div x-show="openModalNilai" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4" style="display: none;" x-transition.opacity>
                                <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 text-left space-y-4" @click.away="openModalNilai = false" x-transition.scale>
                                    <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                                        <h4 class="font-bold text-gray-800 text-sm">Input Nilai Skoring (0 - 100)</h4>
                                        <button @click="openModalNilai = false" class="text-gray-400 hover:text-gray-600 font-bold text-lg">&times;</button>
                                    </div>
                                    <form action="#" method="POST">
                                        @csrf
                                        <div class="space-y-3">
                                            <p class="text-xs text-gray-500">Berikan penilaian berdasarkan kelengkapan bukti dukung, ketepatan waktu, dan kualitas pekerjaan.</p>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-700 mb-1">Skor Penilaian (0 - 100)</label>
                                                <input type="number" name="skor_manual" min="0" max="100" value="{{ $skorManual ?? '' }}" required class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none font-bold text-blue-600">
                                            </div>
                                        </div>
                                        <div class="flex justify-end gap-2 mt-5 pt-3 border-t border-gray-100">
                                            <button type="button" @click="openModalNilai = false" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold">Batal</button>
                                            <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold shadow">Simpan Nilai</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-16 text-gray-400 bg-gray-50/50">
            <h4 class="text-gray-600 font-semibold mb-1">Tidak Ada Data</h4>
            <p class="text-sm">Belum ada rekapitulasi penilaian wilayah yang tersedia.</p>
        </div>
        @endif
    </div>

</div>
@endsection