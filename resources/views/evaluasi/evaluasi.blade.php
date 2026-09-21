@extends('layouts.admin')

@section('title', 'Evaluasi Kegiatan - MENTARI')
@section('header', 'Evaluasi & Penilaian Kegiatan')

@section('content')
<div class="space-y-6">

    {{-- ===== KARTU RATING KESELURUHAN ===== --}}
    <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
        <!-- Decorative element -->
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-gradient-to-br from-blue-50 to-blue-100 rounded-full opacity-50 blur-2xl"></div>
        
        <div class="flex flex-col sm:flex-row items-center gap-6 sm:gap-8 relative z-10">
            {{-- Ikon --}}
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100 text-blue-600 shadow-inner flex-shrink-0 border border-blue-100/50">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
            </div>
            {{-- Info Keseluruhan --}}
            <div class="flex-1 text-center sm:text-left">
                <h2 class="text-sm uppercase tracking-wider text-gray-500 font-semibold mb-2">Rata-rata Skor Keseluruhan</h2>
                <div class="flex flex-col sm:flex-row sm:items-end gap-3 sm:gap-6 mt-1 justify-center sm:justify-start">
                    <span class="text-5xl font-black text-gray-800 tracking-tight">{{ $rataPersentase ?? 0 }}<span class="text-2xl text-gray-400 font-medium">/100</span></span>
                    
                    {{-- Bintang Keseluruhan --}}
                    <div class="flex items-center gap-1 mb-1.5">
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= ($ratingKeseluruhan ?? 0))
                                <svg class="w-8 h-8 text-amber-400 drop-shadow-sm" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @else
                                <svg class="w-8 h-8 text-gray-200" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endif
                        @endfor
                    </div>
                </div>
                <p class="text-sm text-gray-500 mt-2">Berdasarkan evaluasi terhadap <span class="font-semibold text-blue-600">Kualitas, Responsivitas, dan Kecepatan</span>.</p>
            </div>
            
            <div class="flex-shrink-0 mt-4 sm:mt-0">
                <a href="{{ route('evaluasi.export-pdf') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-red-600 text-white font-semibold text-sm rounded-xl hover:bg-red-700 transition shadow-sm hover:shadow group w-full sm:w-auto">
                    <svg class="w-5 h-5 text-red-200 group-hover:text-white transition-colors" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path d="M8.267 14.68c-.184 0-.308.018-.372.036v1.178c.076.018.171.023.302.023.479 0 .774-.242.774-.651 0-.366-.254-.586-.704-.586zm3.487.012c-.2 0-.33.018-.407.036v2.61c.077.018.201.018.313.018.817.006 1.349-.444 1.349-1.396.006-.83-.479-1.268-1.255-1.268z"/>
                      <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM9.498 16.19c-.309.29-.765.42-1.296.42a2.23 2.23 0 0 1-.308-.018v1.426H7v-3.936A7.558 7.558 0 0 1 8.219 14c.557 0 .953.106 1.22.319.254.202.426.533.426.923-.001.392-.131.723-.367.948zm3.807 1.355c-.42.349-1.059.515-1.84.515-.468 0-.799-.03-1.024-.059v-3.917c.372-.066.896-.107 1.457-.107.877 0 1.481.207 1.905.615.42.408.641 1.012.641 1.704 0 .521-.148 1.018-.468 1.255h-.001-.005a1.272 1.272 0 0 1-.006-.005zm3.695-.911H16.11v1.36h-.893v-3.93h1.864c.734 0 1.207.124 1.455.337.243.207.385.503.385.876 0 .42-.154.71-.408.894-.172.124-.432.225-.781.254v.018c.456.047.669.231.811.592.172.444.302.822.408 1.03l.006.017h-.964c-.118-.16-.254-.538-.414-1.006-.13-.396-.343-.443-.722-.443zm1.196-1.55c-.154-.124-.408-.183-.787-.183h-.296v.976h.314c.325 0 .538-.053.663-.16.124-.112.189-.272.189-.438 0-.177-.077-.319-.248-.372h.001-.005a.49.49 0 0 0-.007-.005.418.418 0 0 0-.003-.001zM13 9V3.5L18.5 9H13z"/>
                    </svg>
                    Unduh Rekap PDF
                </a>
            </div>
        </div>
    </div>

    {{-- ===== KOMPONEN PENILAIAN (3 FEEDBACK + BUKTI) ===== --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
        <div class="bg-white border border-gray-100 p-5 rounded-2xl shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500"></div>
            <div class="flex items-start gap-4">
                <div class="p-2.5 bg-indigo-50 rounded-xl text-indigo-600 group-hover:bg-indigo-500 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-800 mb-1">Bukti Dukung</h4>
                    <p class="text-xs text-gray-500 leading-relaxed">Verifikasi kelengkapan dokumen atau tautan dari daerah.</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-100 p-5 rounded-2xl shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
            <div class="flex items-start gap-4">
                <div class="p-2.5 bg-blue-50 rounded-xl text-blue-600 group-hover:bg-blue-500 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-800 mb-1">Kualitas</h4>
                    <p class="text-xs text-gray-500 leading-relaxed">Akurasi data target vs realisasi, kelengkapan, & mutu.</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-100 p-5 rounded-2xl shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-1 h-full bg-emerald-500"></div>
            <div class="flex items-start gap-4">
                <div class="p-2.5 bg-emerald-50 rounded-xl text-emerald-600 group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-800 mb-1">Responsivitas</h4>
                    <p class="text-xs text-gray-500 leading-relaxed">Keaktifan lapor progres & kecepatan merespon.</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-100 p-5 rounded-2xl shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-1 h-full bg-amber-500"></div>
            <div class="flex items-start gap-4">
                <div class="p-2.5 bg-amber-50 rounded-xl text-amber-600 group-hover:bg-amber-500 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-800 mb-1">Kecepatan</h4>
                    <p class="text-xs text-gray-500 leading-relaxed">Ketepatan waktu penyelesaian laporan kegiatan.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== LEGENDA RATING ===== --}}
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <h3 class="text-sm font-bold text-gray-700 whitespace-nowrap">Skala Rating :</h3>
            <div class="flex flex-wrap gap-2.5">
                @foreach ([
                    ['90-100', 5, 'bg-green-50 border-green-200 text-green-700', 'Sangat Baik'],
                    ['75-89',  4, 'bg-teal-50 border-teal-200 text-teal-700', 'Baik'],
                    ['60-74',  3, 'bg-blue-50 border-blue-200 text-blue-700', 'Cukup'],
                    ['40-59',  2, 'bg-yellow-50 border-yellow-200 text-yellow-700', 'Kurang'],
                    ['< 40',   1, 'bg-red-50 border-red-200 text-red-700', 'Sangat Kurang'],
                ] as [$range, $star, $classes, $label])
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-full border {{ $classes }}">
                    <div class="flex items-center">
                        <span class="text-amber-400 text-sm leading-none drop-shadow-sm">{{ str_repeat('★', $star) }}</span>
                    </div>
                    <span class="text-xs font-semibold">{{ $range }} — {{ $label }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ===== TABEL EVALUASI PER PROSES ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-white">
            <h3 class="text-lg font-bold text-gray-800">Rekapitulasi Penilaian & Scoring Wilayah</h3>
            <p class="text-sm text-gray-500 mt-1">Daftar evaluasi rinci untuk setiap kegiatan berdasarkan wilayah.</p>
        </div>

        @if(count($evaluasiData ?? []) > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50/80 border-b text-gray-500 text-xs uppercase tracking-wider font-semibold">
                        <th class="px-6 py-4">Kegiatan & Wilayah</th>
                        <th class="px-4 py-4 text-center">Bukti Dukung</th>
                        <th class="px-4 py-4 w-56">Target vs Realisasi</th>
                        <th class="px-4 py-4">Parameter Penilaian</th>
                        <th class="px-4 py-4 text-center w-28">Skor Total</th>
                        <th class="px-6 py-4 text-center w-36">Rating</th>
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
                        
                        $colorClass = match(true) {
                            $pct >= 90 => 'bg-emerald-500',
                            $pct >= 70 => 'bg-blue-500',
                            $pct >= 50 => 'bg-yellow-400',
                            default => 'bg-red-500',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors group">
                        {{-- Kolom 1: Informasi Kegiatan & Wilayah --}}
                        <td class="px-6 py-5 align-top">
                            <div class="font-bold text-gray-800 text-base mb-1">{{ $row['nama_kegiatan'] }}</div>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                    {{ $row['proses']->nama_proses ?? 'Proses' }}
                                </span>
                            </div>
                            <div class="text-xs font-medium text-blue-600 flex items-center gap-1.5 bg-blue-50/50 inline-flex px-2 py-1 rounded-md border border-blue-100">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                {{ $row['wilayah'] ?? 'Semua Wilayah' }}
                            </div>
                        </td>

                        {{-- Kolom 2: Kelengkapan Bukti Dukung --}}
                        <td class="px-4 py-5 align-top text-center">
                            @if($adaBukti)
                                <div class="inline-flex flex-col items-center justify-center p-2 bg-emerald-50 border border-emerald-100 rounded-xl w-20">
                                    <svg class="w-5 h-5 text-emerald-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="text-[9px] font-bold text-emerald-700 uppercase tracking-wider">Lengkap</span>
                                </div>
                            @else
                                <div class="inline-flex flex-col items-center justify-center p-2 bg-red-50 border border-red-100 rounded-xl w-20" title="-10 Poin Penalti">
                                    <svg class="w-5 h-5 text-red-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="text-[9px] font-bold text-red-700 uppercase tracking-wider">Kosong</span>
                                </div>
                            @endif
                        </td>

                        {{-- Kolom 3: Target Dasar --}}
                        <td class="px-4 py-5 align-top">
                            <div class="flex justify-between items-end mb-2">
                                <div class="text-xs text-gray-500">
                                    <div>Target: <span class="font-semibold text-gray-700">{{ number_format($row['total_target']) }}</span></div>
                                    <div>Realisasi: <span class="font-semibold text-gray-800">{{ number_format($row['total_realisasi']) }}</span></div>
                                </div>
                                <div class="text-sm font-bold text-gray-800">{{ $row['persentase'] }}%</div>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2 border border-gray-100 overflow-hidden">
                                <div class="h-2 rounded-full {{ $colorClass }}" x-data x-init="$el.style.width = '{{ $pct }}%'"></div>
                            </div>
                        </td>

                        {{-- Kolom 4: Parameter Feedback (Kualitas, Respon, Kecepatan) --}}
                        <td class="px-4 py-5 align-top">
                            <div class="space-y-2.5 w-full pr-4">
                                {{-- Kualitas --}}
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-medium text-gray-600 w-24">Kualitas</span>
                                    <div class="flex-1 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $skorKualitas }}%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-gray-800 w-6 text-right">{{ $skorKualitas }}</span>
                                </div>
                                {{-- Respon --}}
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-medium text-gray-600 w-24">Responsivitas</span>
                                    <div class="flex-1 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $skorRespon }}%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-gray-800 w-6 text-right">{{ $skorRespon }}</span>
                                </div>
                                {{-- Kecepatan --}}
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-medium text-gray-600 w-24">Kecepatan</span>
                                    <div class="flex-1 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ $skorCepat }}%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-gray-800 w-6 text-right">{{ $skorCepat }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- Kolom 5: Skor Akhir Total --}}
                        <td class="px-4 py-5 align-middle text-center bg-gray-50/30 group-hover:bg-blue-50/30 transition-colors border-l border-gray-100">
                            <div class="inline-flex items-center justify-center w-14 h-14 rounded-full border-4 {{ $totalSkor >= 80 ? 'border-emerald-100 text-emerald-700' : ($totalSkor >= 60 ? 'border-blue-100 text-blue-700' : 'border-red-100 text-red-700') }} bg-white shadow-sm">
                                <span class="text-lg font-black">{{ $totalSkor }}</span>
                            </div>
                        </td>

                        {{-- Kolom 6: Bintang Akhir --}}
                        <td class="px-6 py-5 align-middle text-center border-l border-gray-100">
                            <div class="flex items-center justify-center gap-0.5">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $ratingBintang)
                                        <svg class="w-5 h-5 text-amber-400 drop-shadow-sm" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @else
                                        <svg class="w-5 h-5 text-gray-200" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endif
                                @endfor
                            </div>
                            <div class="mt-2 text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                                @if($ratingBintang == 5) Sangat Baik
                                @elseif($ratingBintang == 4) Baik
                                @elseif($ratingBintang == 3) Cukup
                                @elseif($ratingBintang == 2) Kurang
                                @else Sangat Kurang
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-16 text-gray-400 bg-gray-50/50">
            <div class="bg-white w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm border border-gray-100">
                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <h4 class="text-gray-600 font-semibold mb-1">Tidak Ada Data</h4>
            <p class="text-sm">Belum ada data evaluasi kegiatan yang tersedia saat ini.</p>
        </div>
        @endif
    </div>

</div>
@endsection