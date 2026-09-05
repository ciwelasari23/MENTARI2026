<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard - MENTARI')</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- CSS Kustom Terpusat -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ time() }}">
</head>

<body class="dash-body flex h-screen w-full overflow-hidden bg-[#F4F7F9] font-sans text-gray-800" x-data="{ sidebarOpen: false }" style="font-family: 'Inter', sans-serif;">

    <!-- Overlay Gelap untuk Mobile -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity 
         class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden" style="display: none;"></div>

    <!-- SIDEBAR KIRI -->
    <aside class="fixed md:static inset-y-0 left-0 z-50 w-[260px] bg-white border-r border-gray-200 flex flex-col flex-shrink-0 transition-transform duration-300 md:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        <!-- Header Sidebar & Logo -->
        <div class="h-20 w-full flex items-center justify-center border-b border-gray-100 flex-shrink-0 relative px-4">
            <img src="{{ asset('images/mentari_samping.png') }}" alt="Ikon MENTARI" class="w-full max-w-[180px] h-auto object-contain">
            <button @click="sidebarOpen = false" class="md:hidden absolute right-4 text-gray-500 hover:text-gray-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Menu Navigasi dengan Pengecekan URL Dinamis -->
        <nav class="sidebar-nav p-4 space-y-1.5 flex-1 overflow-y-auto overflow-x-hidden text-sm font-semibold" x-data="{ currentUrl: window.location.href }">
            
            <!-- Dashboard -->
            <a href="{{ url('/dashboard') }}" class="nav-item" :class="currentUrl.includes('/dashboard') ? 'custom-active' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800'">
                <svg class="nav-icon w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                Dashboard
            </a>

            <!-- Master Data -->
            <div x-data="{ openMaster: window.location.href.includes('/master') }">
                <button type="button" @click="openMaster = !openMaster" class="nav-item w-full flex justify-between items-center" :class="openMaster ? '!bg-blue-50 text-[#005A9C]' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800'">
                    <div class="flex items-center gap-3">
                        <svg class="nav-icon w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                        Master Data
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="openMaster ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="openMaster" x-transition class="pl-4 ml-6 my-1 space-y-1 border-l-2 border-blue-100" style="display: none;">
                    <a href="{{ route('admin.wilayah.index') }}" class="block px-3 py-2 rounded-md transition-colors text-sm font-medium" :class="currentUrl.includes('/wilayah') ? 'submenu-active' : 'text-gray-500 hover:text-[#005A9C] hover:bg-blue-50'">Wilayah</a>
                    <a href="{{ route('admin.timkerja.index') }}" class="block px-3 py-2 rounded-md transition-colors text-sm font-medium" :class="currentUrl.includes('/timkerja') ? 'submenu-active' : 'text-gray-500 hover:text-[#005A9C] hover:bg-blue-50'">Tim Kerja</a>
                    <a href="{{ route('admin.user.index') }}" class="block px-3 py-2 rounded-md transition-colors text-sm font-medium" :class="currentUrl.includes('/user') ? 'submenu-active' : 'text-gray-500 hover:text-[#005A9C] hover:bg-blue-50'">Pengguna</a>
                </div>
            </div>

            <!-- Kelola Kegiatan -->
            <div x-data="{ openKelola: window.location.href.includes('/kegiatan') }">
                <button type="button" @click="openKelola = !openKelola" class="nav-item w-full flex justify-between items-center" :class="openKelola ? '!bg-blue-50 text-[#005A9C]' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800'">
                    <div class="flex items-center gap-3">
                        <svg class="nav-icon w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Kelola Kegiatan
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="openKelola ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="openKelola" x-transition class="pl-4 ml-6 my-1 space-y-1 border-l-2 border-blue-100" style="display: none;">
                    <a href="{{ route('admin.level1.index') }}" class="block px-3 py-2 rounded-md transition-colors text-sm font-medium" :class="currentUrl.includes('/level1') ? 'submenu-active' : 'text-gray-500 hover:text-[#005A9C] hover:bg-blue-50'">Output</a>
                    <a href="{{ route('admin.level2.index') }}" class="block px-3 py-2 rounded-md transition-colors text-sm font-medium" :class="currentUrl.includes('/level2') ? 'submenu-active' : 'text-gray-500 hover:text-[#005A9C] hover:bg-blue-50'">Kegiatan</a>
                    <a href="{{ route('admin.level3.index') }}" class="block px-3 py-2 rounded-md transition-colors text-sm font-medium" :class="currentUrl.includes('/level3') ? 'submenu-active' : 'text-gray-500 hover:text-[#005A9C] hover:bg-blue-50'">Detail</a>
                    <a href="{{ route('admin.level4.index') }}" class="block px-3 py-2 rounded-md transition-colors text-sm font-medium" :class="currentUrl.includes('/level4') ? 'submenu-active' : 'text-gray-500 hover:text-[#005A9C] hover:bg-blue-50'">Proses</a>
                </div>
            </div>
            
            <!-- Target Wilayah -->
            <a href="{{ route('admin.target.index') }}" class="nav-item" :class="currentUrl.includes('/target') ? 'custom-active' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800'">
                <svg class="nav-icon w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Target Wilayah
            </a>

            <!-- Form Pelaporan -->
            <a href="{{ route('pelaporan.index') }}" class="nav-item" :class="currentUrl.includes('/pelaporan') ? 'custom-active' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800'">
                <svg class="nav-icon w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Form Pelaporan
            </a>

            <!-- Verifikasi Laporan -->
            <a href="{{ route('admin.verifikasi.index') }}" class="nav-item" :class="currentUrl.includes('/verifikasi') ? 'custom-active' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800'">
                <svg class="nav-icon w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Verifikasi Laporan
            </a>
        </nav>

        <!-- Profile Area -->
        <div class="sidebar-profile p-4 border-t border-gray-100 bg-gray-50 flex items-center gap-3 flex-shrink-0">
            @php
                $user = Auth::user();
                $name = $user->name ?? 'Administrator';
                $email = $user->email ?? 'admin@bps.go.id';
                $words = explode(' ', trim($name));
                $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
            @endphp
            <div class="profile-avatar w-10 h-10 rounded-full bg-orange-500 text-white font-bold flex items-center justify-center shadow flex-shrink-0">{{ $initials }}</div>
            <div class="profile-info flex flex-col overflow-hidden w-24">
                <span class="profile-name text-sm font-bold text-gray-800 truncate">{{ $name }}</span>
                <span class="profile-email text-xs text-gray-500 truncate">{{ $email }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="ml-auto m-0">
                @csrf
                <button type="submit" class="text-xs text-red-600 font-bold hover:text-red-800 transition-colors">Keluar</button>
            </form>
        </div>
    </aside>

    <!-- KONTEN UTAMA KANAN -->
    <main class="main-content flex-1 flex flex-col h-full overflow-hidden w-full relative">
        
        <!-- Topbar Putih -->
        <header class="topbar h-20 bg-white border-b border-gray-100 flex items-center justify-between px-6 flex-shrink-0 z-10">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="md:hidden text-gray-700 hover:text-gray-900 focus:outline-none">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h1 class="text-xl font-bold text-[#0B1E40]">@yield('header', 'Dashboard Monitoring Utama')</h1>
            </div>
        </header>

        <!-- Area Konten -->
        <div class="scroll-area flex-1 overflow-y-auto px-6 pt-6 pb-10">
            @yield('content')
        </div>
    </main>
</body>
</html>