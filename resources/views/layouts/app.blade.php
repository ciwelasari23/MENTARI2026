<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - MENTARI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js untuk interaktivitas Sidebar Dropdown -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- CSS Kustom Aplikasi MENTARI -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>
<body class="bg-gray-100 font-sans flex h-screen overflow-hidden">

    <!-- Sidebar Menu -->
    <aside class="w-64 bg-[#0b1d3a] text-white flex flex-col shadow-lg z-20 flex-shrink-0">
        <div class="h-16 flex items-center justify-center border-b border-gray-700 bg-white px-4">
            <!-- Logo MENTARI -->
            <img src="{{ asset('images/mentari_logo_horizontal final.png') }}" alt="Logo MENTARI" class="h-10 object-contain">
        </div>
        
        <!-- Navigasi dengan Alpine.js untuk Dropdown Master Data & Kelola Kegiatan -->
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto text-sm" x-data="{ openMaster: false, openKegiatan: false }">
            <a href="{{ route('dashboard') }}" class="block px-4 py-2 rounded {{ request()->routeIs('dashboard') ? 'bg-[#14B8A6] text-white font-semibold' : 'hover:bg-gray-700 transition-colors' }}">
                Dashboard
            </a>
            
            <!-- Menu Dropdown Master Data -->
            <div>
                <button @click="openMaster = !openMaster" class="w-full flex justify-between items-center px-4 py-2 rounded hover:bg-gray-700 transition-colors text-left font-medium">
                    <span>Master Data</span>
                    <svg class="w-4 h-4 transform transition-transform" :class="openMaster ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                
                <!-- Sub-menu Master Data -->
                <div x-show="openMaster" class="pl-4 space-y-1 mt-1 border-l border-gray-700 ml-4" style="display: none;">
                    <a href="{{ route('admin.wilayah.index') }}" class="block px-3 py-1.5 text-xs rounded {{ request()->routeIs('admin.wilayah.*') ? 'bg-[#14B8A6] text-white font-bold' : 'hover:bg-gray-700 text-gray-300' }}">Wilayah</a>
                    <a href="{{ route('admin.timkerja.index') }}" class="block px-3 py-1.5 text-xs rounded {{ request()->routeIs('admin.timkerja.*') ? 'bg-[#14B8A6] text-white font-bold' : 'hover:bg-gray-700 text-gray-300' }}">Tim Kerja</a>
                    <a href="{{ route('admin.user.index') }}" class="block px-3 py-1.5 text-xs rounded {{ request()->routeIs('admin.user.*') ? 'bg-[#14B8A6] text-white font-bold' : 'hover:bg-gray-700 text-gray-300' }}">Pengguna</a>
                </div>
            </div>
            
            <!-- Menu Dropdown Kelola Kegiatan -->
            <div>
                <button @click="openKegiatan = !openKegiatan" class="w-full flex justify-between items-center px-4 py-2 rounded hover:bg-gray-700 transition-colors text-left font-medium">
                    <span>Kelola Kegiatan</span>
                    <svg class="w-4 h-4 transform transition-transform" :class="openKegiatan ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                
                <!-- Sub-menu Level 1 sampai Level 4 -->
                <div x-show="openKegiatan" class="pl-4 space-y-1 mt-1 border-l border-gray-700 ml-4" style="display: none;">
                    <a href="{{ route('admin.level1.index') }}" class="block px-3 py-1.5 text-xs rounded {{ request()->routeIs('admin.level1.*') ? 'bg-[#14B8A6] text-white font-bold' : 'hover:bg-gray-700 text-gray-300' }}">
                        Level 1: Output
                    </a>
                    <a href="{{ route('admin.level2.index') }}" class="block px-3 py-1.5 text-xs rounded {{ request()->routeIs('admin.level2.*') ? 'bg-[#14B8A6] text-white font-bold' : 'hover:bg-gray-700 text-gray-300' }}">
                        Level 2: Kegiatan
                    </a>
                    <a href="{{ route('admin.level3.index') }}" class="block px-3 py-1.5 text-xs rounded {{ request()->routeIs('admin.level3.*') ? 'bg-[#14B8A6] text-white font-bold' : 'hover:bg-gray-700 text-gray-300' }}">
                        Level 3: Detail Kegiatan
                    </a>
                    <a href="{{ route('admin.level4.index') }}" class="block px-3 py-1.5 text-xs rounded {{ request()->routeIs('admin.level4.*') ? 'bg-[#14B8A6] text-white font-bold' : 'hover:bg-gray-700 text-gray-300' }}">
                        Level 4: Proses Kegiatan
                    </a>
                </div>
            </div>

            <a href="{{ route('admin.target.index') }}" class="block px-4 py-2 rounded {{ request()->routeIs('admin.target.*') ? 'bg-[#14B8A6] text-white font-semibold' : 'hover:bg-gray-700 transition-colors' }}">
                Target Wilayah
            </a>
            <a href="{{ route('pelaporan.index') }}" class="block px-4 py-2 rounded {{ request()->routeIs('pelaporan.*') ? 'bg-[#14B8A6] text-white font-semibold' : 'hover:bg-gray-700 transition-colors' }}">
                Form Pelaporan
            </a>
            <a href="{{ route('admin.verifikasi.index') }}" class="block px-4 py-2 rounded {{ request()->routeIs('admin.verifikasi.*') ? 'bg-[#14B8A6] text-white font-semibold' : 'hover:bg-gray-700 transition-colors' }}">
                Verifikasi Laporan
            </a>
        </nav>
    </aside>

    <!-- Area Konten Utama -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        
        <!-- Navbar -->
        <header class="h-16 bg-white shadow-sm border-b border-gray-200 flex items-center justify-between px-8 z-10 flex-shrink-0">
            <h2 class="text-lg font-bold text-gray-800">@yield('header')</h2>
            <div class="flex items-center gap-4">
                <span class="text-sm font-medium text-gray-600">Halo, {{ auth()->user()->nama_lengkap ?? 'Admin' }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-red-500 font-bold hover:underline">Keluar</button>
                </form>
            </div>
        </header>

        <!-- Area Dinamis untuk Halaman Lain -->
        <main class="flex-1 overflow-y-auto p-8 bg-gray-100">
            @yield('content')
        </main>
    </div>

</body>
</html>