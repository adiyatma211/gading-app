@extends('layouts.base')
@section('content')
    <div class="page-heading">
        <h3>Dashboard Gading</h3>
    </div>
    <div class="page-content">
        <section class="row">
            <div class="col-12 col-lg-9">
                <div class="row">
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                        <div class="stats-icon purple mb-2">
                                            <i class="iconly-boldShow"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Total Customer</h6>
                                        <h6 class="font-extrabold mb-0">{{ $cust }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                        <div class="stats-icon blue mb-2">
                                            <i class="iconly-boldProfile"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Total Pemesanan</h6>
                                        <h6 class="font-extrabold mb-0">{{ $pesan }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                        <div class="stats-icon green mb-2">
                                            <i class="iconly-boldAdd-User"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Total Pembayaran</h6>
                                        <h6 class="font-extrabold mb-0">{{ $trans }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                        <div class="stats-icon red mb-2">
                                            <i class="iconly-boldBookmark"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Saved Post</h6>
                                        <h6 class="font-extrabold mb-0">112</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Statistik Pemesanan Gading</h4>
                            </div>
                            <div class="card-body">
                                <div id="bar"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Latest Comments</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-lg">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Comment</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="col-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-md">
                                                            <img src="./assets/compiled/jpg/5.jpg">
                                                        </div>
                                                        <p class="font-bold ms-3 mb-0">Si Cantik</p>
                                                    </div>
                                                </td>
                                                <td class="col-auto">
                                                    <p class=" mb-0">Congratulations on your graduation!</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="col-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-md">
                                                            <img src="./assets/compiled/jpg/2.jpg">
                                                        </div>
                                                        <p class="font-bold ms-3 mb-0">Si Ganteng</p>
                                                    </div>
                                                </td>
                                                <td class="col-auto">
                                                    <p class=" mb-0">Wow amazing design! Can you make another
                                                        tutorial for
                                                        this design?</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="col-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-md">
                                                            <img src="./assets/compiled/jpg/8.jpg">
                                                        </div>
                                                        <p class="font-bold ms-3 mb-0">Singh Eknoor</p>
                                                    </div>
                                                </td>
                                                <td class="col-auto">
                                                    <p class=" mb-0">What a stunning design! You are so
                                                        talented and creative!</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="col-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-md">
                                                            <img src="./assets/compiled/jpg/3.jpg">
                                                        </div>
                                                        <p class="font-bold ms-3 mb-0">Rani Jhadav</p>
                                                    </div>
                                                </td>
                                                <td class="col-auto">
                                                    <p class=" mb-0">I love your design! It’s so beautiful and
                                                        unique! How did you learn to do this?</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <div class="card">
                    <div class="card-body py-4 px-4">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xl">
                                <img src="{{ asset('dist/assets/compiled/jpg/1.jpg') }}" alt="Face 1">
                            </div>
                            <div class="ms-3 name">
                                <h5 class="font-bold">{{ Auth::user()->name }}</h5>
                                <h6 class="text-muted mb-0">{{ Auth::user()->role->rolesName }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
                @foreach ($pesanbaru as $a)
                    <div class="card">
                        <div class="card-header">
                            <h4>Pemesanan Baru</h4>
                        </div>
                        <div class="card-content pb-4">
                            <div class="recent-message d-flex px-4 py-3">
                                <div class="avatar avatar-lg">
                                    <img src="{{ asset('dist/assets/compiled/jpg/4.jpg') }}">
                                </div>
                                <div class="name ms-4">
                                    <h5 class="mb-1">PIC KASIR: {{ $a->createdBy }}</h5>
                                    <h5 class="mb-1">Nama Customer: {{ $a->customer->nama }}</h5>
                                    <h6 class="mb-1">Total Pemesanan: Rp {{ number_format($a->total, 0, ',', '.') }}</h6>
                                    <h6 class="mb-1">
                                        Status Pemesanan:
                                        @if (empty($a->diambil_oleh))
                                            <span class="badge bg-warning text-uppercase">Belum Diambil</span>
                                        @else
                                            <span class="badge bg-success text-uppercase"> Selesai
                                        @endif
                                    </h6>
                                    <h6 class="mb-1">
                                        Status Pembayaran:
                                        <span
                                            class="badge
                                            @if ($a->status_pembayaran == 'lunas') bg-success
                                            @else bg-danger @endif">
                                            {{ strtoupper($a->status_pembayaran) }}
                                        </span>
                                    </h6>
                                </div>
                            </div>
                            <div class="px-4">
                                <button class='btn btn-block btn-xl btn-outline-primary font-bold mt-3'> Lihat
                                    Detail</button>
                            </div>
                        </div>
                    </div>
                @endforeach
                {{-- <div class="card">
                    <div class="card-header">
                        <h4>Visitors Profile</h4>
                    </div>
                    <div class="card-body">
                        <div id="chart-visitors-profile"></div>
                    </div>
                </div> --}}
            </div>
        </section>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var options = {
                chart: {
                    type: 'line',
                    height: 350,
                    zoom: {
                        enabled: true
                    },
                },
                series: [{
                    name: 'Pesanan',
                    data: {!! json_encode($pesanChartData->pluck('total')) !!}
                }],
                xaxis: {
                    categories: {!! json_encode($pesanChartData->pluck('bulan')) !!},
                    title: {
                        text: 'Bulan'
                    }
                },
                yaxis: {
                    title: {
                        text: 'Jumlah Pesanan'
                    }
                },
                tooltip: {
                    enabled: true,
                    y: {
                        formatter: val => val + " pesanan"
                    }
                },
                stroke: {
                    curve: 'smooth' // atau 'straight' kalau tidak mau melengkung
                }
            };

            var chart = new ApexCharts(document.querySelector("#bar"), options);
            chart.render();
        });
    </script>
@endsection
