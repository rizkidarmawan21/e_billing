@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                Tambah Customer
            </h4>

            <p class="text-muted mb-0">
                Tambahkan data pelanggan baru ke dalam sistem.
            </p>
        </div>

        <a href="{{ route('customers.index') }}"
           class="btn btn-secondary">

            ← Kembali

        </a>

    </div>


    {{-- FORM --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-primary text-white py-3">

            <h5 class="mb-0">
                Data Customer
            </h5>

        </div>


        <div class="card-body p-4">

            {{-- ERROR VALIDASI --}}
            @if ($errors->any())

                <div class="alert alert-danger">

                    <strong>Terjadi kesalahan:</strong>

                    <ul class="mb-0 mt-2">

                        @foreach ($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif


            <form method="POST"
                  action="{{ route('customers.store') }}">

                @csrf


                {{-- KODE PELANGGAN --}}
                <div class="mb-3">

                    <label for="kode_pelanggan"
                           class="form-label fw-semibold">

                        Kode Pelanggan

                    </label>

                    <input
                        type="text"
                        id="kode_pelanggan"
                        name="kode_pelanggan"
                        class="form-control"
                        placeholder="Contoh: CUST001"
                        value="{{ old('kode_pelanggan') }}"
                        required
                    >

                </div>


                {{-- NAMA --}}
                <div class="mb-3">

                    <label for="nama"
                           class="form-label fw-semibold">

                        Nama Customer

                    </label>

                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        class="form-control"
                        placeholder="Masukkan nama customer"
                        value="{{ old('nama') }}"
                        required
                    >

                </div>


                {{-- NOMOR HP --}}
                <div class="mb-3">

                    <label for="no_hp"
                           class="form-label fw-semibold">

                        Nomor HP

                    </label>

                    <input
                        type="text"
                        id="no_hp"
                        name="no_hp"
                        class="form-control"
                        placeholder="Contoh: 081234567890"
                        value="{{ old('no_hp') }}"
                    >

                </div>


                {{-- ALAMAT --}}
                <div class="mb-3">

                    <label for="alamat"
                           class="form-label fw-semibold">

                        Alamat

                    </label>

                    <textarea
                        id="alamat"
                        name="alamat"
                        class="form-control"
                        rows="3"
                        placeholder="Masukkan alamat customer"
                    >{{ old('alamat') }}</textarea>

                </div>


                {{-- PAKET --}}
                <div class="mb-4">

                    <label for="package_id"
                           class="form-label fw-semibold">

                        Paket Internet

                    </label>

                    <select
                        id="package_id"
                        name="package_id"
                        class="form-select"
                    >

                        <option value="">
                            -- Pilih Paket --
                        </option>

                        @foreach ($packages as $package)

                            <option
                                value="{{ $package->id }}"
                                {{ old('package_id') == $package->id ? 'selected' : '' }}
                            >

                                {{ $package->nama_paket }}
                                -
                                {{ $package->kecepatan }}
                                -
                                Rp {{ number_format($package->harga, 0, ',', '.') }}

                            </option>

                        @endforeach

                    </select>

                    <small class="text-muted">

                        Pilih paket internet yang digunakan customer.

                    </small>

                </div>


                {{-- TOMBOL --}}
                <div class="d-flex gap-2">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >

                        💾 Simpan Customer

                    </button>


                    <a
                        href="{{ route('customers.index') }}"
                        class="btn btn-secondary"
                    >

                        Batal

                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection