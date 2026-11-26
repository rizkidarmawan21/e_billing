@extends('owner.layout')

@section('content')

<x-ui.page-header title="Dashboard Owner" description="Ringkasan informasi sistem e-Billing">
<x-slot:actions>
{{-- FILTER BULAN --}}
<div class="dropdown">

    <button
        class="btn btn-outline-primary dropdown-toggle"
        type="button"
        data-bs-toggle="dropdown"
        data-bs-auto-close="outside"
        aria-expanded="false"
    >
        <i class="bi bi-funnel me-1"></i>

        Filter Bulan

        @if($filterActive && !empty($selectedMonths))
            <span class="badge bg-primary ms-1">
                {{ count($selectedMonths) }}
            </span>
        @endif

    </button>


    <div
        class="dropdown-menu dropdown-menu-end filter-menu p-3 shadow"
    >

        <form
            method="GET"
            action="{{ route('owner.dashboard') }}"
        >

            {{-- AKTIFKAN FILTER --}}
            <div class="form-check form-switch mb-3">

                <input
                    class="form-check-input"
                    type="checkbox"
                    name="filter_active"
                    value="1"
                    id="filter_active"
                    {{ $filterActive ? 'checked' : '' }}
                    onchange="toggleBulan()"
                >

                <label
                    class="form-check-label fw-semibold"
                    for="filter_active"
                >
                    Aktifkan Filter Bulan
                </label>

            </div>


            <hr>


            {{-- PILIHAN BULAN --}}
            <div
                id="pilihanBulan"
                style="{{ $filterActive ? '' : 'display: none;' }}"
            >

                <p class="small text-muted mb-2">
                    Pilih satu atau beberapa bulan:
                </p>


                <div class="row">

                    @foreach($namaBulan as $nomor => $bulan)

                        <div class="col-6 mb-2">

                            <div class="form-check">

                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="months[]"
                                    value="{{ $bulan }}"
                                    id="bulan{{ $nomor }}"
                                    {{ in_array($bulan, $selectedMonths) ? 'checked' : '' }}
                                >

                                <label
                                    class="form-check-label"
                                    for="bulan{{ $nomor }}"
                                >
                                    {{ $bulan }}
                                </label>

                            </div>

                        </div>

                    @endforeach

                </div>


                <hr>

            </div>

            <x-ui.form-actions class="justify-content-end">

                    <a
                        href="{{ route('owner.dashboard') }}"
                        class="btn btn-light"
                    >
                        Reset
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Terapkan
                    </button>

            </x-ui.form-actions>

        </form>

    </div>

</div>

</x-slot:actions>
</x-ui.page-header>

{{-- ========================================== --}}
{{-- CARD STATISTIK --}}
{{-- ========================================== --}}

<div class="row g-3 mb-4">

    {{-- TOTAL USER --}}
    <div class="col-12 col-sm-6 col-xl-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body p-3">

                <div class="d-flex align-items-center">

                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 45px; height: 45px;">

                        <i class="bi bi-people fs-5"></i>

                    </div>

                    <div class="ms-2">

                        <p class="text-muted mb-1 small">
                            Total User
                        </p>

                        <h5 class="fw-bold mb-0">
                            {{ $totalUsers }}
                        </h5>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- TOTAL TAGIHAN --}}
    <div class="col-12 col-sm-6 col-xl-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body p-3">

                <div class="d-flex align-items-center">

                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 45px; height: 45px;">

                        <i class="bi bi-receipt fs-5"></i>

                    </div>

                    <div class="ms-2">

                        <p class="text-muted mb-1 small">
                            Total Tagihan
                        </p>

                        <h5 class="fw-bold mb-0 text-break">
                            Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                        </h5>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- PENDAPATAN --}}
    <div class="col-12 col-sm-6 col-xl-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body p-3">

                <div class="d-flex align-items-center">

                    <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 45px; height: 45px;">

                        <i class="bi bi-cash-stack fs-5"></i>

                    </div>

                    <div class="ms-2">

                        <p class="text-muted mb-1 small">
                            Pendapatan
                        </p>

                        <h5 class="fw-bold mb-0 text-break">
                            Rp {{ number_format($pendapatan, 0, ',', '.') }}
                        </h5>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- SUDAH BAYAR --}}
    <div class="col-12 col-sm-6 col-xl-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body p-3">

                <div class="d-flex align-items-center">

                    <div class="rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 45px; height: 45px;">

                        <i class="bi bi-check-circle fs-5"></i>

                    </div>

                    <div class="ms-2">

                        <p class="text-muted mb-1 small">
                            Sudah Bayar
                        </p>

                        <h5 class="fw-bold mb-0">
                            {{ $sudahBayar }}
                        </h5>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- BELUM BAYAR --}}
    <div class="col-12 col-sm-6 col-xl-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body p-3">

                <div class="d-flex align-items-center">

                    <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 45px; height: 45px;">

                        <i class="bi bi-x-circle fs-5"></i>

                    </div>

                    <div class="ms-2">

                        <p class="text-muted mb-1 small">
                            Belum Bayar
                        </p>

                        <h5 class="fw-bold mb-0">
                            {{ $belumBayar }}
                        </h5>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

{{-- TOMBOL ALL --}}
<x-ui.form-actions class="justify-content-center mb-4">

    @if($showAll)

        <a href="{{ route('owner.dashboard') }}"
           class="btn btn-outline-primary px-4">
            ← Bulan Ini
        </a>

    @else

        <a href="{{ route('owner.dashboard', ['all' => 1]) }}"
           class="btn btn-outline-primary px-4">
            ALL
        </a>

    @endif

</x-ui.form-actions>

{{-- GRAFIK PENDAPATAN & TAGIHAN --}}
<div class="row g-4 mb-4">

    {{-- PENDAPATAN PER BULAN --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 p-sm-4">

                <h5 class="fw-bold mb-1">
                    Pendapatan per Bulan
                </h5>

                <p class="text-muted small mb-4">
                    Grafik pendapatan berdasarkan bulan
                </p>

                <p id="pendapatanChartSummary" class="visually-hidden">
                    Pendapatan per bulan tahun {{ $tahun }}:
                    @foreach(array_values($namaBulan) as $nama)
                        {{ $nama }}: Rp {{ number_format($pendapatanPerBulan[$loop->index], 0, ',', '.') }}{{ $loop->last ? '.' : ';' }}
                    @endforeach
                </p>
                @if(array_sum($pendapatanPerBulan) == 0)
                    <x-ui.empty-state title="Belum ada pendapatan pada periode ini" description="Ubah filter bulan untuk melihat periode lain." />
                @endif

                <div class="dashboard-chart">
                    <canvas
                        id="pendapatanChart"
                        role="img" aria-label="Pendapatan per bulan" aria-describedby="pendapatanChartSummary"
                        data-values='@json($pendapatanPerBulan)'>
                    </canvas>
                </div>

            </div>
        </div>
    </div>

    {{-- TUNGGAKAN CUSTOMER --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 p-sm-4">

                <h5 class="fw-bold mb-1">
                    Tunggakan Customer per Bulan
                </h5>

                <p class="text-muted small mb-4">
                    Jumlah customer yang belum membayar pada setiap bulan
                    tahun {{ $tahun }}
                </p>

                <p id="tunggakanChartSummary" class="visually-hidden">
                    Tunggakan customer tahun {{ $tahun }}:
                    @foreach(array_values($namaBulan) as $nama)
                        {{ $nama }}: {{ $tunggakanPerBulan[$loop->index] }} customer{{ $loop->last ? '.' : ';' }}
                    @endforeach
                </p>
                @if(array_sum($tunggakanPerBulan) === 0)
                    <p class="text-muted small">Tidak ada tunggakan customer pada periode yang ditampilkan.</p>
                @endif

                <div class="dashboard-chart">
                    <canvas 
                    id="tunggakanCustomerChart"
                    role="img" aria-label="Tunggakan customer per bulan" aria-describedby="tunggakanChartSummary"
                    data-values='@json($tunggakanPerBulan)'>
                </canvas>
                </div>

            </div>
        </div>
    </div>

</div>


    {{-- STATUS PEMBAYARAN & DISTRIBUSI PAKET --}}
<div class="row g-4 mb-4">

    {{-- STATUS PEMBAYARAN --}}
    <div class="col-lg-6">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body p-3 p-sm-4">

                <h5 class="fw-bold mb-1">
                    Status Pembayaran
                </h5>

                <p class="text-muted small mb-4">
                    Perbandingan pembayaran yang sudah dan belum dilakukan.
                </p>

                <p id="pembayaranChartSummary" class="visually-hidden">
                    Sudah bayar: {{ $sudahBayar }}. Belum bayar: {{ $belumBayar }}.
                </p>
                @if(array_sum($statusPembayaran) === 0)
                    <x-ui.empty-state title="Belum ada data pembayaran" description="Ubah filter bulan untuk melihat periode lain." />
                @endif

                <div class="dashboard-chart">
                    <canvas
                        id="pembayaranChart"
                        role="img" aria-label="Status pembayaran" aria-describedby="pembayaranChartSummary"
                        data-values='@json($statusPembayaran)'>
                    </canvas>
                </div>

            </div>

        </div>

    </div>


    {{-- DISTRIBUSI PAKET --}}
    <div class="col-lg-6">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body p-3 p-sm-4">

                <h5 class="fw-bold mb-1">
                    Distribusi Paket Pelanggan
                </h5>

                <p class="text-muted small mb-4">
                    Jumlah pelanggan berdasarkan paket Wi-Fi yang digunakan.
                </p>

                <p id="paketChartSummary" class="visually-hidden">
                    Distribusi paket pelanggan:
                    @foreach($distribusiPaket as $paket)
                        {{ $paket['nama'] }}: {{ $paket['jumlah'] }} pelanggan{{ $loop->last ? '.' : ';' }}
                    @endforeach
                </p>
                @if(array_sum(array_column($distribusiPaket, 'jumlah')) === 0)
                    <x-ui.empty-state title="Belum ada pelanggan pada paket" description="Distribusi ditampilkan setelah pelanggan terhubung ke paket Wi-Fi." />
                @endif

                <div class="dashboard-chart">
                    <canvas
                        id="paketChart"
                        role="img" aria-label="Distribusi paket pelanggan" aria-describedby="paketChartSummary"
                        data-values='@json($distribusiPaket)'>
                    </canvas>
                </div>

            </div>

        </div>

    </div>

</div>


<section id="tagihan-belum-dibuat" class="card border-0 shadow-sm mb-4" aria-labelledby="missing-bills-heading">
    <div class="card-body p-3 p-sm-4">
        <h2 id="missing-bills-heading" class="h5 fw-bold mb-1">Tagihan Belum Dibuat</h2>
        <p class="small text-muted mb-3">Periode berjalan: {{ $currentMonth }} {{ $tahun }}</p>

        @if($customersWithoutBills->total() === 0)
            <p class="mb-0">{{ array_sum(array_column($distribusiPaket, 'jumlah')) > 0
                ? 'Semua pelanggan yang punya paket sudah ditagih bulan ini.'
                : 'Belum ada pelanggan yang memiliki paket.' }}</p>
        @else
            <p class="mb-3"><strong>{{ $customersWithoutBills->total() }} pelanggan</strong> belum punya tagihan bulan ini.</p>
            @if($customersWithoutBills->isNotEmpty())
                <div class="row g-0 d-none d-sm-flex border-bottom pb-2 fw-semibold" aria-hidden="true">
                    <span class="col-sm-7">Pelanggan</span>
                    <span class="col-sm-5">Paket</span>
                </div>
                <ul class="list-unstyled mb-0" aria-label="Pelanggan tanpa tagihan bulan ini">
                    @foreach($customersWithoutBills as $customer)
                        <li class="row g-0 border-bottom py-2">
                            <div class="col-sm-7 pe-sm-3 text-break">
                                <a class="btn btn-link fw-semibold text-start text-break p-0 d-inline-flex align-items-center" href="{{ route('customers.show', $customer) }}">{{ $customer->nama }}</a>
                            </div>
                            <div class="col-sm-5 align-self-center small text-muted text-break">
                                <span class="visually-hidden">Paket: </span>{{ $customer->package?->nama_paket ?? 'Paket tidak tersedia' }}
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="mb-0">Tidak ada pelanggan di halaman ini. <a href="{{ $customersWithoutBills->url(1) }}">Kembali ke halaman pertama</a>.</p>
            @endif
            @if($customersWithoutBills->hasPages())
                <div class="payment-pagination mt-3">{{ $customersWithoutBills->links('pagination::bootstrap-5') }}</div>
            @endif
        @endif
    </div>
</section>

{{-- ========================================== --}}
{{-- SCRIPT FILTER --}}
{{-- ========================================== --}}

<script>

function toggleBulan() {

    const checkbox =
        document.getElementById('filter_active');

    const pilihanBulan =
        document.getElementById('pilihanBulan');


    if (checkbox.checked) {

        pilihanBulan.style.display = 'block';

    } else {

        pilihanBulan.style.display = 'none';

    }

}

</script>


@endsection
