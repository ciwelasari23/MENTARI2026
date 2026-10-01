@extends('layouts.admin')

@section('title', 'Evaluasi Kegiatan - MENTARI')
@section('header', 'Evaluasi & Penilaian Kegiatan')

@section('content')
<div class="space-y-6">

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

    {{-- ===== LEGENDA SKORING MANUAL & BINTANG (0-100) ===== --}}
    <div class="bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2.5 shrink-0">
                <div class="w-2 h-4 bg-blue-600 rounded-full"></div>
                <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Rentang Skoring & Rating</h3>
            </div>
            
            <div class="flex flex-nowrap items-center gap-2 overflow-x-auto">
                @foreach ([
                    ['90 - 100', 'bg-emerald-50 border-emerald-200 text-emerald-800', 'Sangat Baik', 5],
                    ['75 - 89', 'bg-teal-50 border-teal-200 text-teal-800', 'Baik', 4],
                    ['60 - 74', 'bg-blue-50 border-blue-200 text-blue-800', 'Cukup', 3],
                    ['40 - 59', 'bg-amber-50 border-amber-200 text-amber-800', 'Kurang', 2],
                    ['< 40', 'bg-rose-50 border-rose-200 text-rose-800', 'Sangat Kurang', 1],
                ] as [$range, $classes, $label, $stars])
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border {{ $classes }} text-xs font-semibold shadow-2xs whitespace-nowrap shrink-0">
                    <div class="flex text-amber-400">
                        @for($s = 1; $s <= 5; $s++)
                            <svg class="w-3.5 h-3.5 {{ $s <= $stars ? 'fill-amber-400 text-amber-400' : 'fill-gray-300 text-gray-300' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
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

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-base font-bold text-gray-800">Rekapitulasi Penilaian & Status Kegiatan</h3>
                <p class="text-xs text-gray-500 mt-0.5">Daftar rekapitulasi penilaian dan status kegiatan berdasarkan target wilayah.</p>
            </div>

            <form id="form-entries" action="{{ route('evaluasi.index') }}" method="GET" class="flex items-center gap-2 text-xs text-gray-600">
                @foreach(request()->except(['page', 'perPage', '_token']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <select name="perPage" onchange="document.getElementById('form-entries').submit()" class="border border-gray-200 rounded-lg px-2.5 py-1.5 bg-white text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="10" {{ request('perPage', 10) == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('perPage') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('perPage') == 100 ? 'selected' : '' }}>100</option>
                </select>
                <span>entries per page</span>
            </form>
        </div>

        @if(count($evaluasiData ?? []) > 0)
        <div class="overflow-x-auto border border-gray-100 rounded-xl">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/70 border-b border-gray-100 text-gray-500 text-[11px] uppercase tracking-wider font-semibold">
                        <th class="py-3.5 px-4 font-bold">Kegiatan & Wilayah</th>
                        <th class="py-3.5 px-4 text-center font-bold">Target vs Realisasi</th>
                        <th class="py-3.5 px-4 text-center font-bold">Status Kegiatan</th>
                        <th class="py-3.5 px-4 text-center font-bold">Skor Manual (0-100)</th>
                        <th class="py-3.5 px-4 text-center font-bold">Rating Bintang</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
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
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        {{-- Kegiatan & Wilayah --}}
                        <td class="py-3.5 px-4">
                            <span class="font-bold text-gray-800 block text-sm">{{ $row['nama_kegiatan'] }}</span>
                            <span class="inline-block px-2 py-0.5 rounded-lg text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200 mt-1">
                                {{ $row['proses']->nama_proses ?? 'Proses' }}
                            </span>
                            <span class="text-xs font-medium text-blue-600 block mt-1">
                                📍 {{ trim(str_ireplace(['RIAU', 'PROVINSI RIAU'], '', $row['wilayah'] ?? '')) }}
                            </span>
                        </td>

                        <td class="py-3.5 px-4 text-center">
                            <div class="text-xs font-semibold text-gray-700">
                                Target: <span class="text-gray-900">{{ number_format($targetKab) }}</span> | 
                                Realisasi: <span class="text-blue-600 font-bold">{{ number_format($realisasiKab) }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2 mt-2 max-w-[140px] mx-auto overflow-hidden shadow-2xs">
                                <div class="bg-blue-600 h-2 rounded-full" :style="'width: ' + {{ $persenBar }} + '%'"></div>
                            </div>
                        </td>

                        <td class="py-3.5 px-4 text-center">
                            @if($statusKegiatan == 'selesai')
                                <span class="bg-emerald-100 text-emerald-700 font-bold px-3 py-1 rounded-xl text-xs uppercase tracking-wide">Selesai</span>
                            @else
                                <span class="bg-amber-100 text-amber-700 font-bold px-3 py-1 rounded-xl text-xs uppercase tracking-wide">Dalam Proses</span>
                            @endif
                        </td>

                        <td class="py-3.5 px-4 text-center">
                            @if($skorManual !== null)
                                <span class="text-xs font-black text-gray-800 bg-gray-100 px-3 py-1 rounded-xl">{{ $skorManual }} / 100</span>
                            @else
                                <span class="text-xs text-gray-400 italic">Belum dinilai</span>
                            @endif
                        </td>

                        <td class="py-3.5 px-4 text-center">
                            @if($skorManual !== null)
                                <div class="flex justify-center items-center gap-1 text-amber-400 mb-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $rowStars ? 'fill-amber-400 text-amber-400' : 'fill-gray-200 text-gray-200' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endfor
                                </div>
                                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">{{ $rowLabel }}</span>
                            @else
                                <span class="text-xs text-gray-400 italic">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if(isset($targetsPaginator) && method_exists($targetsPaginator, 'links'))
        <div class="flex flex-col sm:flex-row justify-between items-center text-xs text-gray-500 pt-3 gap-3">
            <div>
                Menampilkan <span class="font-semibold text-gray-700">{{ $targetsPaginator->firstItem() ?? 0 }}</span> ke <span class="font-semibold text-gray-700">{{ $targetsPaginator->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-gray-700">{{ number_format($targetsPaginator->total(), 0, ',', '.') }}</span> entri
            </div>
            
            <div class="flex items-center gap-1.5">
                @if ($targetsPaginator->onFirstPage())
                    <span class="px-3.5 py-2 border border-gray-200 rounded-xl bg-gray-50 text-gray-300 cursor-not-allowed flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </span>
                @else
                    <a href="{{ $targetsPaginator->previousPageUrl() }}" class="px-3.5 py-2 border border-gray-300 rounded-xl bg-white text-gray-700 hover:bg-gray-50 flex items-center justify-center transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </a>
                @endif

                <span class="px-4 py-2 border border-blue-500 bg-blue-500 text-white font-bold rounded-xl text-xs shadow-sm">
                    {{ $targetsPaginator->currentPage() }}
                </span>

                @if ($targetsPaginator->hasMorePages())
                    <a href="{{ $targetsPaginator->nextPageUrl() }}" class="px-3.5 py-2 border border-gray-300 rounded-xl bg-white text-gray-700 hover:bg-gray-50 flex items-center justify-center transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                @else
                    <span class="px-3.5 py-2 border border-gray-200 rounded-xl bg-gray-50 text-gray-300 cursor-not-allowed flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </span>
                @endif
            </div>
        </div>
        @endif

        @else
        <div class="text-center py-16 text-gray-400 bg-gray-50/50 rounded-xl">
            <h4 class="text-gray-600 font-semibold mb-1 text-sm">Tidak Ada Data</h4>
            <p class="text-xs">Belum ada rekapitulasi penilaian wilayah yang tersedia.</p>
        </div>
        @endif
    </div>

</div>
@endsection