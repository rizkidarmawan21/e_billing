@extends('owner.layout')

@section('header', 'Tambah Admin')

@section('content')

<div class="card border-0 shadow-sm">

    <div class="card-body p-3 p-md-4">

        <x-ui.page-header>
            <h5 class="fw-semibold mb-1">
                Tambah Admin
            </h5>

            <p class="text-muted mb-0">
                Tambahkan akun administrator baru ke dalam sistem.
            </p>
        </x-ui.page-header>

        <form method="POST" action="{{ route('owner.admin.store') }}">

            @csrf

            {{-- NAMA --}}
            <x-ui.field name="name" label="Nama">
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ old('name') }}"
                       class="form-control @error('name') is-invalid @enderror"
                       placeholder="Masukkan nama admin">

            </x-ui.field>


            {{-- EMAIL --}}
            <x-ui.field name="email" label="Email">
                <input type="email"
                       id="email"
                       name="email"
                       value="{{ old('email') }}"
                       class="form-control @error('email') is-invalid @enderror"
                       placeholder="Masukkan email admin">

            </x-ui.field>


            {{-- PASSWORD --}}
            <div class="mb-3">

                <label for="password" class="form-label fw-semibold">
                    Password
                </label>

                <x-ui.password-input name="password" id="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="Masukkan password" autocomplete="new-password" />

                @error('password')
                    <div class="text-danger small mt-1">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            {{-- KONFIRMASI PASSWORD --}}
            <div class="mb-4">

                <label for="password_confirmation" class="form-label fw-semibold">
                    Konfirmasi Password
                </label>

                <x-ui.password-input name="password_confirmation" id="password_confirmation"
                    class="form-control" placeholder="Ulangi password" autocomplete="new-password" />

            </div>


            {{-- BUTTON --}}
            <x-ui.form-actions>

                <a href="{{ route('owner.admin.index') }}"
                   class="btn btn-outline-secondary">

                    Kembali

                </a>

                <button type="submit"
                        class="btn btn-primary">

                    Simpan Admin

                </button>

            </x-ui.form-actions>

        </form>

    </div>

</div>


@endsection