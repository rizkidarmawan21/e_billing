@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                Edit Paket
            </h4>

            <p class="text-muted mb-0">
                Perbarui informasi paket internet
            </p>
        </div>

        <a href="{{ route('packages.index') }}"
           class="btn btn-secondary">

            ← Kembali

        </a>

    </div>


    {{-- FORM --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-primary text-white">

            <h5 class="mb-0">
                Edit Data Paket
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


            <form action="{{ route('packages.update', $package) }}"
                  method="POST">

                @csrf
                @method('PUT')


                {{-- NAMA PAKET --}}
                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        Nama Paket
                    </label>

                    <input
                        type="text"
                        name="nama_paket"
                        class="form-control"
                        value="{{ old('nama_paket', $package->nama_paket) }}"
                        required
                    >

                </div>


                {{-- KECEPATAN --}}
                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        Kecepatan
                    </label>

                    <input
                        type="text"
                        name="kecepatan"
                        class="form-control"
                        value="{{ old('kecepatan', $package->kecepatan) }}"
                        required
                    >

                </div>


                {{-- HARGA --}}
                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        Harga
                    </label>

                    <input
                        type="number"
                        name="harga"
                        class="form-control"
                        value="{{ old('harga', $package->harga) }}"
                        min="0"
                        required
                    >

                    <small class="text-muted">
                        Masukkan harga tanpa tanda titik atau koma.
                    </small>

                </div>


                {{-- DESKRIPSI --}}
                <div class="mb-4">

                    <label class="form-label fw-semibold">
                        Deskripsi
                    </label>

                    <textarea
                        name="deskripsi"
                        class="form-control"
                        rows="4"
                        placeholder="Deskripsi paket (opsional)"
                    >{{ old('deskripsi', $package->deskripsi) }}</textarea>

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
                        href="{{ route('packages.index') }}"
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