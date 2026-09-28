@extends('layouts.admin')

@section('header', 'Detail Payment')

@section('content')

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="fw-bold mb-1">
                Detail Customer
            </h4>

            <p class="text-muted mb-0">
                Informasi customer dan riwayat tagihan pembayaran.
            </p>

        </div>


        <a href="{{ route('payments.index') }}"
           class="btn btn-secondary">

            ← Kembali

        </a>

    </div>


    <div class="row g-4">

        {{-- INFORMASI CUSTOMER --}}
        <div class="col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-header bg-primary text-white">

                    <h5 class="mb-0">
                        👤 Informasi Customer
                    </h5>

                </div>


                <div class="card-body">

                    <div class="mb-3">

                        <small class="text-muted">
                            Kode Customer
                        </small>

                        <div class="fw-semibold">
                            {{ $customer->kode_pelanggan ?? '-' }}
                        </div>

                    </div>


                    <div class="mb-3">

                        <small class="text-muted">
                            Nama Customer
                        </small>

                        <div class="fw-semibold">
                            {{ $customer->nama ?? '-' }}
                        </div>

                    </div>


                    <div class="mb-3">

                        <small class="text-muted">
                            No. HP
                        </small>

                        <div>
                            {{ $customer->no_hp ?? '-' }}
                        </div>

                    </div>


                    <div>

                        <small class="text-muted">
                            Alamat
                        </small>

                        <div>
                            {{ $customer->alamat ?? '-' }}
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- INFORMASI PAKET --}}
        <div class="col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-header bg-info text-white">

                    <h5 class="mb-0">
                        📦 Informasi Paket
                    </h5>

                </div>


                <div class="card-body">

                    <div class="mb-3">

                        <small class="text-muted">
                            Nama Paket
                        </small>

                        <div class="fw-semibold">
                            {{ $customer->package->nama_paket ?? '-' }}
                        </div>

                    </div>


                    <div class="mb-3">

                        <small class="text-muted">
                            Kecepatan
                        </small>

                        <div>
                            {{ $customer->package->kecepatan ?? '-' }}
                        </div>

                    </div>


                    <div>

                        <small class="text-muted">
                            Harga Paket Saat Ini
                        </small>

                        <div class="fw-bold text-primary fs-5">

                            Rp
                            {{ number_format(
                                $customer->package->harga ?? 0,
                                0,
                                ',',
                                '.'
                            ) }}

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- RIWAYAT TAGIHAN --}}
        <div class="col-12">

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-light">

                    <h5 class="fw-bold mb-1">
                        💳 Riwayat Tagihan
                    </h5>

                    <small class="text-muted">
                        Seluruh tagihan customer berdasarkan periode.
                    </small>

                </div>


                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-hover align-middle mb-0">

                            <thead class="table-light">

                                <tr>

                                    <th class="px-4 py-3">
                                        No
                                    </th>

                                    <th>
                                        Periode
                                    </th>

                                    <th>
                                        Paket
                                    </th>

                                    <th>
                                        Jumlah Tagihan
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                    <th>
                                        Tanggal Bayar
                                    </th>

                                    <th class="text-center">
                                        Aksi
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @forelse ($customer->payments as $history)

                                    <tr>

                                        {{-- NO --}}
                                        <td class="px-4">

                                            {{ $loop->iteration }}

                                        </td>


                                        {{-- PERIODE --}}
                                        <td>

                                            <strong>
                                                {{ $history->periode }}
                                                {{ $history->tahun }}
                                            </strong>

                                        </td>


                                        {{-- PAKET --}}
                                        <td>

                                            {{ $history->package->nama_paket ?? '-' }}

                                            @if ($history->package)

                                                <div class="small text-muted">

                                                    {{ $history->package->kecepatan }}

                                                </div>

                                            @endif

                                        </td>


                                        {{-- JUMLAH --}}
                                        <td>

                                            <strong class="text-primary">

                                                Rp
                                                {{ number_format(
                                                    $history->jumlah_tagihan,
                                                    0,
                                                    ',',
                                                    '.'
                                                ) }}

                                            </strong>

                                        </td>


                                        {{-- STATUS --}}
                                        <td>

                                            @if ($history->status === 'lunas')

                                                <span class="badge bg-success px-3 py-2">

                                                    ✓ Lunas

                                                </span>

                                            @else

                                                <span class="badge bg-danger px-3 py-2">

                                                    ✕ Belum Lunas

                                                </span>

                                            @endif

                                        </td>


                                        {{-- TANGGAL BAYAR --}}
                                        <td>

                                            @if ($history->tanggal_bayar)

                                                {{ $history->tanggal_bayar->format('d-m-Y') }}

                                            @else

                                                <span class="text-muted">
                                                    -
                                                </span>

                                            @endif

                                        </td>


                                        {{-- AKSI --}}
                                       <td class="text-center">
    <a href="{{ route('payments.edit', $history) }}"
       class="btn btn-sm btn-warning me-1">
        Edit
    </a>

    <form action="{{ route('payments.destroy', $history) }}"
          method="POST"
          class="d-inline"
          onsubmit="return confirm('Yakin ingin menghapus riwayat pembayaran {{ $history->periode }} {{ $history->tahun }}?');">
        @csrf
        @method('DELETE')

        <button type="submit" class="btn btn-sm btn-danger">
            Hapus
        </button>
    </form>
</td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td colspan="7"
                                            class="text-center py-5">

                                            <div class="text-muted">

                                                <div class="fs-1 mb-3">
                                                    💳
                                                </div>

                                                <h5>
                                                    Belum ada riwayat tagihan
                                                </h5>

                                                <p class="mb-0">
                                                    Customer belum memiliki data tagihan.
                                                </p>

                                            </div>

                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>


        {{-- TOMBOL --}}
        <div class="col-12">

            <div class="d-flex gap-2">

                <a href="{{ route('payments.edit', $payment) }}"
                   class="btn btn-warning">

                    ✏️ Edit Payment

                </a>


                <form action="{{ route('payments.destroy', $payment) }}"
                      method="POST"
                      class="d-inline"
                      onsubmit="return confirm('Yakin ingin menghapus payment ini?');">

                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            class="btn btn-danger">

                        🗑️ Hapus Payment

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection