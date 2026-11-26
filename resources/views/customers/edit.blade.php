@extends(Auth::user()->role === 'owner' ? 'owner.layout' : 'layouts.admin')

@section('content')

<div class="container-fluid">

    {{-- HEADER --}}
    <x-ui.page-header>

        <div>
            <h4 class="fw-bold mb-1">
                Edit Customer
            </h4>

            <p class="text-muted mb-0">
                Perbarui informasi pelanggan
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
                <x-ui.field name="kode_pelanggan" label="Kode Pelanggan" :required="true">
                    <input
                        type="text"
                        name="kode_pelanggan"
                        id="kode_pelanggan"
                        class="form-control"
                        value="{{ old('kode_pelanggan', $customer->kode_pelanggan) }}"
                        required
                    >

                </x-ui.field>

                {{-- NAMA --}}
                <x-ui.field name="nama" label="Nama Pelanggan" :required="true">
                    <input
                        type="text"
                        name="nama"
                        id="nama"
                        class="form-control"
                        value="{{ old('nama', $customer->nama) }}"
                        required
                    >

                </x-ui.field>

                {{-- NO HP --}}
                <x-ui.field name="no_hp" label="No HP">
                    <input
                        type="text"
                        name="no_hp"
                        id="no_hp"
                        class="form-control"
                        value="{{ old('no_hp', $customer->no_hp) }}"
                    >

                </x-ui.field>

                {{-- ALAMAT --}}
                <x-ui.field name="alamat" label="Alamat">
                    <textarea
                        name="alamat"
                        id="alamat"
                        class="form-control"
                        rows="3"
                    >{{ old('alamat', $customer->alamat) }}</textarea>

                </x-ui.field>

                {{-- PAKET --}}
                <div class="mb-4">

                    <label for="package_id" class="form-label fw-semibold">
                        Paket Internet
                    </label>

                    <select
                        name="package_id"
                        id="package_id"
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
                <x-ui.form-actions>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        💾 Simpan Perubahan
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