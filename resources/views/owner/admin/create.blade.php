@extends('owner.layout')

@section('header', 'Tambah Admin')

@section('content')

<div class="card border-0 shadow-sm">

    <div class="card-body p-4">

        <div class="mb-4">
            <h5 class="fw-semibold mb-1">
                Tambah Admin
            </h5>

            <p class="text-muted mb-0">
                Tambahkan akun administrator baru ke dalam sistem.
            </p>
        </div>


        <form method="POST" action="{{ route('owner.admin.store') }}">

            @csrf

            {{-- NAMA --}}
            <div class="mb-3">

                <label class="form-label fw-semibold">
                    Nama
                </label>

                <input type="text"
                       name="name"
                       value="{{ old('name') }}"
                       class="form-control @error('name') is-invalid @enderror"
                       placeholder="Masukkan nama admin">

                @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            {{-- EMAIL --}}
            <div class="mb-3">

                <label class="form-label fw-semibold">
                    Email
                </label>

                <input type="email"
                       name="email"
                       value="{{ old('email') }}"
                       class="form-control @error('email') is-invalid @enderror"
                       placeholder="Masukkan email admin">

                @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            {{-- PASSWORD --}}
            <div class="mb-3">

                <label class="form-label fw-semibold">
                    Password
                </label>

                <div class="input-group">

                    <input type="password"
                           name="password"
                           id="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Masukkan password">

                    <button type="button"
                         class="btn btn-outline-secondary"
                         onclick="togglePassword('password', this)"
                          title="Tampilkan password">

                         <i class="bi bi-eye"></i>

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

                <label class="form-label fw-semibold">
                    Konfirmasi Password
                </label>

                <div class="input-group">

                    <input type="password"
                           name="password_confirmation"
                           id="password_confirmation"
                           class="form-control"
                           placeholder="Ulangi password">

                    <button type="button"
                         class="btn btn-outline-secondary"
                         onclick="togglePassword('password_confirmation', this)"
                         title="Tampilkan password">

                     <i class="bi bi-eye"></i>

                    </button>

                </div>

            </div>


            {{-- BUTTON --}}
            <div class="d-flex gap-2">

                <a href="{{ route('owner.admin.index') }}"
                   class="btn btn-secondary">

                    Kembali

                </a>

                <button type="submit"
                        class="btn btn-primary">

                    Simpan Admin

                </button>

            </div>

        </form>

    </div>

</div>


<script>
    function togglePassword(inputId, button) {

    const input = document.getElementById(inputId);
    const icon = button.querySelector('i');

    if (input.type === 'password') {

        input.type = 'text';

        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');

        button.title = 'Sembunyikan password';

    } else {

        input.type = 'password';

        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');

        button.title = 'Tampilkan password';

    }

}
</script>

@endsection