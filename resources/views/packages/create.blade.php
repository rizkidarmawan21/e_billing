@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                Tambah Paket
            </h4>

            <p class="text-muted mb-0">
                Tambahkan paket internet baru
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
                Data Paket Internet
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


            <form action="{{ route('packages.store') }}"
                  method="POST">

                @csrf


                {{-- NAMA PAKET --}}
                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        Nama Paket
                    </label>

                    <input
                        type="text"
                        name="nama_paket"
                        class="form-control"
                        value="{{ old('nama_paket') }}"
                        placeholder="Contoh: Basic"
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
                        value="{{ old('kecepatan') }}"
                        placeholder="Contoh: 100 Mbps"
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
                        value="{{ old('harga') }}"
                        placeholder="Contoh: 120000"
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
                    >{{ old('deskripsi') }}</textarea>

                </div>


                {{-- TOMBOL --}}
                <div class="d-flex gap-2">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        💾 Simpan Paket
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