@extends('layouts.app')

@section('title', 'Dashboard Petugas')

@push('styles')
{{-- Menambahkan sedikit style kustom untuk tampilan yang lebih baik --}}
<style>
    .icon-circle {
        height: 3rem;
        width: 3rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        color: white;
        font-size: 1.5rem;
    }
    .card-link {
        text-decoration: none;
        color: inherit;
        display: block;
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .card-link:hover {
        transform: translateY(-5px);
        text-decoration: none;
        color: inherit;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
    /* PERBAIKAN WARNA: Gradien dengan tone yang lebih kalem */
    .bg-gradient-pending {
        background: linear-gradient(45deg, #FAD961 0%, #F76B1C 100%);
    }
    .bg-gradient-processing {
        background: linear-gradient(45deg, #89f7fe 0%, #66a6ff 100%);
    }
    .bg-gradient-completed {
        background: linear-gradient(45deg, #96fbc4 0%, #f9f586 100%);
    }
    .bg-gradient-rejected {
        background: linear-gradient(45deg, #ff8a80 0%, #ffabab 100%);
    }
    .card-gradient-text {
        color: white;
    }
    .card-gradient-text .text-gray-800 {
        color: white !important;
        text-shadow: 1px 1px 3px rgba(0,0,0,0.2);
    }
    .card-gradient-text .text-gray-300 {
        color: rgba(255, 255, 255, 0.7) !important;
    }
    .card-gradient-text .text-uppercase {
        color: rgba(255, 255, 255, 0.9) !important;
        font-weight: 500 !important;
    }
     /* PERBAIKAN WARNA: Menambahkan class warna kalem untuk menu layanan */
    .border-left-calm-1 { border-left: 0.25rem solid #6c5ce7 !important; }
    .bg-calm-1 { background-color: #6c5ce7 !important; }
    .text-calm-1 { color: #6c5ce7 !important; }
    .border-left-calm-2 { border-left: 0.25rem solid #48dbfb !important; }
    .bg-calm-2 { background-color: #48dbfb !important; }
    .text-calm-2 { color: #48dbfb !important; }
    .border-left-calm-3 { border-left: 0.25rem solid #1dd1a1 !important; }
    .bg-calm-3 { background-color: #1dd1a1 !important; }
    .text-calm-3 { color: #1dd1a1 !important; }
    .border-left-calm-4 { border-left: 0.25rem solid #feca57 !important; }
    .bg-calm-4 { background-color: #feca57 !important; }
    .text-calm-4 { color: #feca57 !important; }
    .border-left-calm-5 { border-left: 0.25rem solid #ff9f43 !important; }
    .bg-calm-5 { background-color: #ff9f43 !important; }
    .text-calm-5 { color: #ff9f43 !important; }
    .border-left-calm-6 { border-left: 0.25rem solid #54a0ff !important; }
    .bg-calm-6 { background-color: #54a0ff !important; }
    .text-calm-6 { color: #54a0ff !important; }
    .border-left-calm-7 { border-left: 0.25rem solid #5f27cd !important; }
    .bg-calm-7 { background-color: #5f27cd !important; }
    .text-calm-7 { color: #5f27cd !important; }
    .border-left-calm-8 { border-left: 0.25rem solid #00d2d3 !important; }
    .bg-calm-8 { background-color: #00d2d3 !important; }
    .text-calm-8 { color: #00d2d3 !important; }
    .border-left-calm-9 { border-left: 0.25rem solid #ff6b6b !important; }
    .bg-calm-9 { background-color: #ff6b6b !important; }
    .text-calm-9 { color: #ff6b6b !important; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Layanan Desa</h1>
    </div>

    {{-- PERBAIKAN TAMPILAN: Kartu statistik sekarang menggunakan gradien kalem --}}
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Permohonan Pending</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $overallTotalPending ?? 0 }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-clock fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Sedang Diproses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $overallTotalInProcess ?? 0 }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-cogs fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Sudah Selesai</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $overallTotalAccepted ?? 0 }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Permohonan Ditolak</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $overallTotalRejected ?? 0 }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-times-circle fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    

    {{-- Konten Utama: Menu Layanan dan Grafik --}}
    <div class="row">
        {{-- Kolom Kiri: Menu Layanan --}}
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Akses Cepat Layanan Surat</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @php
                            $calmColors = ['calm-1', 'calm-2', 'calm-3', 'calm-4', 'calm-5', 'calm-6', 'calm-7', 'calm-8', 'calm-9'];
                        @endphp
                        @foreach ($permohonanDetails as $key => $details)
                        @php
                            // Mengambil class warna secara berurutan dan berulang
                            $colorClass = $calmColors[$loop->index % count($calmColors)];
                        @endphp
                        <div class="col-lg-6 col-md-6 mb-4">
                            <a href="{{ route($details['route']) }}" class="card-link">
                                {{-- PERBAIKAN: Menggunakan class warna kalem baru --}}
                                <div class="card border-left-{{ $colorClass }} shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle bg-{{ $colorClass }} mr-3">
                                                <i class="{{ $details['icon'] }}"></i>
                                            </div>
                                            <div>
                                                <h6 class="font-weight-bold mb-0 text-{{ $colorClass }}">{{ $details['title'] }}</h6>
                                                <small class="text-muted">{{ $stats[$key]['total'] ?? 0 }} Permohonan Diajukan</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Grafik dan Info Pengguna --}}
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Komposisi Status Permohonan</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="statusPieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2"><i class="fas fa-circle text-warning"></i> Pending</span>
                        <span class="mr-2"><i class="fas fa-circle text-info"></i> Diproses</span>
                        <span class="mr-2"><i class="fas fa-circle text-success"></i> Selesai</span>
                        <span class="mr-2"><i class="fas fa-circle text-danger"></i> Ditolak</span>
                    </div>
                </div>
            </div>
             <div class="card shadow mb-4">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                    <h6 class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pengguna Terdaftar</h6>
                    <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $totalUsers ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Pastikan Chart.js sudah di-load di layout utama Anda --}}
<script src="{{ asset('sbadmin/vendor/chart.js/Chart.min.js') }}"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Data untuk Pie Chart
    var ctx = document.getElementById("statusPieChart").getContext('2d');
    var myPieChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ["Pending", "Diproses", "Selesai", "Ditolak"],
            datasets: [{
                data: [
                    {{ $overallTotalPending ?? 0 }}, 
                    {{ $overallTotalInProcess ?? 0 }}, 
                    {{ $overallTotalAccepted ?? 0 }}, 
                    {{ $overallTotalRejected ?? 0 }}
                ],
                // PERBAIKAN: Menyesuaikan warna chart dengan tema kartu statistik
                backgroundColor: ['#FAD961', '#66a6ff', '#85e09b', '#ff8a80'],
                hoverBackgroundColor: ['#f8c291', '#30c7ec', '#78e08f', '#ffabab'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
            },
            legend: {
                display: false
            },
            cutoutPercentage: 80,
        },
    });
});
</script>
@endpush
