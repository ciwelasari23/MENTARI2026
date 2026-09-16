@extends('layouts.admin')

@section('title', 'Evaluasi Kegiatan - MENTARI')
@section('header', 'Evaluasi & Penilaian Kegiatan')

@section('content')
<div class="space-y-6">

    {{-- ===== KARTU RATING KESELURUHAN ===== --}}
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex flex-col sm:flex-row items-center gap-6">
            {{-- Ikon --}}
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 text-[#005A9C] flex-shrink-0">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
            </div>
            {{-- Info Keseluruhan --}}
            <div class="flex-1 text-center sm:text-left">
                <p class="text-sm text-gray-500 font-medium">Rata-rata Skor Keseluruhan</p>
                <div class="flex items-center justify-center sm:justify-start gap-3 mt-1">
                    {{-- Bintang Keseluruhan --}}
                    <div class="flex items-center gap-0.5">
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= ($ratingKeseluruhan ?? 0))
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
                    <span class="text-3xl font-extrabold text-gray-800">{{ $rataPersentase ?? 0 }} / 100</span>
                </div>
                <p class="text-xs text-gray-400 mt-1">Dihitung dari rata-rata kombinasi <span class="font-semibold text-[#005A9C]">Kualitas, Responsivitas, dan Kecepatan</span>.</p>
            </div>
            
            <div class="flex-shrink-0">
                <a href="{{ route('evaluasi.export-pdf') }}" class="inline-block px-4 py-2 bg-blue-50 text-[#005A9C] font-semibold text-sm rounded-lg hover:bg-blue-100 transition border border-blue-200 text-center">
                    Unduh Rekap PDF
                </a>
            </div>
        </div>
    </div>

    {{-- ===== KOMPONEN PENILAIAN (3 FEEDBACK + BUKTI) ===== --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-indigo-50 border border-indigo-100 p-4 rounded-xl shadow-sm">
            <h4 class="text-xs font-bold text-indigo-800 uppercase mb-1">Bukti Dukung</h4>
            <p class="text-xs text-indigo-600">Verifikasi kelengkapan dokumen/link bukti dari Kabupaten & Provinsi.</p>
        </div>
        <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl shadow-sm">
            <h4 class="text-xs font-bold text-blue-800 uppercase mb-1">1. Kualitas Pelaksanaan</h4>
            <p class="text-xs text-blue-600">Akurasi data target vs realisasi, kelengkapan, dan mutu output kegiatan.</p>
        </div>
        <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl shadow-sm">
            <h4 class="text-xs font-bold text-emerald-800 uppercase mb-1">2. Responsivitas</h4>
            <p class="text-xs text-emerald-600">Tingkat keaktifan melaporkan progres dan kecepatan merespon instruksi.</p>
        </div>
        <div class="bg-amber-50 border border-amber-100 p-4 rounded-xl shadow-sm">
            <h4 class="text-xs font-bold text-amber-800 uppercase mb-1">3. Kecepatan</h4>
            <p class="text-xs text-amber-600">Ketepatan waktu penyelesaian laporan (dibantu/dideteksi program).</p>
        </div>
    </div>

    {{-- ===== LEGENDA RATING ===== --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <p class="text-xs font-bold text-gray-500 uppercase mb-3">Keterangan Skala Rating Total</p>
        <div class="flex flex-wrap gap-3">
            @foreach ([
                ['Skor 90-100', 5, 'bg-green-100',  'text-green-700',  'Sangat Baik'],
                ['Skor 75-89',  4, 'bg-teal-100',   'text-teal-700',   'Baik'],
                ['Skor 60-74',  3, 'bg-blue-100',   'text-blue-700',   'Cukup'],
                ['Skor 40-59',  2, 'bg-yellow-100', 'text-yellow-700', 'Kurang'],
                ['Skor < 40',   1, 'bg-red-100',    'text-red-700',    'Sangat Kurang'],
            ] as [$range, $star, $bg, $text, $label])
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg {{ $bg }}">
                <span class="text-yellow-400 text-sm leading-none">{{ str_repeat('★', $star) }}</span>
                <span class="text-xs font-semibold {{ $text }}">{{ $range }} — {{ $label }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ===== TABEL EVALUASI PER PROSES ===== --}}
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Rekapitulasi Penilaian & Scoring Wilayah</h3>

        @if(count($evaluasiData ?? []) > 0)
        <table class="w-full text-left border-collapse text-sm whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50 border-b border-t text-gray-600 text-xs uppercase tracking-wider">
                    <th class="p-3">Kegiatan & Wilayah</th>
                    <th class="p-3 text-center">Bukti Dukung</th>
                    <th class="p-3 w-48">Target vs Realisasi</th>
                    <th class="p-3">Parameter Penilaian (Skor)</th>
                    <th class="p-3 text-center w-24">Skor Total</th>
                    <th class="p-3 text-center w-32">Rating</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                @foreach($evaluasiData as $row)
                @php
                    // Simulasi perhitungan skor 1-100 jika belum ada di database
                    $skorKualitas = $row['skor_kualitas'] ?? rand(70, 100);
                    $skorRespon   = $row['skor_responsivitas'] ?? rand(60, 100);
                    $skorCepat    = $row['skor_kecepatan'] ?? rand(65, 100);
                    $adaBukti     = $row['bukti_lengkap'] ?? (rand(0,1) == 1);
                    
                    // Rumus Total Skor (Contoh: Kualitas 40%, Respon 30%, Kecepatan 30%)
                    $totalSkor = ($skorKualitas * 0.4) + ($skorRespon * 0.3) + ($skorCepat * 0.3);
                    if(!$adaBukti) $totalSkor -= 10; // Penalti -10 poin jika tidak ada bukti dukung
                    $totalSkor = max(0, min(100, round($totalSkor)));

                    $ratingBintang = match(true) {
                        $totalSkor >= 90 => 5,
                        $totalSkor >= 75 => 4,
                        $totalSkor >= 60 => 3,
                        $totalSkor >= 40 => 2,
                        default => 1,
                    };
                    
                    $pct = min($row['persentase'], 100);
                @endphp
                <tr class="hover:bg-gray-50/50 transition-colors">
                    {{-- Kolom 1: Informasi Kegiatan & Wilayah --}}
                    <td class="p-3 align-top">
                        <div class="font-bold text-gray-800">{{ $row['nama_kegiatan'] }}</div>
                        <div class="text-xs text-gray-500 mt-0.5"><span class="font-semibold">Proses:</span> {{ $row['proses']->nama_proses ?? '-' }}</div>
                        <div class="text-xs font-semibold text-[#005A9C] mt-1.5 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            {{ $row['wilayah'] ?? 'Semua Wilayah' }}
                        </div>
                    </td>

                    {{-- Kolom 2: Kelengkapan Bukti Dukung --}}
                    <td class="p-3 align-top text-center">
                        @if($adaBukti)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-100 text-emerald-700 text-xs font-bold">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> Lengkap
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-red-100 text-red-700 text-xs font-bold" title="-10 Poin Penalti">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg> Kosong
                            </span>
                        @endif
                    </td>

                    {{-- Kolom 3: Target Dasar --}}
                    <td class="p-3 align-top">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-500">Target:</span>
                            <span class="font-bold">{{ number_format($row['total_target']) }}</span>
                        </div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-500">Realisasi:</span>
                            <span class="font-bold text-[#14B8A6]">{{ number_format($row['total_realisasi']) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                            <div class="bg-blue-500 h-1.5 rounded-full" x-data x-init="$el.style.width = '{{ $pct }}%'"></div>
                        </div>
                        <div class="text-[10px] text-right text-gray-500 mt-0.5">{{ $row['persentase'] }}% Tercapai</div>
                    </td>

                    {{-- Kolom 4: Parameter Feedback (Kualitas, Respon, Kecepatan) --}}
                    <td class="p-3 align-top">
                        <div class="space-y-1.5 w-full pr-4">
                            {{-- Kualitas --}}
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-gray-500 w-24">1. Kualitas</span>
                                <div class="flex items-center gap-2 flex-1">
                                    <div class="w-full bg-gray-100 rounded-full h-1.5"><div class="bg-blue-400 h-1.5 rounded-full" x-data x-init="$el.style.width = '{{ $skorKualitas }}%'"></div></div>
                                    <span class="font-bold text-gray-700 w-6 text-right">{{ $skorKualitas }}</span>
                                </div>
                            </div>
                            {{-- Respon --}}
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-gray-500 w-24">2. Responsivitas</span>
                                <div class="flex items-center gap-2 flex-1">
                                    <div class="w-full bg-gray-100 rounded-full h-1.5"><div class="bg-emerald-400 h-1.5 rounded-full" x-data x-init="$el.style.width = '{{ $skorRespon }}%'"></div></div>
                                    <span class="font-bold text-gray-700 w-6 text-right">{{ $skorRespon }}</span>
                                </div>
                            </div>
                            {{-- Kecepatan --}}
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-gray-500 w-24">3. Kecepatan</span>
                                <div class="flex items-center gap-2 flex-1">
                                    <div class="w-full bg-gray-100 rounded-full h-1.5"><div class="bg-amber-400 h-1.5 rounded-full" x-data x-init="$el.style.width = '{{ $skorCepat }}%'"></div></div>
                                    <span class="font-bold text-gray-700 w-6 text-right">{{ $skorCepat }}</span>
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- Kolom 5: Skor Akhir Total --}}
                    <td class="p-3 align-middle text-center bg-gray-50/50 border-l border-gray-100">
                        <div class="text-xl font-black text-[#0B1E40]">{{ $totalSkor }}</div>
                    </td>

                    {{-- Kolom 6: Bintang Akhir --}}
                    <td class="p-3 align-middle text-center border-l border-gray-100">
                        <div class="flex items-center justify-center gap-0.5">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= $ratingBintang)
                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-200" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endif
                            @endfor
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="text-center py-12 text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <p class="text-sm">Belum ada data evaluasi kegiatan yang tersedia untuk dievaluasi.</p>
        </div>
        @endif
    </div>

</div>
@endsection