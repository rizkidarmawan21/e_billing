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
        <x-ui.field name="name" label="Nama" :required="true">
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

        </x-ui.field>

        {{-- EMAIL --}}
        <x-ui.field name="email" label="Email" :required="true">
            <input
                id="email"
                name="email"
                type="email"
                class="form-control"
                value="{{ old('email', $user->email) }}"
                required
                autocomplete="username"
            >

        </x-ui.field>

        {{-- BUTTON --}}
        <x-ui.form-actions class="mt-4">
            <button type="submit"
                    class="btn text-white px-4"
                    style="background-color: #2563a6;">
                Simpan Perubahan
            </button>
        </x-ui.form-actions>

        @if (session('status') === 'profile-updated')
            <div class="alert alert-success mt-3 mb-0">
                ✓ Informasi profile berhasil diperbarui.
            </div>
        @endif
    </form>
</section>