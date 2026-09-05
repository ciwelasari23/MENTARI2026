@extends('layouts.admin')

@section('title', 'Kelola Kegiatan Level 1 (Output)')

@section('content')
    <div class="card-container">
        
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-extrabold text-[#0B1E40]">Daftar Output Kegiatan</h2>
            <button class="btn-teal">
                + Tambah Output
            </button>
        </div>

        <!-- Tabel Output Kegiatan -->
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Output</th>
                        <th>Tahun</th>
                        <th>Tim Kerja</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td class="td-title">Publikasi BPS SE 2026</td>
                        <td>2026</td>
                        <td>-</td>
                        <td>
                            <!-- Tombol aksi Anda (Detail, Edit, Hapus) -->
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
    </div>
@endsection