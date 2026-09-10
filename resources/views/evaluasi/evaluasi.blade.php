@extends('layouts.admin')

@section('title', 'Evaluasi Kegiatan - MENTARI')
@section('header', 'Evaluasi Kegiatan')

@section('content')
<div class="space-y-6">

    {{-- ===== KARTU RATING KESELURUHAN ===== --}}
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex flex-col sm:flex-row items-center gap-6">
            {{-- Ikon --}}
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 text-[#005A9C] flex-shrink-0">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
            </div>
            {{-- Info Keseluruhan --}}
            <div class="flex-1 text-center sm:text-left">
                <p class="text-sm text-gray-500 font-medium">Rata-rata Capaian Keseluruhan</p>
                <div class="flex items-center justify-center sm:justify-start gap-3 mt-1">
                    {{-- Bintang Keseluruhan --}}
                    <div class="flex items-center gap-0.5">
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= $ratingKeseluruhan)
                                <svg class="w-7 h-7 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @else
                                <svg class="w-7 h-7 text-gray-200" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endif
                        @endfor
                    </div>
                    <span class="text-3xl font-extrabold text-gray-800">{{ $rataPersentase }}%</span>
                </div>
                <p class="text-xs text-gray-400 mt-1">Dihitung dari rata-rata capaian semua proses kegiatan (hanya laporan <span class="font-semibold text-green-600">approved</span>)</p>
            </div>
            {{-- Badge Keterangan --}}
            <div class="flex-shrink-0">
                @php
                    $badgeConfig = match(true) {
                        $rataPersentase >= 100 => ['bg-green-100',  'text-green-700',  '⭐ Sangat Baik'],
                        $rataPersentase >= 85  => ['bg-teal-100',   'text-teal-700',   '👍 Baik'],
                        $rataPersentase >= 70  => ['bg-blue-100',   'text-blue-700',   '🙂 Cukup'],
                        $rataPersentase >= 50  => ['bg-yellow-100', 'text-yellow-700', '⚠️ Kurang'],
                        default                => ['bg-red-100',    'text-red-700',    '❌ Sangat Kurang'],
                    };
                @endphp
                <span class="px-4 py-2 rounded-full text-sm font-bold {{ $badgeConfig[0] }} {{ $badgeConfig[1] }}">
                    {{ $badgeConfig[2] }}
                </span>
            </div>
        </div>
    </div>

    {{-- ===== RUMUS PERHITUNGAN ===== --}}
    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-5 rounded-xl border border-blue-100">
        <div class="flex items-center gap-2 mb-4">
            <div class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-[#005A9C] text-white flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h4 class="text-sm font-bold text-[#005A9C] uppercase tracking-wide">Rumus Perhitungan Rating</h4>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Rumus Capaian --}}
            <div class="bg-white rounded-lg p-4 border border-blue-100 shadow-sm">
                <p class="text-xs font-bold text-gray-500 uppercase mb-3">① Persentase Capaian</p>
                <div class="flex items-center justify-center gap-3 py-3">
                    <div class="text-center">
                        <div class="text-xs text-gray-500 mb-1">Capaian (%)</div>
                        <div class="text-lg font-extrabold text-[#005A9C]">C</div>
                    </div>
                    <div class="text-gray-400 text-xl font-light">=</div>
                    <div class="text-center">
                        <div class="border-b-2 border-gray-700 pb-1 mb-1 text-center">
                            <span class="text-sm font-semibold text-[#14B8A6]">Σ Realisasi <span class="text-xs font-normal text-gray-400">(approved)</span></span>
                        </div>
                        <div class="text-sm font-semibold text-gray-700">Total Target</div>
                    </div>
                    <div class="text-gray-400 text-xl font-light">×</div>
                    <div class="text-center">
                        <div class="text-lg font-extrabold text-gray-700">100</div>
                        <div class="text-xs text-gray-400">%</div>
                    </div>
                </div>
                <p class="text-xs text-gray-400 text-center mt-1 italic">
                    Hanya laporan dengan status <span class="font-semibold text-green-600">approved</span> yang dihitung
                </p>
            </div>

            {{-- Konversi ke Bintang --}}
            <div class="bg-white rounded-lg p-4 border border-blue-100 shadow-sm">
                <p class="text-xs font-bold text-gray-500 uppercase mb-3">② Konversi ke Rating Bintang</p>
                <div class="space-y-1.5">
                    @foreach ([
                        ['C ≥ 100%',       5, 'text-green-600',  'bg-green-50'],
                        ['85% ≤ C < 100%', 4, 'text-teal-600',   'bg-teal-50'],
                        ['70% ≤ C < 85%',  3, 'text-blue-600',   'bg-blue-50'],
                        ['50% ≤ C < 70%',  2, 'text-yellow-600', 'bg-yellow-50'],
                        ['C < 50%',        1, 'text-red-600',    'bg-red-50'],
                    ] as [$kondisi, $bintang, $textColor, $bgColor])
                    <div class="flex items-center justify-between px-3 py-1.5 rounded-lg {{ $bgColor }}">
                        <span class="text-xs font-mono font-semibold {{ $textColor }}">{{ $kondisi }}</span>
                        <div class="flex items-center gap-1">
                            <span class="text-yellow-400 text-xs">
                                {{ str_repeat('★', $bintang) }}{{ str_repeat('☆', 5 - $bintang) }}
                            </span>
                            <span class="text-xs font-bold {{ $textColor }}">{{ $bintang }}/5</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- Catatan --}}
        <div class="mt-3 flex items-start gap-2 text-xs text-blue-600 bg-blue-100 px-3 py-2 rounded-lg">
            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>
                <strong>Rating keseluruhan</strong> dihitung dari rata-rata persentase capaian semua proses kegiatan.
                Persentase bisa melebihi 100% jika realisasi melampaui target, namun bintang maksimal tetap 5.
            </span>
        </div>
    </div>

    {{-- ===== LEGENDA RATING ===== --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <p class="text-xs font-bold text-gray-500 uppercase mb-3">Keterangan Skala Rating</p>
        <div class="flex flex-wrap gap-3">
            @foreach ([
                ['≥ 100%', 5, 'bg-green-100',  'text-green-700',  'Sangat Baik'],
                ['85–99%', 4, 'bg-teal-100',   'text-teal-700',   'Baik'],
                ['70–84%', 3, 'bg-blue-100',   'text-blue-700',   'Cukup'],
                ['50–69%', 2, 'bg-yellow-100', 'text-yellow-700', 'Kurang'],
                ['< 50%',  1, 'bg-red-100',    'text-red-700',    'Sangat Kurang'],
            ] as [$range, $star, $bg, $text, $label])
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg {{ $bg }}">
                <span class="text-yellow-400 text-sm leading-none">
                    {{ str_repeat('★', $star) }}
                </span>
                <span class="text-xs font-semibold {{ $text }}">{{ $range }} — {{ $label }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ===== TABEL EVALUASI PER PROSES ===== --}}
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Rekapitulasi Capaian Per Proses Kegiatan</h3>

        @if(count($evaluasiData) > 0)
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-gray-50 border-b text-gray-600 text-xs uppercase">
                    <th class="p-3">Kegiatan</th>
                    <th class="p-3">Detail / Proses</th>
                    <th class="p-3 text-right">Target</th>
                    <th class="p-3 text-right">Realisasi</th>
                    <th class="p-3 text-center w-48">Capaian</th>
                    <th class="p-3 text-center">Rating</th>
                    <th class="p-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($evaluasiData as $row)
                @php
                    $pct = min($row['persentase'], 100);
                    $barColor = match(true) {
                        $row['persentase'] >= 100 => 'bg-green-500',
                        $row['persentase'] >= 85  => 'bg-teal-500',
                        $row['persentase'] >= 70  => 'bg-blue-500',
                        $row['persentase'] >= 50  => 'bg-yellow-400',
                        default                   => 'bg-red-500',
                    };
                    $statusConfig = match(true) {
                        $row['persentase'] >= 100 => ['bg-green-100',  'text-green-700',  'Sangat Baik'],
                        $row['persentase'] >= 85  => ['bg-teal-100',   'text-teal-700',   'Baik'],
                        $row['persentase'] >= 70  => ['bg-blue-100',   'text-blue-700',   'Cukup'],
                        $row['persentase'] >= 50  => ['bg-yellow-100', 'text-yellow-700', 'Kurang'],
                        default                   => ['bg-red-100',    'text-red-700',    'Sangat Kurang'],
                    };
                @endphp
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="p-3">
                        <span class="font-semibold text-gray-800">{{ $row['nama_kegiatan'] }}</span>
                    </td>
                    <td class="p-3">
                        <span class="text-gray-700">{{ $row['nama_detail'] }}</span><br>
                        <span class="text-xs text-gray-400">{{ $row['proses']->nama_proses ?? '-' }}
                            @if($row['proses']->satuan_target)
                                <span class="italic">({{ $row['proses']->satuan_target }})</span>
                            @endif
                        </span>
                    </td>
                    <td class="p-3 text-right font-medium text-gray-700">
                        {{ number_format($row['total_target']) }}
                    </td>
                    <td class="p-3 text-right font-semibold text-[#14B8A6]">
                        {{ number_format($row['total_realisasi']) }}
                    </td>
                    <td class="p-3">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 bg-gray-100 rounded-full h-2.5">
                                <div class="{{ $barColor }} h-2.5 rounded-full transition-all duration-500"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                            <span class="text-xs font-bold text-gray-700 w-12 text-right">
                                {{ $row['persentase'] }}%
                            </span>
                        </div>
                    </td>
                    <td class="p-3 text-center">
                        <div class="flex items-center justify-center gap-0.5">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= $row['rating'])
                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 text-gray-200" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endif
                            @endfor
                        </div>
                        <span class="text-xs text-gray-400 mt-0.5 block">{{ $row['rating'] }}/5</span>
                    </td>
                    <td class="p-3 text-center">
                        <span class="px-2 py-1 rounded-full text-xs font-bold {{ $statusConfig[0] }} {{ $statusConfig[1] }}">
                            {{ $statusConfig[2] }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="text-center py-12 text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-sm">Belum ada data proses kegiatan yang tersedia.</p>
        </div>
        @endif
    </div>

</div>
@endsection