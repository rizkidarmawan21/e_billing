@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                Edit Customer
            </h4>

            <p class="text-muted mb-0">
                Perbarui informasi pelanggan
            </p>
        </div>

        <a href="{{ route('customers.index') }}"
           class="btn btn-secondary">

            ← Kembali

        </a>

    </div>


    {{-- FORM --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-primary text-white">

            <h5 class="mb-0">
                Edit Data Customer
            </h5>

        </div>


        <div class="card-body">

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


            <form action="{{ route('customers.update', $customer) }}"
                  method="POST">

                @csrf
                @method('PUT')


                {{-- KODE PELANGGAN --}}
                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        Kode Pelanggan
                    </label>

                    <input
                        type="text"
                        name="kode_pelanggan"
                        class="form-control"
                        value="{{ old('kode_pelanggan', $customer->kode_pelanggan) }}"
                        required
                    >

                </div>


                {{-- NAMA --}}
                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        Nama Pelanggan
                    </label>

                    <input
                        type="text"
                        name="nama"
                        class="form-control"
                        value="{{ old('nama', $customer->nama) }}"
                        required
                    >

                </div>


                {{-- NO HP --}}
                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        No HP
                    </label>

                    <input
                        type="text"
                        name="no_hp"
                        class="form-control"
                        value="{{ old('no_hp', $customer->no_hp) }}"
                    >

                </div>


                {{-- ALAMAT --}}
                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        Alamat
                    </label>

                    <textarea
                        name="alamat"
                        class="form-control"
                        rows="3"
                    >{{ old('alamat', $customer->alamat) }}</textarea>

                </div>


                {{-- PAKET --}}
                <div class="mb-4">

                    <label class="form-label fw-semibold">
                        Paket Internet
                    </label>

                    <select
                        name="package_id"
                        class="form-select"
                    >

                        <option value="">
                            -- Pilih Paket --
                        </option>

                        @foreach ($packages as $package)

                            <option
                                value="{{ $package->id }}"
                                {{ old('package_id', $customer->package_id) == $package->id ? 'selected' : '' }}
                            >

                                {{ $package->nama_paket }}
                                - {{ $package->kecepatan }}
                                - Rp {{ number_format($package->harga, 0, ',', '.') }}

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- TOMBOL --}}
                <div class="d-flex gap-2">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        💾 Simpan Perubahan
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