<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MENTARI</title>
    
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- CSS Kustom Login -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}?v={{ time() }}">
</head>
<body class="login-body" x-data="{ role: 'pegawai', showPassword: false, showAlert: true }">

    <!-- Latar Belakang -->
    <div class="bg-wrapper">
        <img src="{{ asset('images/foto_kantor_bps.jpg') }}" alt="Kantor BPS Riau" class="bg-img">
        <div class="bg-overlay"></div>
    </div>

    <!-- Header Logo BPS -->
    <header class="header-logo">
        <img src="{{ asset('images/logo_bps.png') }}" alt="Logo BPS">
    </header>

    <!-- Area Konten -->
    <div class="content-area">
        
        <!-- Notifikasi Error -->
        @if($errors->any())
            <div x-show="showAlert" x-init="setTimeout(() => showAlert = false, 3000)" class="alert-box">
                <span>{{ $errors->first() }}</span>
                <button type="button" @click="showAlert = false" class="alert-close">×</button>
            </div>
        @endif

        <!-- Kotak Form Utama -->
        <main class="login-card">

            <!-- Logo Mentari -->
            <div class="card-logo-wrapper">
                <img src="{{ asset('images/mentari_icon.png') }}" alt="Ikon MENTARI" class="mentari-icon-img">
                <h1 class="mentari-title">MENTARI</h1>
                <p class="mentari-subtitle">MONITORING KEGIATAN TERINTEGRASI</p>
            </div>

            <!-- Tab Switcher -->
            <div class="tab-container">
                <button type="button" @click="role = 'pegawai'" :class="role === 'pegawai' ? 'tab-active-pegawai' : 'tab-inactive'" class="tab-btn">
                    SSO (Pegawai)
                </button>
                <button type="button" @click="role = 'mitra'" :class="role === 'mitra' ? 'tab-active-mitra' : 'tab-inactive'" class="tab-btn">
                    Login Manual (Mitra)
                </button>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <input type="hidden" name="kategori_user" x-model="role">
                
                <!-- Input Username/Email -->
                <div class="form-group">
                    <label class="form-label">Username / E-mail</label>
                    <div class="input-wrapper">
                        <span class="input-icon-left">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </span>
                        <input type="email" name="email" required class="form-input has-icon" placeholder="Masukkan Username atau E-mail">
                    </div>
                </div>

                <!-- Input Password -->
                <div class="form-group">
                    <label class="form-label">Kata Sandi</label>
                    <div class="input-wrapper">
                        <span class="input-icon-left">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </span>
                        <input :type="showPassword ? 'text' : 'password'" name="password" required class="form-input has-icon has-icon-right" value="secret">
                        <button type="button" @click="showPassword = !showPassword" class="input-icon-right">
                            <svg x-show="!showPassword" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                            <svg x-show="showPassword" style="display:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </button>
                    </div>
                </div>
                
                <!-- Tombol Submit (Warna Dinamis) -->
                <button type="submit" :class="role === 'pegawai' ? 'submit-pegawai' : 'submit-mitra'" class="submit-btn">
                    Masuk ke Sistem
                </button>
            </form>

            <!-- Login Google (Hanya Muncul saat Tab Mitra Dipilih) -->
            <div class="google-section" x-show="role === 'mitra'" x-transition style="display: none;">
                <div class="divider-container">
                    <span class="divider-line"></span>
                    <span class="divider-text">atau</span>
                </div>
                <a href="/auth/google" class="google-btn">
                    <svg class="google-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.56 12.25C22.56 11.47 22.49 10.72 22.36 10H12V14.26H17.92C17.66 15.63 16.88 16.79 15.71 17.57V20.34H19.28C21.36 18.42 22.56 15.6 22.56 12.25Z" fill="#4285F4"/>
                        <path d="M12 23C14.97 23 17.46 22.02 19.28 20.34L15.71 17.57C14.73 18.23 13.48 18.63 12 18.63C9.14 18.63 6.71 16.7 5.84 14.1H2.18V16.94C3.99 20.53 7.7 23 12 23Z" fill="#34A853"/>
                        <path d="M5.84 14.1C5.62 13.44 5.49 12.74 5.49 12C5.49 11.26 5.62 10.56 5.84 9.9V7.06H2.18C1.43 8.55 1 10.22 1 12C1 13.78 1.43 15.45 2.18 16.94L5.84 14.1Z" fill="#FBBC05"/>
                        <path d="M12 5.38C13.62 5.38 15.06 5.94 16.21 7.02L19.36 3.87C17.45 2.09 14.97 1 12 1C7.7 1 3.99 3.47 2.18 7.06L5.84 9.9C6.71 7.3 9.14 5.38 12 5.38Z" fill="#EA4335"/>
                    </svg>
                    Login dengan Google
                </a>
            </div>

        </main>
    </div>

    <!-- Footer -->
    <footer class="footer-area">
        <p class="footer-text-main">Hak Cipta © 2026 Badan Pusat Statistik Provinsi Riau.</p>
        <p class="footer-text-sub">Sistem Monitoring Kegiatan Terintegrasi (MENTARI)</p>
    </footer>

</body>
</html>