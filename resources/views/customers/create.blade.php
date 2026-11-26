@extends(Auth::user()->role === 'owner' ? 'owner.layout' : 'layouts.admin')

@section('content')

<div class="container-fluid">

    {{-- HEADER --}}
    <x-ui.page-header>

        <div>
            <h4 class="fw-bold mb-1">
                Tambah Customer
            </h4>

            <p class="text-muted mb-0">
                Tambahkan data pelanggan baru ke dalam sistem.
            </p>
        </div>

        <x-slot:actions>
        <a href="{{ route('customers.index') }}"
           class="btn btn-outline-secondary">

            ← Kembali

        </a>

        </x-slot:actions>
    </x-ui.page-header>

    {{-- FORM --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-primary text-white py-3">

            <h5 class="mb-0">
                Data Customer
            </h5>

        </div>


        <div class="card-body p-3 p-md-4">

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
<x-ui.field name="kode_pelanggan" label="Kode Pelanggan" :required="true">
    <input
        type="text"
        id="kode_pelanggan"
        name="kode_pelanggan"
        class="form-control"
        placeholder="Contoh: CUST001"
        value="{{ old('kode_pelanggan') }}"
        required
    >

</x-ui.field>

                {{-- NAMA --}}
                <x-ui.field name="nama" label="Nama Customer" :required="true">
                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        class="form-control"
                        placeholder="Masukkan nama customer"
                        value="{{ old('nama') }}"
                        required
                    >

                </x-ui.field>

                {{-- NOMOR HP --}}
                <x-ui.field name="no_hp" label="Nomor HP">
                    <input
                        type="text"
                        id="no_hp"
                        name="no_hp"
                        class="form-control"
                        placeholder="Contoh: 081234567890"
                        value="{{ old('no_hp') }}"
                    >

                </x-ui.field>

                {{-- ALAMAT --}}
                <x-ui.field name="alamat" label="Alamat">
                    <textarea
                        id="alamat"
                        name="alamat"
                        class="form-control"
                        rows="3"
                        placeholder="Masukkan alamat customer"
                    >{{ old('alamat') }}</textarea>

                </x-ui.field>

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
                <x-ui.form-actions>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >

                        💾 Simpan Customer

                    </button>


                    <a
                        href="{{ route('customers.index') }}"
                        class="btn btn-outline-secondary"
                    >

                        Batal

                    </a>

                </x-ui.form-actions>

            </form>

        </div>

    </div>

</div>

@endsection