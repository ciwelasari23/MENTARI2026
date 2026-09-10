@extends('layouts.admin')

@section('title', 'Dashboard - MENTARI')
@section('header', 'Dashboard Monitoring Utama')

@section('content')
<!-- Library Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Library Flatpickr (Kalender Picker) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>

<div class="space-y-6">

    <!-- Filter Rentang Waktu - Kalender Picker -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
        <div>
            <h3 class="text-base font-bold text-gray-800">Ringkasan Eksekutif</h3>
            <p class="text-xs text-gray-500 mt-0.5">Pantau capaian progres kegiatan di seluruh wilayah Provinsi Riau.</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Label periode terpilih -->
            <div class="text-right hidden sm:block">
                <p class="text-xs text-gray-400">Periode Dipilih</p>
                <p class="text-sm font-bold text-[#005A9C]" id="label-periode">September 2026</p>
            </div>
            <!-- Input Kalender Flatpickr -->
            <div class="relative">
                <input 
                    type="text" 
                    id="filterKalender" 
                    placeholder="Pilih Bulan & Tahun"
                    readonly
                    class="pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-[#005A9C] cursor-pointer w-48"
                >
                <!-- Ikon Kalender -->
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-[#005A9C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <!-- Tombol Reset -->
            <button 
                onclick="resetFilter()" 
                title="Tampilkan semua periode"
                class="p-2 border border-gray-200 rounded-lg text-gray-400 hover:text-[#005A9C] hover:border-[#005A9C] transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1 -->
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Kegiatan</p>
                <h4 class="text-2xl font-bold text-gray-800">124</h4>
            </div>
        </div>
        <!-- Card 2 -->
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Selesai</p>
                <h4 class="text-2xl font-bold text-gray-800">86</h4>
            </div>
        </div>
        <!-- Card 3 -->
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Dalam Proses</p>
                <h4 class="text-2xl font-bold text-gray-800">32</h4>
            </div>
        </div>
        <!-- Card 4 -->
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-red-50 text-red-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Terlambat</p>
                <h4 class="text-2xl font-bold text-gray-800">6</h4>
            </div>
        </div>
    </div>

    <!-- Area Grafik: Grid 2 Kolom (Bar Chart + Pie Chart) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <!-- Grafik Bar (kiri, 2/3 lebar) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-base font-bold text-gray-800 mb-4">Grafik Rata-rata Capaian per Kabupaten/Kota (%)</h3>
            <div class="relative h-72 w-full">
                <canvas id="capaianChart"></canvas>
            </div>
        </div>

        <!-- Pie Chart Distribusi Status Kegiatan (kanan, 1/3 lebar) -->
        <div class="lg:col-span-1 bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col">
            <h3 class="text-base font-bold text-gray-800 mb-4">Distribusi Status Kegiatan</h3>
            <div class="relative flex-1 flex items-center justify-center" style="min-height: 220px;">
                <canvas id="statusPieChart"></canvas>
                <!-- Label Total di tengah Donut -->
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <span class="text-2xl font-extrabold text-gray-800 leading-none" id="pie-total">124</span>
                    <span class="text-xs font-semibold text-gray-400 mt-0.5">Total Kegiatan</span>
                </div>
            </div>
            <!-- Legend Manual -->
            <div class="mt-4 grid grid-cols-3 gap-2 text-center text-xs">
                <div>
                    <span class="inline-block w-3 h-3 rounded-full bg-emerald-500 mb-1"></span>
                    <p class="font-semibold text-gray-700">Selesai</p>
                    <p class="text-lg font-bold text-emerald-600" id="pie-selesai">86</p>
                </div>
                <div>
                    <span class="inline-block w-3 h-3 rounded-full bg-amber-500 mb-1"></span>
                    <p class="font-semibold text-gray-700">Dalam Proses</p>
                    <p class="text-lg font-bold text-amber-600" id="pie-proses">32</p>
                </div>
                <div>
                    <span class="inline-block w-3 h-3 rounded-full bg-red-500 mb-1"></span>
                    <p class="font-semibold text-gray-700">Terlambat</p>
                    <p class="text-lg font-bold text-red-600" id="pie-terlambat">6</p>
                </div>
            </div>
        </div>

    </div>

    <!-- Tabel Progres Kegiatan Terbaru -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 class="text-base font-bold text-gray-800">Status Progres Kegiatan (Top 5)</h3>
            <a href="#" class="text-sm font-semibold text-[#005A9C] hover:underline">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-white border-b border-gray-100 text-gray-500 font-semibold text-xs uppercase tracking-wider">
                        <th class="p-4">Nama Kegiatan (Level 4)</th>
                        <th class="p-4">Wilayah</th>
                        <th class="p-4 text-center">Target</th>
                        <th class="p-4 text-center">Realisasi</th>
                        <th class="p-4 w-48">Progres Bar</th>
                        <th class="p-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    
                    <!-- Dummy Data 1 -->
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="p-4 font-bold text-gray-800">Data Usaha Konstruksi</td>
                        <td class="p-4">Kab. Kepulauan Meranti</td>
                        <td class="p-4 text-center">123</td>
                        <td class="p-4 text-center">123</td>
                        <td class="p-4">
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-emerald-500 h-2.5 rounded-full" style="width: 100%"></div>
                            </div>
                            <div class="text-xs text-right mt-1 font-semibold text-emerald-600">100%</div>
                        </td>
                        <td class="p-4 text-center">
                            <span class="bg-emerald-100 text-emerald-700 font-bold px-2.5 py-1 rounded-full text-xs">Selesai</span>
                        </td>
                    </tr>

                    <!-- Dummy Data 2 -->
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="p-4 font-bold text-gray-800">Survei Angkatan Kerja Nasional</td>
                        <td class="p-4">Kota Pekanbaru</td>
                        <td class="p-4 text-center">500</td>
                        <td class="p-4 text-center">350</td>
                        <td class="p-4">
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-blue-500 h-2.5 rounded-full" style="width: 70%"></div>
                            </div>
                            <div class="text-xs text-right mt-1 font-semibold text-blue-600">70%</div>
                        </td>
                        <td class="p-4 text-center">
                            <span class="bg-blue-100 text-blue-700 font-bold px-2.5 py-1 rounded-full text-xs">Proses</span>
                        </td>
                    </tr>

                    <!-- Dummy Data 3 -->
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="p-4 font-bold text-gray-800">Sensus Pertanian 2026</td>
                        <td class="p-4">Kab. Kampar</td>
                        <td class="p-4 text-center">200</td>
                        <td class="p-4 text-center">50</td>
                        <td class="p-4">
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-amber-500 h-2.5 rounded-full" style="width: 25%"></div>
                            </div>
                            <div class="text-xs text-right mt-1 font-semibold text-amber-600">25%</div>
                        </td>
                        <td class="p-4 text-center">
                            <span class="bg-amber-100 text-amber-700 font-bold px-2.5 py-1 rounded-full text-xs">Diajukan</span>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Script Inisialisasi Chart.js & Flatpickr -->
<script>
    document.addEventListener("DOMContentLoaded", function() {

        // =============================================
        // Inisialisasi Flatpickr - Kalender Bulan/Tahun
        // =============================================
        flatpickr("#filterKalender", {
            locale: "id",
            plugins: [
                new monthSelectPlugin({
                    shorthand: false,
                    dateFormat: "F Y",
                    altFormat: "F Y",
                    theme: "material_blue"
                })
            ],
            defaultDate: new Date(),
            disableMobile: true,
            onChange: function(selectedDates, dateStr) {
                document.getElementById('label-periode').textContent = dateStr;
            }
        });

        // =============================================
        // Fungsi Reset Filter Kalender
        // =============================================
        window.resetFilter = function() {
            const fp = document.querySelector("#filterKalender")._flatpickr;
            fp.clear();
            document.getElementById('label-periode').textContent = 'Semua Periode';
            document.getElementById('filterKalender').placeholder = 'Pilih Bulan & Tahun';
        };

        const ctx = document.getElementById('capaianChart').getContext('2d');
        
        // Data Dummy untuk Kabupaten/Kota (Nantinya diganti dengan variabel dari Controller)
        const chartData = {
            labels: ['Pekanbaru', 'Dumai', 'Kampar', 'Bengkalis', 'Siak', 'Pelalawan', 'Rokan Hulu', 'Rokan Hilir', 'Inhu', 'Inhil', 'Kuansing', 'Kep. Meranti'],
            datasets: [{
                label: 'Rata-rata Capaian (%)',
                data: [85, 90, 65, 75, 95, 60, 80, 70, 88, 77, 62, 100],
                backgroundColor: '#005A9C', // Warna biru MENTARI
                borderRadius: 4,
                barThickness: 24
            }]
        };

        const config = {
            type: 'bar',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0B1E40',
                        titleFont: { family: 'Inter', size: 13 },
                        bodyFont: { family: 'Inter', size: 13 },
                        padding: 10,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + '% Capaian Selesai';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            stepSize: 20,
                            font: { family: 'Inter', size: 12 }
                        },
                        grid: {
                            color: '#f3f4f6',
                            drawBorder: false
                        }
                    },
                    x: {
                        ticks: {
                            font: { family: 'Inter', size: 12 },
                            autoSkip: false,
                            maxRotation: 45,
                            minRotation: 45
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        };

        new Chart(ctx, config);

        // =============================================
        // Donut Chart: Distribusi Status Kegiatan
        // =============================================
        const ctxPie = document.getElementById('statusPieChart').getContext('2d');

        const pieData = {
            labels: ['Selesai', 'Dalam Proses', 'Terlambat'],
            datasets: [{
                data: [86, 32, 6],
                backgroundColor: [
                    '#10b981', // emerald-500 - Selesai
                    '#f59e0b', // amber-500   - Dalam Proses
                    '#ef4444'  // red-500     - Terlambat
                ],
                borderColor: ['#ffffff', '#ffffff', '#ffffff'],
                borderWidth: 3,
                hoverOffset: 8
            }]
        };

        const pieConfig = {
            type: 'doughnut',
            data: pieData,
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '65%',
                plugins: {
                    legend: {
                        display: false // Legend ditampilkan manual di bawah chart
                    },
                    tooltip: {
                        backgroundColor: '#0B1E40',
                        titleFont: { family: 'Inter', size: 13 },
                        bodyFont: { family: 'Inter', size: 13 },
                        padding: 10,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + context.parsed + ' (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        };

        new Chart(ctxPie, pieConfig);
    });
</script>
@endsection