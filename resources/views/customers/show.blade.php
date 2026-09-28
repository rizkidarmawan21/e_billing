@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                Profil Pelanggan
            </h4>

            <p class="text-muted mb-0">
                Detail informasi pelanggan
            </p>
        </div>

        <a href="{{ route('customers.index') }}"
           class="btn btn-secondary">

            ← Kembali

        </a>

    </div>


    {{-- PROFIL --}}
    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <div class="row g-4">

                {{-- INFORMASI PELANGGAN --}}
                <div class="col-md-8">

                    <h5 class="fw-bold mb-4">
                        Informasi Pelanggan
                    </h5>


                    <div class="row mb-3">

                        <div class="col-sm-4 text-muted">
                            Kode Pelanggan
                        </div>

                        <div class="col-sm-8 fw-semibold">
                            {{ $customer->kode_pelanggan }}
                        </div>

                    </div>


                    <div class="row mb-3">

                        <div class="col-sm-4 text-muted">
                            Nama
                        </div>

                        <div class="col-sm-8 fw-semibold">
                            {{ $customer->nama }}
                        </div>

                    </div>


                    <div class="row mb-3">

                        <div class="col-sm-4 text-muted">
                            No HP
                        </div>

                        <div class="col-sm-8">
                            {{ $customer->no_hp ?? '-' }}
                        </div>

                    </div>


                    <div class="row mb-3">

                        <div class="col-sm-4 text-muted">
                            Alamat
                        </div>

                        <div class="col-sm-8">
                            {{ $customer->alamat ?? '-' }}
                        </div>

                    </div>

                </div>


                {{-- PAKET INTERNET --}}
                <div class="col-md-4">

                    <div class="card bg-light border-0">

                        <div class="card-body">

                            <h5 class="fw-bold mb-3">
                                📦 Paket Internet
                            </h5>

                            @if ($customer->package)

                                <h4 class="fw-bold">
                                    {{ $customer->package->nama_paket }}
                                </h4>

                                <p class="text-muted mb-2">
                                    {{ $customer->package->kecepatan }}
                                </p>

                                <h5 class="text-primary fw-bold">
                                    Rp {{ number_format($customer->package->harga, 0, ',', '.') }}
                                </h5>

                                {{-- STATUS PEMBAYARAN --}}
                                @php
                                  $payment = $customer->payments->sortByDesc('created_at')->first();
                                @endphp

                                <div class="mt-3">

                                     @if ($payment && $payment->status === 'lunas')

                                       <span class="badge bg-success px-3 py-2">
                                           ✓ Lunas
                                        </span>

                                        @if ($payment->tanggal_bayar)
                                             <div class="small text-muted mt-2">
                                                 Dibayar pada:
                                                 {{ $payment->tanggal_bayar->format('d-m-Y') }}
                                             </div>
                                          @endif

                                      @else

                                         <span class="badge bg-danger px-3 py-2">
                                            ✕ Belum Lunas
                                         </span>

                                      @endif

                                </div>
                                @if ($customer->package->deskripsi)

                                    <p class="small text-muted mt-3 mb-0">
                                        {{ $customer->package->deskripsi }}
                                    </p>

                                @endif

                            @else

                                <p class="text-muted mb-0">
                                    Pelanggan belum memiliki paket.
                                </p>

                            @endif

                        </div>

                    </div>

                </div>

            </div>


            {{-- TOMBOL --}}
            <div class="border-top mt-4 pt-4">

                <a href="{{ route('customers.edit', $customer) }}"
                   class="btn btn-warning me-2">

                    ✏️ Edit Data

                </a>


                <form action="{{ route('customers.destroy', $customer) }}"
                      method="POST"
                      class="d-inline"
                      onsubmit="return confirm('Yakin ingin menghapus pelanggan ini?')">

                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            class="btn btn-danger">

                        🗑️ Hapus Pelanggan

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection