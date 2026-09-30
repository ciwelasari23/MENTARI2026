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
                <h2 class="text-xs uppercase tracking-wider text-gray-400 font-bold mb-2">Rata-rata Skor Keseluruhan</h2>
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

    {{-- ===== LEGENDA SKORING MANUAL & BINTANG (0-100) - 100% SATU BARIS LURUS ===== --}}
    <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2 shrink-0">
                <div class="w-2 h-4 bg-blue-600 rounded-full"></div>
                <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Rentang Skoring & Rating</h3>
            </div>
            
            <div class="flex flex-nowrap items-center gap-1.5 overflow-x-auto">
                @foreach ([
                    ['90 - 100', 'bg-emerald-50 border-emerald-200 text-emerald-800', 'Sangat Baik', 5],
                    ['75 - 89', 'bg-teal-50 border-teal-200 text-teal-800', 'Baik', 4],
                    ['60 - 74', 'bg-blue-50 border-blue-200 text-blue-800', 'Cukup', 3],
                    ['40 - 59', 'bg-amber-50 border-amber-200 text-amber-800', 'Kurang', 2],
                    ['< 40', 'bg-rose-50 border-rose-200 text-rose-800', 'Sangat Kurang', 1],
                ] as [$range, $classes, $label, $stars])
                <div class="inline-flex items-center gap-1 px-2 py-1 rounded-lg border {{ $classes }} text-[11px] font-semibold shadow-2xs whitespace-nowrap shrink-0">
                    <div class="flex text-amber-400">
                        @for($s = 1; $s <= 5; $s++)
                            <svg class="w-2.5 h-2.5 {{ $s <= $stars ? 'fill-amber-400 text-amber-400' : 'fill-gray-300 text-gray-300' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <span class="font-bold">{{ $range }}</span>
                    <span class="text-gray-400 font-normal">|</span>
                    <span>{{ $label }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ===== TABEL EVALUASI & STATUS SELESAI ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-white">
            <h3 class="text-lg font-bold text-gray-800">Rekapitulasi Penilaian & Status Kegiatan</h3>
            <p class="text-sm text-gray-500 mt-1">Daftar rekapitulasi penilaian dan status kegiatan berdasarkan target wilayah.</p>
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
                        <th class="px-6 py-4 text-center">Rating Bintang</th>
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
                        <td class="px-6 py-4 text-center">
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