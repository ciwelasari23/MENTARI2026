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
    
    <!-- CSS Kustom -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    <!-- CSS INTERNAL PEMUTUS CACHE: Memastikan warna biru berpindah dinamis secara mutlak -->
    <style>
        .custom-active {
            background-color: #005A9C !important;
            color: #ffffff !important;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            font-weight: 700;
        }
    </style>
</head>

<body class="dash-body" x-data="{ sidebarOpen: false }">

    <!-- Overlay Gelap untuk Mobile -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity 
         class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden" style="display: none;"></div>

    <!-- SIDEBAR KIRI -->
    <aside class="fixed md:static inset-y-0 left-0 z-50 w-[260px] bg-white border-r border-gray-200 flex flex-col flex-shrink-0 transition-transform duration-300 md:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        <!-- Header Sidebar & Logo -->
        <div class="sidebar-header-custom">
            <img src="{{ asset('images/mentari_samping.png') }}" alt="Ikon MENTARI" class="sidebar-icon-large">
            <button @click="sidebarOpen = false" class="md:hidden absolute right-4 text-gray-500 hover:text-gray-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Menu Navigasi dengan Kelas .custom-active -->
        <nav class="sidebar-nav p-4 space-y-1.5" x-data="{ openMaster: false, openKelola: false }">
            
            <!-- Dashboard Menu -->
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'custom-active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                Dashboard
            </a>

            <!-- Master Data -->
            <div>
                <button type="button" @click="openMaster = !openMaster" class="nav-item w-full flex justify-between items-center text-gray-500 hover:bg-gray-100 hover:text-gray-800">
                    <div class="flex items-center gap-3">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                        Master Data
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="openMaster ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="openMaster" x-transition class="pl-6 py-1 space-y-1 bg-gray-50/50 rounded-lg mt-1" style="display: none;">
                    <a href="{{ route('admin.wilayah.index') }}" class="nav-item text-xs py-1.5 {{ request()->routeIs('admin.wilayah.*') ? '!text-[#005A9C] font-extrabold underline bg-blue-50/80 rounded-md' : '' }}">• Wilayah</a>
                    <a href="{{ route('admin.timkerja.index') }}" class="nav-item text-xs py-1.5 {{ request()->routeIs('admin.timkerja.*') ? '!text-[#005A9C] font-extrabold underline bg-blue-50/80 rounded-md' : '' }}">• Tim Kerja</a>
                    <a href="{{ route('admin.user.index') }}" class="nav-item text-xs py-1.5 {{ request()->routeIs('admin.user.*') ? '!text-[#005A9C] font-extrabold underline bg-blue-50/80 rounded-md' : '' }}">• Pengguna</a>
                </div>
            </div>

            <!-- Kelola Kegiatan -->
            <div>
                <button type="button" @click="openKelola = !openKelola" class="nav-item w-full flex justify-between items-center text-gray-500 hover:bg-gray-100 hover:text-gray-800">
                    <div class="flex items-center gap-3">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Kelola Kegiatan
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="openKelola ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="openKelola" x-transition class="pl-6 py-1 space-y-1 bg-gray-50/50 rounded-lg mt-1" style="display: none;">
                    <a href="{{ route('admin.level1.index') }}" class="nav-item text-xs py-1.5 {{ request()->routeIs('admin.level1.*') ? '!text-[#005A9C] font-extrabold underline bg-blue-50/80 rounded-md' : '' }}">• Output</a>
                    <a href="{{ route('admin.level2.index') }}" class="nav-item text-xs py-1.5 {{ request()->routeIs('admin.level2.*') ? '!text-[#005A9C] font-extrabold underline bg-blue-50/80 rounded-md' : '' }}">• Kegiatan</a>
                    <a href="{{ route('admin.level3.index') }}" class="nav-item text-xs py-1.5 {{ request()->routeIs('admin.level3.*') ? '!text-[#005A9C] font-extrabold underline bg-blue-50/80 rounded-md' : '' }}">• Detail</a>
                    <a href="{{ route('admin.level4.index') }}" class="nav-item text-xs py-1.5 {{ request()->routeIs('admin.level4.*') ? '!text-[#005A9C] font-extrabold underline bg-blue-50/80 rounded-md' : '' }}">• Proses</a>
                </div>
            </div>
            
            <!-- Target Wilayah -->
            <a href="{{ route('admin.target.index') }}" class="nav-item {{ request()->routeIs('admin.target.*') ? 'custom-active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Target Wilayah
            </a>

            <!-- Form Pelaporan -->
            <a href="{{ route('pelaporan.index') }}" class="nav-item {{ request()->routeIs('pelaporan.*') ? 'custom-active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Form Pelaporan
            </a>

            <!-- Verifikasi Laporan -->
            <a href="{{ route('admin.verifikasi.index') }}" class="nav-item {{ request()->routeIs('admin.verifikasi.*') ? 'custom-active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Verifikasi Laporan
            </a>
        </nav>

        <!-- Profile Area -->
        <div class="sidebar-profile">
            @php
                $user = Auth::user();
                $name = $user->name ?? 'Administrator';
                $email = $user->email ?? 'admin@bps.go.id';
                $words = explode(' ', trim($name));
                $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
            @endphp
            <div class="profile-avatar">{{ $initials }}</div>
            <div class="profile-info">
                <span class="profile-name">{{ $name }}</span>
                <span class="profile-email">{{ $email }}</span>
            </div>
        </div>
    </aside>

    <!-- KONTEN UTAMA KANAN -->
    <main class="main-content">
        <header class="topbar">
            <div class="topbar-title-group">
                <button @click="sidebarOpen = true" class="md:hidden text-gray-700 hover:text-gray-900 focus:outline-none">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h1 class="topbar-title">@yield('title', 'Dashboard Monitoring Utama')</h1>
            </div>
        </header>

        <div class="scroll-area">
            @yield('content')
        </div>
    </main>
</body>
</html>