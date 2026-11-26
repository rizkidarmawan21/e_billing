@extends('owner.layout')

@section('header', 'Edit Admin')

@section('content')

<div class="card border-0 shadow-sm">

    <div class="card-body p-3 p-md-4">

        {{-- HEADER --}}
        <x-ui.page-header>
            <h5 class="fw-semibold mb-1">
                Edit Admin
            </h5>

            <p class="text-muted mb-0">
                Ubah informasi akun administrator.
            </p>
        </x-ui.page-header>

        {{-- FORM --}}
        <form method="POST"
              action="{{ route('owner.admin.update', $user->id) }}">

            @csrf
            @method('PUT')


            {{-- NAMA --}}
            <x-ui.field name="name" label="Nama" :required="true">
                <input type="text"
                       id="name"
                       name="name"
                       class="form-control"
                       value="{{ old('name', $user->name) }}"
                       required>

            </x-ui.field>


            {{-- EMAIL --}}
            <x-ui.field name="email" label="Email" :required="true">
                <input type="email"
                       id="email"
                       name="email"
                       class="form-control"
                       value="{{ old('email', $user->email) }}"
                       required>

            </x-ui.field>


            {{-- PASSWORD --}}
            <div class="mb-3">

                <label for="password"
                       class="form-label fw-semibold">
                    Password Baru
                    <span class="text-muted fw-normal">
                        (opsional)
                    </span>
                </label>

                <x-ui.password-input id="password" name="password" class="form-control"
                    placeholder="Kosongkan jika tidak diubah" autocomplete="new-password" />

                @error('password')
                    <div class="text-danger small mt-1">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            {{-- KONFIRMASI PASSWORD --}}
            <div class="mb-4">

                <label for="password_confirmation"
                       class="form-label fw-semibold">
                    Konfirmasi Password Baru
                </label>

                <x-ui.password-input id="password_confirmation" name="password_confirmation"
                    class="form-control" placeholder="Ulangi password baru" autocomplete="new-password" />

            </div>


            {{-- TOMBOL --}}
            <x-ui.form-actions class="justify-content-md-end">

                <a href="{{ route('owner.admin.index') }}"
                   class="btn btn-outline-secondary">
                    Batal
                </a>

                <button type="submit"
                        class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>
                    Simpan Perubahan
                </button>

            </x-ui.form-actions>

        </form>

    </div>

</div>


@endsection