<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard - MENTARI')</title>
    
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ time() }}">
    <style>
        .sidebar-header-custom {
            padding: 1.5rem 1rem 1rem 1rem;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .sidebar-icon-large {
            width: 170px;
            height: auto;
            object-fit: contain;
        }
    </style>
</head>
<body class="dash-body">

    <!-- SIDEBAR KIRI -->
    <aside class="sidebar" style="height: 100vh; max-height: 100vh; display: flex; flex-direction: column; justify-content: space-between; overflow: hidden;">
        
        <!-- Bagian Atas: Logo & Navigasi (Bisa di-scroll jika submenu panjang) -->
        <div style="display: flex; flex-direction: column; flex: 1; min-height: 0; overflow: hidden;">
            
            <!-- Logo Sidebar (Diperbesar lebih optimal) -->
            <div class="sidebar-header-custom flex-shrink-0">
                <img src="{{ asset('images/mentari_samping.png') }}" alt="Ikon MENTARI" class="sidebar-icon-large">
            </div>

            <!-- Menu Navigasi (Area yang bisa di-scroll secara mandiri) -->
            <nav class="sidebar-nav" 
                 style="flex: 1; overflow-y: auto; overflow-x: hidden;"
                 x-data="{ 
                    openMaster: {{ request()->is('admin/master*') || request()->routeIs('admin.wilayah.*') || request()->routeIs('admin.timkerja.*') || request()->routeIs('admin.user.*') ? 'true' : 'false' }},
                    openKelola: {{ request()->is('admin/kegiatan*') || request()->routeIs('admin.level1.*') || request()->routeIs('admin.level2.*') || request()->routeIs('admin.level3.*') || request()->routeIs('admin.level4.*') ? 'true' : 'false' }} 
                 }">
                
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'nav-active' : '' }}">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Dashboard
                </a>

                <!-- 1. Master Data (Dropdown) -->
                <div>
                    <button type="button" @click="openMaster = !openMaster" class="nav-item w-full flex justify-between items-center {{ request()->routeIs('admin.wilayah.*') || request()->routeIs('admin.timkerja.*') || request()->routeIs('admin.user.*') ? 'bg-gray-50 text-[#005A9C] font-bold' : '' }}">
                        <div class="flex items-center gap-3">
                            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                            Master Data
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="openMaster ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="openMaster" x-transition class="pl-6 py-1 space-y-1 bg-gray-50/50 rounded-lg mt-1">
                        <a href="{{ route('admin.wilayah.index') }}" class="nav-item text-xs py-1.5 {{ request()->routeIs('admin.wilayah.*') ? 'nav-active text-white' : '' }}">
                            • Wilayah
                        </a>
                        <a href="{{ route('admin.timkerja.index') }}" class="nav-item text-xs py-1.5 {{ request()->routeIs('admin.timkerja.*') ? 'nav-active text-white' : '' }}">
                            • Tim Kerja
                        </a>
                        <a href="{{ route('admin.user.index') }}" class="nav-item text-xs py-1.5 {{ request()->routeIs('admin.user.*') ? 'nav-active text-white' : '' }}">
                            • Pengguna
                        </a>
                    </div>
                </div>

                <!-- 2. Kelola Kegiatan (Dropdown) -->
                <div>
                    <button type="button" @click="openKelola = !openKelola" class="nav-item w-full flex justify-between items-center {{ request()->routeIs('admin.level1.*') || request()->routeIs('admin.level2.*') || request()->routeIs('admin.level3.*') || request()->routeIs('admin.level4.*') ? 'bg-gray-50 text-[#005A9C] font-bold' : '' }}">
                        <div class="flex items-center gap-3">
                            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            Kelola Kegiatan
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="openKelola ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="openKelola" x-transition class="pl-6 py-1 space-y-1 bg-gray-50/50 rounded-lg mt-1">
                        <a href="{{ route('admin.level1.index') }}" class="nav-item text-xs py-1.5 {{ request()->routeIs('admin.level1.*') ? 'nav-active text-white' : '' }}">
                            • Kelola Kegiatan Output
                        </a>
                        <a href="{{ route('admin.level2.index') }}" class="nav-item text-xs py-1.5 {{ request()->routeIs('admin.level2.*') ? 'nav-active text-white' : '' }}">
                            • Kelola Kegiatan
                        </a>
                        <a href="{{ route('admin.level3.index') }}" class="nav-item text-xs py-1.5 {{ request()->routeIs('admin.level3.*') ? 'nav-active text-white' : '' }}">
                            • Kelola Kegiatan Detail
                        </a>
                        <a href="{{ route('admin.level4.index') }}" class="nav-item text-xs py-1.5 {{ request()->routeIs('admin.level4.*') ? 'nav-active text-white' : '' }}">
                            • Kelola Kegiatan Proses
                        </a>
                    </div>
                </div>

                <!-- 3. Target Wilayah -->
                <a href="{{ route('admin.target.index') }}" class="nav-item {{ request()->routeIs('admin.target.*') ? 'nav-active' : '' }}">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Target Wilayah
                </a>

                <!-- 4. Form Pelaporan -->
                <a href="{{ route('pelaporan.index') }}" class="nav-item {{ request()->routeIs('pelaporan.*') ? 'nav-active' : '' }}">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Form Pelaporan
                </a>

                <!-- 5. Verifikasi Laporan -->
                <a href="{{ route('admin.verifikasi.index') }}" class="nav-item {{ request()->routeIs('admin.verifikasi.*') ? 'nav-active' : '' }}">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Verifikasi Laporan
                </a>
            </nav>
        </div>

        <!-- Bagian Bawah: Profil Pengguna (Terkunci permanen di dasar paling bawah) -->
        <div class="sidebar-profile flex-shrink-0 border-t border-gray-100 bg-gray-50/50">
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
                <h1 class="topbar-title">@yield('title', 'Dashboard Monitoring Utama')</h1>
            </div>
        </header>

        <div class="scroll-area">
            @yield('content')
        </div>
    </main>
</body>
</html>