@extends(Auth::user()->role === 'owner' ? 'owner.layout' : 'layouts.admin')

@section('content')

<div class="container-fluid">

    {{-- HEADER --}}
    <x-ui.page-header>

        <div>
            <h4 class="fw-bold mb-1">
                Tambah Paket
            </h4>

            <p class="text-muted mb-0">
                Tambahkan paket internet baru
            </p>
        </div>

        <x-slot:actions>
        <a href="{{ route('packages.index') }}"
           class="btn btn-outline-secondary">

            ← Kembali

        </a>

        </x-slot:actions>
    </x-ui.page-header>

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
                <x-ui.field name="nama_paket" label="Nama Paket" :required="true">
                    <input
                        type="text"
                        name="nama_paket"
                        id="nama_paket"
                        class="form-control"
                        value="{{ old('nama_paket') }}"
                        placeholder="Contoh: Basic"
                        required
                    >

                </x-ui.field>

                {{-- KECEPATAN --}}
                <x-ui.field name="kecepatan" label="Kecepatan" :required="true">
                    <input
                        type="text"
                        name="kecepatan"
                        id="kecepatan"
                        class="form-control"
                        value="{{ old('kecepatan') }}"
                        placeholder="Contoh: 100 Mbps"
                        required
                    >

                </x-ui.field>

                {{-- HARGA --}}
                <x-ui.field name="harga" label="Harga" :required="true" help="Masukkan harga tanpa tanda titik atau koma.">
                    <input
                        type="number"
                        name="harga"
                        id="harga"
                        aria-describedby="harga-help{{ $errors->has('harga') ? ' harga-error' : '' }}"
                        class="form-control"
                        value="{{ old('harga') }}"
                        placeholder="Contoh: 120000"
                        min="0"
                        required
                    >

                </x-ui.field>

                {{-- DESKRIPSI --}}
                <x-ui.field name="deskripsi" label="Deskripsi" class="mb-4">
                    <textarea
                        name="deskripsi"
                        id="deskripsi"
                        class="form-control"
                        rows="4"
                        placeholder="Deskripsi paket (opsional)"
                    >{{ old('deskripsi') }}</textarea>

                </x-ui.field>

                {{-- TOMBOL --}}
                <x-ui.form-actions>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        💾 Simpan Paket
                    </button>


                    <a
                        href="{{ route('packages.index') }}"
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