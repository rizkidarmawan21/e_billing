@extends('owner.layout')

@section('header', 'Edit Admin')

@section('content')

<div class="card border-0 shadow-sm">

    <div class="card-body p-4">

        {{-- HEADER --}}
        <div class="mb-4">
            <h5 class="fw-semibold mb-1">
                Edit Admin
            </h5>

            <p class="text-muted mb-0">
                Ubah informasi akun administrator.
            </p>
        </div>


        {{-- FORM --}}
        <form method="POST"
              action="{{ route('owner.admin.update', $user->id) }}">

            @csrf
            @method('PUT')


            {{-- NAMA --}}
            <div class="mb-3">

                <label for="name"
                       class="form-label fw-semibold">
                    Nama
                </label>

                <input type="text"
                       id="name"
                       name="name"
                       class="form-control"
                       value="{{ old('name', $user->name) }}"
                       required>

                @error('name')
                    <div class="text-danger small mt-1">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            {{-- EMAIL --}}
            <div class="mb-3">

                <label for="email"
                       class="form-label fw-semibold">
                    Email
                </label>

                <input type="email"
                       id="email"
                       name="email"
                       class="form-control"
                       value="{{ old('email', $user->email) }}"
                       required>

                @error('email')
                    <div class="text-danger small mt-1">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            {{-- PASSWORD --}}
            <div class="mb-3">

                <label for="password"
                       class="form-label fw-semibold">
                    Password Baru
                    <span class="text-muted fw-normal">
                        (opsional)
                    </span>
                </label>

                <div class="input-group">

                    <input type="password"
                           id="password"
                           name="password"
                           class="form-control"
                           placeholder="Kosongkan jika tidak diubah">

                    <button type="button"
                            class="btn btn-outline-secondary"
                            onclick="togglePassword('password', 'passwordIcon')">

                        <i class="bi bi-eye"
                           id="passwordIcon"></i>

                    </button>

                </div>

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

                <div class="input-group">

                    <input type="password"
                           id="password_confirmation"
                           name="password_confirmation"
                           class="form-control"
                           placeholder="Ulangi password baru">

                    <button type="button"
                            class="btn btn-outline-secondary"
                            onclick="togglePassword('password_confirmation', 'confirmPasswordIcon')">

                        <i class="bi bi-eye"
                           id="confirmPasswordIcon"></i>

                    </button>

                </div>

            </div>


            {{-- TOMBOL --}}
            <div class="d-flex justify-content-end gap-2">

                <a href="{{ route('owner.admin.index') }}"
                   class="btn btn-secondary">
                    Batal
                </a>

                <button type="submit"
                        class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

</div>


{{-- JAVASCRIPT PASSWORD --}}
<script>

function togglePassword(inputId, iconId)
{
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);

    if (input.type === 'password') {

        input.type = 'text';

        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');

    } else {

        input.type = 'password';

        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');

    }
}

</script>

@endsection