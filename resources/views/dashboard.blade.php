@extends('layouts.admin')

@section('title', 'Dashboard Monitoring Utama')

@section('content')
    <!-- Grid 4 Kartu Statistik -->
    <div class="stats-grid">
        
        <!-- Kartu 1 -->
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-title">Total Kegiatan</span>
                <div class="stat-icon-wrapper stat-icon-blue"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg></div>
            </div>
            <div class="stat-value">48</div>
            <div class="stat-desc">
                <span class="text-success">↑ 4 Baru</span> Tahun Anggaran 2026
            </div>
        </div>

        <!-- Kartu 2 -->
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-title">Target Wilayah</span>
                <div class="stat-icon-wrapper stat-icon-indigo"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg></div>
            </div>
            <div class="stat-value">12</div>
            <div class="stat-desc">
                <span class="text-success">100% Aktif</span> Kabupaten & Kota Riau
            </div>
        </div>

        <!-- Kartu 3 -->
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-title">Laporan Masuk</span>
                <div class="stat-icon-wrapper stat-icon-blue"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg></div>
            </div>
            <div class="stat-value">156</div>
            <div class="stat-desc">
                <span class="text-success">↑ 28% Beban</span> Dalam 30 hari terakhir
            </div>
        </div>

        <!-- Kartu 4 -->
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-title">Tingkat Capaian</span>
                <div class="stat-icon-wrapper stat-icon-blue"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg></div>
            </div>
            <div class="stat-value">78.5%</div>
            <div class="stat-desc">
                <span class="text-success">↑ 2.4% Progres</span> Rata-rata kumulatif
            </div>
        </div>
    </div>

    <!-- Grid 2 Grafik Tengah -->
    <div class="charts-grid">
        
        <!-- Bar Chart Simulasi -->
        <div class="chart-card chart-col-span-2">
            <div class="chart-header">
                <h3 class="chart-title">Capaian per Wilayah (%)</h3>
                <a href="#" class="chart-link">Lihat Detail</a>
            </div>
            
            <div class="bar-chart-container">
                <div class="bar-item">
                    <span class="bar-value">92%</span>
                    <div class="bar-fill" style="height: 92%;"></div>
                    <span class="bar-label">Pekanbaru</span>
                </div>
                <div class="bar-item">
                    <span class="bar-value">78%</span>
                    <div class="bar-fill" style="height: 78%;"></div>
                    <span class="bar-label">Kampar</span>
                </div>
                <div class="bar-item">
                    <span class="bar-value">85%</span>
                    <div class="bar-fill" style="height: 85%;"></div>
                    <span class="bar-label">Bengkalis</span>
                </div>
                <div class="bar-item">
                    <span class="bar-value">74%</span>
                    <div class="bar-fill" style="height: 74%;"></div>
                    <span class="bar-label">Siak</span>
                </div>
                <div class="bar-item">
                    <span class="bar-value">68%</span>
                    <div class="bar-fill" style="height: 68%;"></div>
                    <span class="bar-label">Inhil</span>
                </div>
                <div class="bar-item">
                    <span class="bar-value">71%</span>
                    <div class="bar-fill" style="height: 71%;"></div>
                    <span class="bar-label">Rohul</span>
                </div>
            </div>
        </div>

        <!-- Donut Chart Simulasi -->
        <div class="chart-card relative">
            <div class="chart-header">
                <h3 class="chart-title">Status Laporan Masuk</h3>
                <button class="icon-btn">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </button>
            </div>
            
            <div class="donut-container">
                <div class="donut-chart-outer">
                    <div class="donut-chart-inner">
                        <span class="donut-value">156</span>
                        <span class="donut-label">Laporan</span>
                    </div>
                </div>

                <div class="legend-container">
                    <div class="legend-item">
                        <span class="legend-color bg-green"></span>
                        <span class="legend-title">Disetujui</span>
                        <span class="legend-count">92 Laporan</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color bg-blue"></span>
                        <span class="legend-title">Diajukan</span>
                        <span class="legend-count">48 Laporan</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color bg-yellow"></span>
                        <span class="legend-title">Perlu Revisi</span>
                        <span class="legend-count">12 Laporan</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color bg-red"></span>
                        <span class="legend-title">Ditolak</span>
                        <span class="legend-count">4 Laporan</span>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection