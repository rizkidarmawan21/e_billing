@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="mb-4">
        <h3 class="fw-bold mb-1">
            Dashboard
        </h3>

        <p class="text-muted mb-0">
            Ringkasan data E-Billing
        </p>
    </div>


    {{-- ========================= --}}
    {{-- FILTER --}}
    {{-- ========================= --}}

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            <form method="GET"
                  action="{{ route('dashboard') }}">

                <div class="row align-items-end g-3">

                    {{-- TAHUN --}}
                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            Tahun
                        </label>

                        <select name="tahun"
                                class="form-select"
                                onchange="this.form.submit()">

                            @foreach($tahunList as $tahunItem)

                                <option value="{{ $tahunItem }}"
                                    {{ $tahun == $tahunItem ? 'selected' : '' }}>

                                    {{ $tahunItem }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- FILTER BULAN --}}
                    <div class="col-md-4">

                        <div class="form-check mb-2">

                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="filterBulan"
                                {{ $bulan ? 'checked' : '' }}
                                onchange="toggleBulan()">

                            <label class="form-check-label fw-semibold"
                                   for="filterBulan">

                                Filter Bulan

                            </label>

                        </div>


                        <select name="bulan"
                                id="selectBulan"
                                class="form-select"
                                {{ $bulan ? '' : 'disabled' }}
                                onchange="this.form.submit()">

                            <option value="">
                                Semua Bulan
                            </option>

                            @foreach($namaBulan as $nomor => $nama)

                                <option value="{{ $nama }}"
                                    {{ $bulan == $nama ? 'selected' : '' }}>

                                    {{ $nama }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

            </form>

        </div>
    </div>



    {{-- ========================= --}}
    {{-- RINGKASAN --}}
    {{-- ========================= --}}

    <div class="row g-4 mb-4">

        {{-- TOTAL USER --}}
        <div class="col-md-6 col-xl-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <p class="text-muted mb-2">
                        Total User
                    </p>

                    <h2 class="fw-bold mb-0">
                        {{ $totalUser }}
                    </h2>

                    <small class="text-muted">
                        User aktif
                    </small>

                </div>

            </div>

        </div>


        {{-- TOTAL TAGIHAN --}}
        <div class="col-md-6 col-xl-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <p class="text-muted mb-2">
                        Total Tagihan
                    </p>

                    <h2 class="fw-bold mb-0">

                        Rp {{ number_format($totalTagihan, 0, ',', '.') }}

                    </h2>

                    <small class="text-muted">
                        Total nominal tagihan
                    </small>

                </div>

            </div>

        </div>


        {{-- SUDAH BAYAR --}}
        <div class="col-md-6 col-xl-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <p class="text-muted mb-2">
                        Sudah Bayar
                    </p>

                    <h2 class="fw-bold text-success mb-0">
                        {{ $sudahBayar }}
                    </h2>

                    <small class="text-muted">
                        Tagihan lunas
                    </small>

                </div>

            </div>

        </div>


        {{-- BELUM BAYAR --}}
        <div class="col-md-6 col-xl-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <p class="text-muted mb-2">
                        Belum Bayar
                    </p>

                    <h2 class="fw-bold text-danger mb-0">
                        {{ $belumBayar }}
                    </h2>

                    <small class="text-muted">
                        Tagihan belum lunas
                    </small>

                </div>

            </div>

        </div>

    </div>



    {{-- ========================= --}}
    {{-- DIAGRAM --}}
    {{-- ========================= --}}

    <div class="row g-4">

        {{-- DIAGRAM DONUT --}}
        <div class="col-lg-5">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="mb-3">

                        <h5 class="fw-bold mb-1">
                            Status Pembayaran
                        </h5>

                        <small class="text-muted">

                            @if($bulan)
                                Status pembayaran {{ $bulan }} {{ $tahun }}
                            @else
                                Status pembayaran tahun {{ $tahun }}
                            @endif

                        </small>

                    </div>


                    <div style="height: 320px;">

                        <canvas id="paymentChart"></canvas>

                    </div>

                </div>

            </div>

        </div>



        {{-- DIAGRAM BATANG --}}
        <div class="col-lg-7">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="mb-3">

                        <h4>Tunggakan Customer per Bulan</h4>

                        <small class="text-muted">
                             Jumlah customer yang belum membayar pada setiap bulan
                             tahun {{ $tahun }}
                        </small>

                    </div>


                    <div style="height: 320px;">

                        <canvas id="userChart"></canvas>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>



{{-- ========================= --}}
{{-- CHART.JS --}}
{{-- ========================= --}}

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>

    /*
    |--------------------------------------------------------------------------
    | FILTER BULAN
    |--------------------------------------------------------------------------
    */

    function toggleBulan() {

        const checkbox = document.getElementById('filterBulan');

        const select = document.getElementById('selectBulan');

        if (checkbox.checked) {

            select.disabled = false;

        } else {

            select.disabled = true;

            select.value = '';

            select.form.submit();

        }

    }


    /*
    |--------------------------------------------------------------------------
    | DIAGRAM DONUT
    |--------------------------------------------------------------------------
    */

    const paymentChart =
        document.getElementById('paymentChart');


    new Chart(paymentChart, {

        type: 'doughnut',

        data: {

            labels: [
                'Sudah Bayar',
                'Belum Bayar'
            ],

            datasets: [{

                data: [
                    {{ $sudahBayar }},
                    {{ $belumBayar }}
                ]

            }]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            plugins: {

                legend: {

                    position: 'bottom'

                }

            }

        }

    });



    /*
    |--------------------------------------------------------------------------
    | DIAGRAM BATANG
    |--------------------------------------------------------------------------
    */

    const userChart =
        document.getElementById('userChart');


    new Chart(userChart, {

        type: 'bar',

        data: {

            labels: [

                @foreach($namaBulan as $nama)

                    '{{ $nama }}',

                @endforeach

            ],

            datasets: [{

                label: 'Tunggakan Customer',

                data: [

                    @foreach($tunggakanPerBulan as $jumlah)

                        {{ $jumlah }},

                    @endforeach

                ],

                borderWidth: 1

            }]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            scales: {

                y: {

                    beginAtZero: true,

                    ticks: {

                        stepSize: 10

                    }

                }

            },

            plugins: {

                legend: {

                    display: false

                }

            }

        }

    });

</script>

@endsection