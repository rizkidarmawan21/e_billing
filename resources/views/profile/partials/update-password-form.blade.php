<section>
    <div class="mb-4">
        <h5 class="fw-bold mb-1">
            Ubah Password
        </h5>

        <p class="text-muted mb-0">
            Gunakan password yang kuat untuk menjaga keamanan akun administrator.
        </p>
    </div>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        {{-- PASSWORD LAMA --}}
        <div class="mb-3">
            <label for="current_password" class="form-label fw-semibold">
                Password Saat Ini
            </label>

            <input
                id="current_password"
                name="current_password"
                type="password"
                class="form-control"
                autocomplete="current-password"
            >

            @if ($errors->updatePassword->get('current_password'))
                <div class="text-danger small mt-1">
                    {{ $errors->updatePassword->first('current_password') }}
                </div>
            @endif
        </div>

        {{-- PASSWORD BARU --}}
        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">
                Password Baru
            </label>

            <input
                id="password"
                name="password"
                type="password"
                class="form-control"
                autocomplete="new-password"
            >

            @if ($errors->updatePassword->get('password'))
                <div class="text-danger small mt-1">
                    {{ $errors->updatePassword->first('password') }}
                </div>
            @endif
        </div>

        {{-- KONFIRMASI --}}
        <div class="mb-3">
            <label for="password_confirmation" class="form-label fw-semibold">
                Konfirmasi Password Baru
            </label>

            <input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                class="form-control"
                autocomplete="new-password"
            >

            @if ($errors->updatePassword->get('password_confirmation'))
                <div class="text-danger small mt-1">
                    {{ $errors->updatePassword->first('password_confirmation') }}
                </div>
            @endif
        </div>

        {{-- BUTTON --}}
        <div class="mt-4">
            <button type="submit"
                    class="btn text-white px-4"
                    style="background-color: #2563a6;">
                Simpan Password
            </button>
        </div>

        @if (session('status') === 'password-updated')
            <div class="alert alert-success mt-3 mb-0">
                ✓ Password berhasil diperbarui.
            </div>
        @endif
    </form>
</section>