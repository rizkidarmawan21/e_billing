<section>
    <div class="mb-4">
        <h5 class="fw-bold mb-1">
            Informasi Akun
        </h5>

        <p class="text-muted mb-0">
            Perbarui nama dan alamat email akun administrator.
        </p>
    </div>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        {{-- NAMA --}}
        <div class="mb-3">
            <label for="name" class="form-label fw-semibold">
                Nama
            </label>

            <input
                id="name"
                name="name"
                type="text"
                class="form-control"
                value="{{ old('name', $user->name) }}"
                required
                autofocus
                autocomplete="name"
            >

            @if ($errors->get('name'))
                <div class="text-danger small mt-1">
                    {{ $errors->first('name') }}
                </div>
            @endif
        </div>

        {{-- EMAIL --}}
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">
                Email
            </label>

            <input
                id="email"
                name="email"
                type="email"
                class="form-control"
                value="{{ old('email', $user->email) }}"
                required
                autocomplete="username"
            >

            @if ($errors->get('email'))
                <div class="text-danger small mt-1">
                    {{ $errors->first('email') }}
                </div>
            @endif
        </div>

        {{-- BUTTON --}}
        <div class="mt-4">
            <button type="submit"
                    class="btn text-white px-4"
                    style="background-color: #2563a6;">
                Simpan Perubahan
            </button>
        </div>

        @if (session('status') === 'profile-updated')
            <div class="alert alert-success mt-3 mb-0">
                ✓ Informasi profile berhasil diperbarui.
            </div>
        @endif
    </form>
</section>