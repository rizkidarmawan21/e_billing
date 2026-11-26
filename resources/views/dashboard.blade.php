@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    {{-- HEADER --}}
    <x-ui.page-header title="Dashboard" description="Ringkasan data E-Billing" />


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

                        <label for="dashboardTahun" class="form-label fw-semibold">
                            Tahun
                        </label>

                        <select name="tahun"
                                id="dashboardTahun"
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
                                aria-label="Bulan tagihan"
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
        <div class="col-12 col-sm-6 col-xl-3">

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
        <div class="col-12 col-sm-6 col-xl-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <p class="text-muted mb-2">
                        Total Tagihan
                    </p>

                    <h2 class="fw-bold mb-0 text-break">

                        Rp {{ number_format($totalTagihan, 0, ',', '.') }}

                    </h2>

                    <small class="text-muted">
                        Total nominal tagihan
                    </small>

                </div>

            </div>

        </div>


        {{-- SUDAH BAYAR --}}
        <div class="col-12 col-sm-6 col-xl-3">

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
        <div class="col-12 col-sm-6 col-xl-3">

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
{{-- TOMBOL ALL --}}
{{-- ========================= --}}

<x-ui.form-actions class="justify-content-center mb-4">

    {{-- TOMBOL ALL --}}
    <a href="{{ route('dashboard', ['tahun' => $tahun, 'all' => 1]) }}"
       class="btn btn-primary px-4">
        ALL
    </a>

    {{-- KEMBALI KE BULAN SEKARANG --}}
    <a href="{{ route('dashboard', ['tahun' => now()->year]) }}"
       class="btn btn-outline-secondary px-4">
        Bulan Sekarang
    </a>

</x-ui.form-actions>

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

                            @if($showAll)
                                Status pembayaran seluruh tahun {{ $tahun }}
                            @else
                                Status pembayaran {{ $periodeDashboard }} {{ $tahun }}
                            @endif

                        </small>

                    </div>


                    <p id="paymentChartSummary" class="visually-hidden">
                        Sudah bayar: {{ $sudahBayar }}. Belum bayar: {{ $belumBayar }}.
                    </p>
                    @if($sudahBayar + $belumBayar === 0)
                        <x-ui.empty-state title="Belum ada data pembayaran" description="Pilih periode lain untuk melihat status pembayaran." />
                    @endif

                    <div class="dashboard-chart">

                        <canvas id="paymentChart" role="img" aria-label="Status pembayaran" aria-describedby="paymentChartSummary"></canvas>

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


                    <p id="userChartSummary" class="visually-hidden">
                        Tunggakan customer tahun {{ $tahun }}:
                        @foreach($diagramLabels as $label)
                            {{ $label }}: {{ $diagramData[$loop->index] }} customer{{ $loop->last ? '.' : ';' }}
                        @endforeach
                    </p>
                    @if(array_sum($diagramData) === 0)
                        <p class="text-muted small">Tidak ada tunggakan customer pada periode yang ditampilkan.</p>
                    @endif

                    <div class="dashboard-chart">

                        <canvas id="userChart" role="img" aria-label="Tunggakan customer per bulan" aria-describedby="userChartSummary"></canvas>

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
    | DIAGRAM DONUT: STATUS PEMBAYARAN
    |--------------------------------------------------------------------------
    */
    const paymentCanvas = document.getElementById('paymentChart');

    if (paymentCanvas) {
        new Chart(paymentCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Sudah Bayar', 'Belum Bayar'],
                datasets: [{
                    data: [
                        @json($sudahBayar),
                        @json($belumBayar)
                    ],
                    borderWidth: 1
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
    }

    /*
    |--------------------------------------------------------------------------
    | DIAGRAM BATANG: TUNGGAKAN CUSTOMER
    |--------------------------------------------------------------------------
    */
    const userCanvas = document.getElementById('userChart');

    if (userCanvas) {
        new Chart(userCanvas, {
            type: 'bar',
            data: {
                labels: @json($diagramLabels),
                datasets: [{
                    label: 'Tunggakan Customer',
                    data: @json($diagramData),
                    borderWidth: 1,
                    barThickness: {{ $showAll ? '40' : '25' }}
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
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
    }
</script>

@endsection