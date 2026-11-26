@extends('owner.layout')

@section('content')

<style>
    .page-title {
        font-weight: 700;
        color: #1f2937;
    }

    .page-subtitle {
        color: #6b7280;
        font-size: 14px;
    }

    .soft-card {
        border: 0;
        border-radius: 16px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
    }

    .filter-card {
        background: #ffffff;
        border-left: 4px solid #0d6efd;
    }

    .summary-card {
        border: 0;
        border-radius: 16px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
        transition: 0.2s ease;
    }

    .summary-card:hover {
        transform: translateY(-2px);
    }

    .summary-icon {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .icon-blue {
        background: #e8f1ff;
        color: #0d6efd;
    }

    .icon-green {
        background: #e9f8ef;
        color: #198754;
    }

    .icon-orange {
        background: #fff4df;
        color: #f59e0b;
    }

    .icon-red {
        background: #fdecec;
        color: #dc3545;
    }

    .filter-label {
        font-size: 13px;
        font-weight: 600;
        color: #495057;
        margin-bottom: 6px;
    }

    .form-control,
    .form-select,
    .dropdown-toggle {
        border-radius: 10px;
    }

    .table-card {
        border: 0;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
    }

    .table thead th {
        background: #f4f7fb;
        color: #495057;
        font-size: 13px;
        font-weight: 600;
        border-bottom: 1px solid #e9ecef;
        white-space: nowrap;
    }

    .table tbody td {
        vertical-align: middle;
        font-size: 14px;
    }


    .pagination {
        margin-bottom: 0;
    }

    .pagination .page-link {
        border-radius: 8px;
        margin: 0 2px;
        border: 0;
        color: #0d6efd;
    }

    .pagination .active .page-link {
        background: #0d6efd;
        color: white;
    }
</style>

<div class="container-fluid px-0 py-3 py-sm-4">

    {{-- HEADER --}}
    <x-ui.page-header title="Laporan" description="Ringkasan dan detail data tagihan serta pembayaran pelanggan." />


    {{-- FILTER --}}
    <div class="card soft-card filter-card mb-4">
        <div class="card-body p-3 p-sm-4">

            <div class="d-flex align-items-center mb-3">
                <div class="summary-icon icon-blue me-3">
                    <i class="bi bi-funnel"></i>
                </div>

                <div>
                    <h6 class="mb-1 fw-bold">Filter Laporan</h6>
                    <small class="text-muted">
                        Gunakan filter untuk menampilkan data tertentu.
                    </small>
                </div>
            </div>

            <form method="GET" action="{{ route('owner.laporan') }}">

                {{-- SEARCH --}}
                <div class="mb-3">
                    <label for="laporanSearch" class="filter-label">
                        Cari Pelanggan
                    </label>

                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search text-muted"></i>
                        </span>

                        <input
                            type="text"
                            name="search"
                            id="laporanSearch"
                            class="form-control"
                            placeholder="Cari berdasarkan nama atau kode pelanggan..."
                            value="{{ $search ?? '' }}"
                        >
                    </div>
                </div>


                {{-- FILTER BARIS KEDUA --}}
                <div class="row g-3 align-items-end">

                    {{-- PERIODE --}}
                    <div class="col-12 col-md-6 col-xl-4">

                        <label for="laporanPeriode" class="filter-label">
                            Periode
                        </label>

                        <div class="dropdown">

                            <button
                                class="btn btn-outline-secondary dropdown-toggle w-100 text-start text-wrap"
                                type="button"
                                id="laporanPeriode"
                                data-bs-toggle="dropdown"
                                data-bs-auto-close="outside"
                                aria-expanded="false"
                            >
                                <i class="bi bi-calendar3 me-2"></i>

                                @if(!empty($months))
                                    {{ implode(', ', $months) }}
                                @else
                                    Semua Periode
                                @endif
                            </button>

                            <div class="dropdown-menu filter-menu p-3 w-100"
                                 style="max-height: 280px; overflow-y: auto;">

                                @foreach($namaBulan as $bulan)

                                    <div class="form-check mb-2">

                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="months[]"
                                            value="{{ $bulan }}"
                                            id="bulan_{{ $loop->index }}"
                                            {{ in_array($bulan, $months ?? []) ? 'checked' : '' }}
                                        >

                                        <label
                                            class="form-check-label"
                                            for="bulan_{{ $loop->index }}"
                                        >
                                            {{ $bulan }}
                                        </label>

                                    </div>

                                @endforeach

                            </div>
                        </div>

                    </div>


                    {{-- TAHUN --}}
                    <div class="col-12 col-md-6 col-xl-2">

                        <label for="laporanTahun" class="filter-label">
                            Tahun
                        </label>

                        <select name="tahun" id="laporanTahun" class="form-select">

                            <option value="">
                                Semua Tahun
                            </option>

                            @foreach($tahunList as $item)

                                <option
                                    value="{{ $item }}"
                                    {{ (string)($tahun ?? '') === (string)$item ? 'selected' : '' }}
                                >
                                    {{ $item }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- STATUS --}}
                    <div class="col-12 col-md-6 col-xl-3">

                        <label for="laporanStatus" class="filter-label">
                            Status Pembayaran
                        </label>

                        <select name="status" id="laporanStatus" class="form-select">

                            <option value="">
                                Semua Status
                            </option>

                            <option
                                value="lunas"
                                {{ ($status ?? '') === 'lunas' ? 'selected' : '' }}
                            >
                                Lunas
                            </option>

                            <option
                                value="belum_bayar"
                                {{ ($status ?? '') === 'belum_bayar' ? 'selected' : '' }}
                            >
                                Belum Bayar
                            </option>

                        </select>

                    </div>


                    {{-- BUTTON --}}
                    <div class="col-12 col-md-6 col-xl-3">

                        <x-ui.form-actions>

                            <button
                                type="submit"
                                class="btn btn-primary flex-fill"
                            >
                                <i class="bi bi-funnel me-1"></i>
                                Terapkan
                            </button>

                            <a
                                href="{{ route('owner.laporan') }}"
                                class="btn btn-light border"
                                title="Reset Filter"
                            >
                                <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                                Reset
                            </a>

                        </x-ui.form-actions>

                    </div>

                </div>

            </form>

        </div>
    </div>


    {{-- SUMMARY --}}
    <div class="row g-3 mb-4">

        {{-- TOTAL TAGIHAN --}}
        <div class="col-12 col-sm-6 col-xl-4">

            <div class="card summary-card h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <small class="text-muted">
                                Total Tagihan
                            </small>

                            <h5 class="fw-bold mb-0 mt-1 text-break">
                                Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                            </h5>
                        </div>

                        <div class="summary-icon icon-blue">
                            <i class="bi bi-receipt"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- PENDAPATAN --}}
        <div class="col-12 col-sm-6 col-xl-4">

            <div class="card summary-card h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <small class="text-muted">
                                Pendapatan
                            </small>

                            <h5 class="fw-bold mb-0 mt-1 text-success text-break">
                                Rp {{ number_format($pendapatan, 0, ',', '.') }}
                            </h5>
                        </div>

                        <div class="summary-icon icon-green">
                            <i class="bi bi-cash-stack"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- SUDAH BAYAR --}}
        <div class="col-12 col-sm-6 col-xl-4">

            <div class="card summary-card h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <small class="text-muted">
                                Sudah Bayar
                            </small>

                            <h5 class="fw-bold mb-0 mt-1">
                                {{ $sudahBayar }} pelanggan
                            </h5>
                        </div>

                        <div class="summary-icon icon-green">
                            <i class="bi bi-check-circle"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- BELUM BAYAR --}}
        <div class="col-12 col-sm-6 col-xl-4">

            <div class="card summary-card h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <small class="text-muted">
                                Belum Bayar
                            </small>

                            <h5 class="fw-bold mb-0 mt-1">
                                {{ $belumBayar }} pelanggan
                            </h5>
                        </div>

                        <div class="summary-icon icon-orange">
                            <i class="bi bi-clock-history"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- PIUTANG --}}
        <div class="col-12 col-sm-6 col-xl-4">

            <div class="card summary-card h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <small class="text-muted">
                                Piutang
                            </small>

                            <h5 class="fw-bold mb-0 mt-1 text-danger text-break">
                                Rp {{ number_format($piutang, 0, ',', '.') }}
                            </h5>
                        </div>

                        <div class="summary-icon icon-red">
                            <i class="bi bi-exclamation-circle"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- TABLE --}}
    <div class="card table-card">

        <div class="card-body p-0">

            <div class="p-3 p-sm-4 pb-sm-3">

                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">

                    <div>
                        <h6 class="fw-bold mb-1">
                            Detail Laporan Pembayaran
                        </h6>

                        <small class="text-muted">
                            Menampilkan data transaksi pembayaran pelanggan.
                        </small>
                    </div>

                    <span class="badge bg-light text-dark border">
                        {{ $payments->total() }} Data
                    </span>

                </div>

            </div>


            <div class="table-responsive" tabindex="0" role="region" aria-label="Laporan tagihan">

                <x-ui.table>

                    <thead>

                        <tr>

                            <th class="ps-4 text-center" scope="col">No.</th>
                            <th>Kode Pelanggan</th>
                            <th>Nama</th>
                            <th>Paket</th>
                            <th>Periode</th>
                            <th>Tagihan</th>
                            <th>Status</th>
                            <th>Tanggal Bayar</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($payments as $payment)

                            <tr>

                                <td data-label="No." class="ps-4">
                                    {{ $payments->firstItem() + $loop->index }}
                                </td>

                                <td data-label="Kode Pelanggan">
                                    <span class="fw-semibold">
                                        {{ $payment->customer->kode_pelanggan ?? '-' }}
                                    </span>
                                </td>

                                <td data-label="Nama">
                                    {{ $payment->customer->nama ?? '-' }}
                                </td>

                                <td data-label="Paket">
                                    {{ $payment->package->nama_paket ?? '-' }}
                                </td>

                                <td data-label="Periode">
                                    {{ $payment->periode }} {{ $payment->tahun }}
                                </td>

                                <td data-label="Tagihan">
                                    Rp {{ number_format($payment->jumlah_tagihan, 0, ',', '.') }}
                                </td>

                                <td data-label="Status">

                                    <x-ui.payment-status :status="$payment->status" />

                                </td>

                                <td data-label="Tanggal Bayar">
                                    {{ $payment->tanggal_bayar
                                        ? $payment->tanggal_bayar->format('d/m/Y')
                                        : '-' }}
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="8" class="text-center py-5">

                                    <x-ui.empty-state title="Belum ada data laporan" description="Tidak ada pembayaran yang sesuai dengan filter. Ubah atau reset filter untuk melihat data lain." />

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </x-ui.table>

            </div>


            {{-- PAGINATION --}}
            @if($payments->hasPages())

                <div class="p-3 p-sm-4">

                    {{ $payments->links() }}

                </div>

            @endif

        </div>

    </div>

</div>

@endsection