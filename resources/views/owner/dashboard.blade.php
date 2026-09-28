@extends('owner.layout')

@section('content')

<div class="d-flex justify-content-between align-items-start mb-4">

<div>
    <h2 class="fw-bold mb-1">
        Dashboard Owner
    </h2>

    <p class="text-muted mb-0">
        Ringkasan informasi sistem e-Billing
    </p>
</div>


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
        class="dropdown-menu dropdown-menu-end p-3 shadow"
        style="width: 360px;"
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


                <div class="d-flex justify-content-end gap-2">

                    <a
                        href="{{ route('owner.dashboard') }}"
                        class="btn btn-sm btn-light"
                    >
                        Reset
                    </a>

                    <button
                        type="submit"
                        class="btn btn-sm btn-primary"
                    >
                        Terapkan
                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

</div>

{{-- ========================================== --}}
{{-- CARD STATISTIK --}}
{{-- ========================================== --}}

<div class="row g-3 mb-4">

    {{-- TOTAL USER --}}
    <div class="col">

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
    <div class="col">

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

                        <h5 class="fw-bold mb-0">
                            Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                        </h5>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- PENDAPATAN --}}
    <div class="col">

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

                        <h5 class="fw-bold mb-0">
                            Rp {{ number_format($pendapatan, 0, ',', '.') }}
                        </h5>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- SUDAH BAYAR --}}
    <div class="col">

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
    <div class="col">

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

{{-- GRAFIK PENDAPATAN & TAGIHAN --}}
<div class="row g-4 mb-4">

    {{-- PENDAPATAN PER BULAN --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">

            <div class="card-body p-4">

                <h5 class="fw-bold mb-1">
                    Pendapatan per Bulan
                </h5>

                <p class="text-muted small mb-4">
                    Grafik pendapatan berdasarkan bulan
                </p>

                <div style="height: 300px;">
                    <canvas
                        id="pendapatanChart"
                        data-values='@json($pendapatanPerBulan)'>
                    </canvas>
                </div>

            </div>

        </div>
    </div>


    {{-- TAGIHAN VS PENDAPATAN --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">

            <div class="card-body p-4">

                <h5 class="fw-bold mb-1">
                    Tagihan vs Pendapatan
                </h5>

                <p class="text-muted small mb-4">
                    Perbandingan total tagihan dengan pendapatan
                </p>

                <div style="height: 300px;">
                    <canvas
                        id="tagihanPendapatanChart"
                        data-values='@json($tagihanVsPendapatan)'>
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

            <div class="card-body p-4">

                <h5 class="fw-bold mb-1">
                    Status Pembayaran
                </h5>

                <p class="text-muted small mb-4">
                    Perbandingan pembayaran yang sudah dan belum dilakukan.
                </p>

                <div style="height: 300px;">
                    <canvas
                        id="pembayaranChart"
                        data-values='@json($statusPembayaran)'>
                    </canvas>
                </div>

            </div>

        </div>

    </div>


    {{-- DISTRIBUSI PAKET --}}
    <div class="col-lg-6">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body p-4">

                <h5 class="fw-bold mb-1">
                    Distribusi Paket Pelanggan
                </h5>

                <p class="text-muted small mb-4">
                    Jumlah pelanggan berdasarkan paket Wi-Fi yang digunakan.
                </p>

                <div style="height: 300px;">
                    <canvas
                        id="paketChart"
                        data-values='@json($distribusiPaket)'>
                    </canvas>
                </div>

            </div>

        </div>

    </div>

</div>


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
