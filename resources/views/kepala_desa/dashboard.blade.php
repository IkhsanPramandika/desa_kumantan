@extends('layouts.app_kepala_desa')

@section('title', 'Dashboard Kepala Desa')

@push('styles')
<style>
    .stat-card .h5 { font-size: 1.75rem; }
    .chart-container { position: relative; height: 320px; width: 100%; }
    .horizontal-chart-container { position: relative; height: 400px; width: 100%; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Kepala Desa</h1>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 stat-card"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Permohonan</div><div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPermohonan ?? 0 }}</div></div><div class="col-auto"><i class="fas fa-file-alt fa-2x text-gray-300"></i></div></div></div></div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 stat-card"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="text-xs font-weight-bold text-success text-uppercase mb-1">Permohonan Selesai</div><div class="h5 mb-0 font-weight-bold text-gray-800">{{ $permohonanSelesai ?? 0 }}</div></div><div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div></div></div></div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2 stat-card"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Menunggu Persetujuan</div><div class="h5 mb-0 font-weight-bold text-gray-800">{{ $permohonanPending ?? 0 }}</div></div><div class="col-auto"><i class="fas fa-clock fa-2x text-gray-300"></i></div></div></div></div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2 stat-card"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="text-xs font-weight-bold text-info text-uppercase mb-1">Rata-rata Proses</div><div class="h5 mb-0 font-weight-bold text-gray-800">{{ $rataRataProses ?? 'N/A' }}</div></div><div class="col-auto"><i class="fas fa-hourglass-half fa-2x text-gray-300"></i></div></div></div></div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Tren Permohonan Bulanan (Tahun {{ Carbon\Carbon::now()->year }})</h6></div>
                <div class="card-body"><div class="chart-container"><canvas id="monthlyTrendChart"></canvas></div></div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Volume Layanan Paling Banyak Diajukan</h6></div>
                <div class="card-body"><div class="horizontal-chart-container"><canvas id="jenisPermohonanChart"></canvas></div></div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top 5 Layanan Terpopuler</h6>
                </div>
                <div class="card-body">
                    @forelse($permohonanByJenis as $jenis => $total)
                        @if($loop->index < 5)
                            <h4 class="small font-weight-bold">{{ $jenis }} <span class="float-right">{{ $total }}</span></h4>
                            <div class="progress mb-4">
                                @php $percentage = ($totalPermohonan > 0) ? ($total / $totalPermohonan) * 100 : 0; @endphp
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        @endif
                    @empty
                        <p class="text-center text-muted">Belum ada data permohonan.</p>
                    @endforelse
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Analisis Waktu Proses Layanan</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 280px;">
                        <canvas id="waktuProsesChart"></canvas>
                    </div>
                     <div class="mt-2 text-center small">
                        <span class="mr-2"><i class="fas fa-circle text-success"></i> Cepat</span>
                        <span class="mr-2"><i class="fas fa-circle text-warning"></i> Sedang</span>
                        <span class="mr-2"><i class="fas fa-circle text-danger"></i> Lambat</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Pastikan Anda memuat Chart.js, misalnya dari CDN atau asset lokal --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Data dari Controller
    const permohonanBulanan = @json($permohonanBulanan ?? []);
    const permohonanByJenis = @json($permohonanByJenis ?? []);
    const waktuProsesData = @json($waktuProsesByJenis ?? []);

    // Konfigurasi Umum Chart.js
    Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#858796';

    // Chart #1: Tren Bulanan
    if (document.getElementById("monthlyTrendChart")) {
        var ctxMonthly = document.getElementById("monthlyTrendChart").getContext('2d');
        new Chart(ctxMonthly, {
            type: 'bar',
            data: {
                labels: Object.keys(permohonanBulanan),
                datasets: [{
                    label: "Jumlah Permohonan",
                    backgroundColor: "#4e73df",
                    hoverBackgroundColor: "#2e59d9",
                    borderColor: "#4e73df",
                    data: Object.values(permohonanBulanan),
                }],
            },
            options: {
                maintainAspectRatio: false,
                legend: { display: false },
                scales: {
                    xAxes: [{ gridLines: { display: false, drawBorder: false }, ticks: { maxTicksLimit: 12 } }],
                    yAxes: [{ ticks: { min: 0, padding: 10, precision: 0 }, gridLines: { color: "rgb(234, 236, 244)", zeroLineColor: "rgb(234, 236, 244)", drawBorder: false, borderDash: [2], zeroLineBorderDash: [2] } }],
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, chart) { return 'Total: ' + tooltipItem.yLabel; }
                    }
                }
            }
        });
    }

    // Chart #2: Volume Layanan
    if (document.getElementById("jenisPermohonanChart")) {
        var ctxHorizontal = document.getElementById("jenisPermohonanChart").getContext('2d');
        new Chart(ctxHorizontal, {
            type: 'horizontalBar',
            data: {
                labels: Object.keys(permohonanByJenis),
                datasets: [{
                    label: 'Jumlah Diajukan',
                    backgroundColor: '#4e73df',
                    data: Object.values(permohonanByJenis)
                }]
            },
            options: {
                maintainAspectRatio: false,
                legend: { display: false },
                scales: {
                    xAxes: [{
                        ticks: {
                            beginAtZero: true,
                            precision: 0
                        }
                    }]
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, chart) { return 'Total: ' + tooltipItem.xLabel; }
                    }
                }
            }
        });
    }

    // Chart #3: Analisis Waktu Proses
    if (document.getElementById("waktuProsesChart")) {
        const ctxWaktuProses = document.getElementById("waktuProsesChart").getContext('2d');
        
        const dataValues = Object.values(waktuProsesData);
        const backgroundColors = dataValues.map(value => {
            if (value > 5) return '#e74a3b'; // Merah (Lambat > 5 hari)
            if (value > 2) return '#f6c23e'; // Kuning (Sedang 3-5 hari)
            return '#1cc88a';              // Hijau (Cepat <= 2 hari)
        });

        new Chart(ctxWaktuProses, {
            type: 'horizontalBar',
            data: {
                labels: Object.keys(waktuProsesData),
                datasets: [{
                    label: 'Rata-rata Hari',
                    backgroundColor: backgroundColors,
                    borderColor: backgroundColors,
                    data: dataValues,
                }]
            },
            options: {
                maintainAspectRatio: false,
                legend: { display: false },
                scales: {
                    xAxes: [{
                        ticks: {
                            beginAtZero: true,
                            precision: 0,
                            callback: function(value) { return value + ' hari' }
                        }
                    }]
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, chart) {
                            return tooltipItem.xLabel.toFixed(1) + ' hari';
                        }
                    }
                }
            }
        });
    }

    // [DIHAPUS] Chart Doughnut Komposisi tidak lagi ditampilkan di mockup ini, 
    // Anda bisa menambahkannya kembali jika perlu.
});
</script>
@endpush