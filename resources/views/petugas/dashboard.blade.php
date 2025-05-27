@extends('layouts.app')

@section('title', 'Dashboard Petugas') {{-- Mengatur judul halaman --}}

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Layanan Desa Kumantan</h1>
    </div>

    {{-- Ringkasan Statistik Global (menggunakan card yang lebih besar atau digabungkan) --}}
    <div class="row">
        {{-- Card Statistik Gabungan --}}
        <div class="col-xl-6 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Ringkasan Statistik Permohonan</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-warning h-100 py-2">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $overallTotalPending ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-success h-100 py-2">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Diterima</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $overallTotalAccepted ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-danger h-100 py-2">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Ditolak</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $overallTotalRejected ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Total Pengguna --}}
        <div class="col-xl-6 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Umum</h6>
                </div>
                <div class="card-body text-center">
                    <div class="h5 mb-0 font-weight-bold text-gray-800 mt-3">Total Pengguna Sistem</div>
                    <div class="h1 mt-2 mb-4 text-primary">{{ $totalUsers ?? 0 }}</div>
                    <i class="fas fa-users fa-5x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Statistik Permohonan per Jenis dalam bentuk Card yang lebih rapi --}}
    <div class="row">
        <div class="col-12 mb-4">
            <h4 class="h4 mb-2 text-gray-800">Detail Statistik Permohonan per Jenis</h4>
        </div>

        {{-- Bagian untuk setiap jenis permohonan --}}
        @php
            $permohonanTypes = [
                'kkBaru'          => ['title' => 'Kartu Keluarga Baru', 'icon' => 'fas fa-id-card-alt', 'route' => 'permohonan-kk.index', 'color' => 'primary'],
                'kkPerubahanData' => ['title' => 'KK Perubahan Data', 'icon' => 'fas fa-edit', 'route' => 'permohonan-kk-perubahan.index', 'color' => 'success'],
                'kkHilang'        => ['title' => 'Kartu Keluarga Hilang', 'icon' => 'fas fa-id-card', 'route' => 'permohonan-kk-hilang.index', 'color' => 'info'],
                'skKelahiran'     => ['title' => 'SK Kelahiran & Akta', 'icon' => 'fas fa-baby', 'route' => 'permohonan-sk-kelahiran.index', 'color' => 'warning'],
                'skAhliWaris'     => ['title' => 'SK Ahli Waris', 'icon' => 'fas fa-gavel', 'route' => 'permohonan-sk-ahli-waris.index', 'color' => 'danger'],
                'skPerkawinan'    => ['title' => 'Surat Pengantar Nikah', 'icon' => 'fas fa-ring', 'route' => 'permohonan-sk-perkawinan.index', 'color' => 'dark'],
                'skUsaha'         => ['title' => 'Surat Keterangan Usaha', 'icon' => 'fas fa-briefcase', 'route' => 'permohonan-sk-usaha.index', 'color' => 'secondary'],
                'skDomisili'      => ['title' => 'Surat Keterangan Domisili', 'icon' => 'fas fa-home', 'route' => 'permohonan-sk-domisili.index', 'color' => 'primary'],
                'skTidakMampu'    => ['title' => 'SK Tidak Mampu', 'icon' => 'fas fa-hand-holding-heart', 'route' => 'permohonan-sk-tidak-mampu.index', 'color' => 'info'],
            ];
        @endphp

        @foreach ($permohonanTypes as $key => $type)
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card shadow h-100 py-2 border-left-{{ $type['color'] }}">
                    <div class="card-body">
                        <h6 class="font-weight-bold text-{{ $type['color'] }} mb-3">
                            <i class="{{ $type['icon'] }} mr-2"></i> {{ $type['title'] }}
                        </h6>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Pending
                                <span class="badge badge-warning badge-pill">{{ $stats[$key]['pending'] ?? 0 }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Diterima
                                <span class="badge badge-success badge-pill">{{ $stats[$key]['diterima'] ?? 0 }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Ditolak
                                <span class="badge badge-danger badge-pill">{{ $stats[$key]['ditolak'] ?? 0 }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center font-weight-bold">
                                Total
                                <span class="badge badge-{{ $type['color'] }} badge-pill">{{ $stats[$key]['total'] ?? 0 }}</span>
                            </li>
                        </ul>
                        <a href="{{ route($type['route']) }}" class="btn btn-{{ $type['color'] }} btn-block mt-3">Lihat Detail</a>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Contoh area untuk Chart (jika ingin menambahkan visualisasi) --}}
        {{-- Aktifkan script Chart.js di bagian @push('scripts') di bawah --}}
        {{-- <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Grafik Status Permohonan</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="myAreaChart"></canvas>
                    </div>
                </div>
            </div>
        </div> --}}

        {{-- <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Permohonan Berdasarkan Status</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="myPieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> Pending
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Diterima
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-danger"></i> Ditolak
                        </span>
                    </div>
                </div>
            </div>
        </div> --}}

    </div>
</div>
@endsection

@push('scripts')
{{-- Script untuk Chart.js (hanya aktifkan jika Anda ingin menggunakan grafik) --}}
{{--
<script src="{{ asset('sbadmin/vendor/chart.js/Chart.min.js') }}"></script>
<script>
    // Data yang akan digunakan di Chart.js
    var totalPending = {{ $overallTotalPending ?? 0 }};
    var totalAccepted = {{ $overallTotalAccepted ?? 0 }};
    var totalRejected = {{ $overallTotalRejected ?? 0 }};

    // Area Chart Example (jika mau menampilkan tren)
    // var ctx = document.getElementById("myAreaChart");
    // var myLineChart = new Chart(ctx, {
    //   type: 'line',
    //   data: {
    //     labels: ["Pending", "Diterima", "Ditolak"],
    //     datasets: [{
    //       label: "Total",
    //       lineTension: 0.3,
    //       backgroundColor: "rgba(78, 115, 223, 0.05)",
    //       borderColor: "rgba(78, 115, 223, 1)",
    //       pointRadius: 3,
    //       pointBackgroundColor: "rgba(78, 115, 223, 1)",
    //       pointBorderColor: "rgba(78, 115, 223, 1)",
    //       pointHoverRadius: 3,
    //       pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
    //       pointHitRadius: 10,
    //       pointBorderWidth: 2,
    //       data: [totalPending, totalAccepted, totalRejected],
    //     }],
    //   },
    //   options: {
    //     maintainAspectRatio: false,
    //     layout: {
    //       padding: {
    //         left: 10,
    //         right: 25,
    //         top: 25,
    //         bottom: 0
    //       }
    //     },
    //     scales: {
    //       xAxes: [{
    //         time: {
    //           unit: 'date'
    //         },
    //         gridLines: {
    //           display: false,
    //           drawBorder: false
    //         },
    //         ticks: {
    //           maxTicksLimit: 7
    //         }
    //       }],
    //       yAxes: [{
    //         ticks: {
    //           maxTicksLimit: 5,
    //           padding: 10,
    //           // Include a dollar sign in the ticks
    //           callback: function(value, index, values) {
    //             return number_format(value);
    //           }
    //         },
    //         gridLines: {
    //           color: "rgb(234, 236, 244)",
    //           zeroLineColor: "rgb(234, 236, 244)",
    //           drawBorder: false,
    //           borderDash: [2],
    //           zeroLineBorderDash: [2]
    //         }
    //       }],
    //     },
    //     legend: {
    //       display: false
    //     },
    //     tooltips: {
    //       backgroundColor: "rgb(255,255,255)",
    //       bodyFontColor: "#858796",
    //       titleMarginBottom: 10,
    //       titleFontColor: '#6e707e',
    //       titleFontSize: 14,
    //       borderColor: '#dddfeb',
    //       borderWidth: 1,
    //       xPadding: 15,
    //       yPadding: 15,
    //       displayColors: false,
    //       intersect: false,
    //       mode: 'index',
    //       caretPadding: 10,
    //       callbacks: {
    //         label: function(tooltipItem, chart) {
    //           var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
    //           return datasetLabel + ': ' + number_format(tooltipItem.yLabel);
    //         }
    //       }
    //     }
    //   }
    // });

    // Pie Chart Example
    var ctxPie = document.getElementById("myPieChart");
    var myPieChart = new Chart(ctxPie, {
      type: 'doughnut',
      data: {
        labels: ["Pending", "Diterima", "Ditolak"],
        datasets: [{
          data: [totalPending, totalAccepted, totalRejected],
          backgroundColor: ['#f6c23e', '#1cc88a', '#e74a3b'], // Warna warning, success, danger
          hoverBackgroundColor: ['#e4b63a', '#17a673', '#cc392c'],
          hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
      },
      options: {
        maintainAspectRatio: false,
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

    // Function to format numbers (from SB Admin 2 demo)
    function number_format(number, decimals, dec_point, thousands_sep) {
      number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
      var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function(n, prec) {
          var k = Math.pow(10, prec);
          return '' + Math.round(n * k) / k;
        };
      s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
      if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
      }
      if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
      }
      return s.join(dec);
    }
</script>
--}}
@endpush    