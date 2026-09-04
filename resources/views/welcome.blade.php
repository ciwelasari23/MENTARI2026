<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MENTARI - Monitoring Kegiatan Terintegrasi</title>
    @fonts
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <!-- CSS Kustom untuk Hilangkan Scrollbar & Kunci Layar -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>
<body class="bg-white text-gray-800 h-screen flex flex-col justify-between font-sans antialiased overflow-hidden">

    <!-- Header Logo BPS Provinsi Riau -->
    <header class="w-full px-8 py-3 border-b border-gray-100 flex-shrink-0">
        <div class="max-w-7xl mx-auto flex items-center">
            <img src="{{ asset('images/logo_bps.png') }}" alt="Logo BPS" class="h-10 w-auto object-contain">
        </div>
    </header>

    <!-- Main Content Section (Menghapus items-center agar stretch ke atas/bawah) -->
    <main class="w-full max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-8 flex-1 min-h-0">
        
        <!-- Left Side: Container Background Menempel ke Kiri & Atas Header -->
        <div class="relative flex flex-col items-center justify-center w-full h-full">
            <!-- Background melengkung ditarik dari top-0 hingga menempel persis ke border navbar -->
            <div class="absolute right-0 top-0 bottom-0 w-[200vw] bg-[#EFF4F9] rounded-r-[3rem] lg:rounded-r-[14rem] -z-10"></div>
            
            <!-- Gambar Logo Mentari -->
            <div class="relative z-10 flex flex-col items-center text-center w-full px-4 lg:pr-10">
                <img src="{{ asset('images/mentari_logo_horizontal final.png') }}" alt="Logo MENTARI" class="w-full max-w-[380px] h-auto object-contain mix-blend-multiply">
            </div>
        </div>

        <!-- Right Side: Content & Login Button (Diberi justify-center agar teks tetap di tengah) -->
        <div class="flex flex-col justify-center space-y-4 lg:pl-4 h-full py-8">
            <div>
                <span class="text-xs font-medium text-gray-500">Selamat Datang di</span>
                <h2 class="text-4xl font-extrabold text-[#0B1E40] tracking-tight mt-0.5">MENTARI</h2>
                <h3 class="text-sm font-bold text-blue-600 tracking-wider mt-0.5">MONITORING KEGIATAN TERINTEGRASI</h3>
            </div>

            <p class="text-xs text-gray-500 leading-relaxed max-w-lg">
                MENTARI adalah platform terintegrasi untuk memantau, mengelola, dan mengevaluasi kegiatan perencanaan di lingkungan Badan Pusat Statistik secara efektif, transparan, dan akuntabel.
            </p>

            <p class="text-xs text-gray-500 leading-relaxed max-w-lg">
                Platform ini mendukung pengambilan keputusan berbasis data dan mendorong kolaborasi untuk perencanaan yang lebih berkualitas demi terwujudnya statistik berkualitas untuk Indonesia maju.
            </p>

            <div class="pt-2">
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 bg-[#254EDb] hover:bg-blue-700 text-white font-bold px-6 py-2.5 rounded-md shadow-sm text-sm transition duration-200">
                        Login ke MENTARI 
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                @endif
            </div>

            <div class="flex items-center gap-2 text-[10px] text-gray-400 pt-1">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                <span>Akses hanya untuk pegawai BPS dan mitra kerja yang terdaftar</span>
            </div>
        </div>
    </main>

    <!-- Footer Features Bar -->
    <footer class="w-full bg-[#EBF4FA] border-t border-blue-100 py-5 px-8 flex-shrink-0">
        <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- Feature 1 -->
            <div class="flex items-center gap-4">
                <div class="bg-white p-3 rounded-full shadow-sm text-[#254EDb] flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-[#0B1E40]">Terpercaya</h4>
                    <p class="text-[10px] text-gray-500 mt-0.5 leading-tight">Keamanan informasi dengan jaminan<br>kualitas statistik handal.</p>
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="flex items-center gap-4">
                <div class="bg-white p-3 rounded-full shadow-sm text-[#254EDb] flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-[#0B1E40]">Berbasis Data</h4>
                    <p class="text-[10px] text-gray-500 mt-0.5 leading-tight">Informasi akurat untuk perencanaan<br>yang jauh lebih baik.</p>
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="flex items-center gap-4">
                <div class="bg-white p-3 rounded-full shadow-sm text-[#254EDb] flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-[#0B1E40]">Kolaboratif</h4>
                    <p class="text-[10px] text-gray-500 mt-0.5 leading-tight">Mendorong sinergi dan kolaborasi antar<br>unit kerja.</p>
                </div>
            </div>

            <!-- Feature 4 -->
            <div class="flex items-center gap-4">
                <div class="bg-white p-3 rounded-full shadow-sm text-[#254EDb] flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-[#0B1E40]">Efektif & Efisien</h4>
                    <p class="text-[10px] text-gray-500 mt-0.5 leading-tight">Optimalisasi proses bisnis berbasis<br>teknologi modern.</p>
                </div>
            </div>

        </div>
    </footer>

</body>
</html>