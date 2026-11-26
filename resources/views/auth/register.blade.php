<x-guest-layout>
    <x-ui.page-header :title="__('Register')" />

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <x-ui.field name="name" label="Name" :required="true">
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                class="form-control"
                required
                autofocus
                autocomplete="name"
            >

        </x-ui.field>

        <!-- Role -->
        <x-ui.field name="role" label="Role" :required="true">
            <select
                id="role"
                name="role"
                class="form-select"
                required
            >
                <option value="">-- Pilih Role --</option>

                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                    Admin
                </option>

                <option value="owner" {{ old('role') == 'owner' ? 'selected' : '' }}>
                    Owner
                </option>
            </select>

        </x-ui.field>

        <!-- Email -->
        <x-ui.field name="email" label="Email" :required="true">
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                class="form-control"
                required
                autocomplete="username"
            >

        </x-ui.field>

        <!-- Password -->
<div class="mb-3">
    <label for="password" class="form-label fw-semibold">
        Password
    </label>

    <x-ui.password-input id="password" name="password" class="form-control" required autocomplete="new-password" />

    @error('password')
        <div class="text-danger small mt-1">
            {{ $message }}
        </div>
    @enderror
</div>

        <!-- Confirm Password -->
<div class="mb-3">
    <label for="password_confirmation" class="form-label fw-semibold">
        Confirm Password
    </label>

    <x-ui.password-input id="password_confirmation" name="password_confirmation" class="form-control" required autocomplete="new-password" />

    @error('password_confirmation')
        <div class="text-danger small mt-1">
            {{ $message }}
        </div>
    @enderror
</div>

        <!-- Tombol -->
        <x-ui.form-actions class="justify-content-sm-between mt-4">

            <a
                href="{{ route('login') }}"
                class="text-decoration-none text-secondary"
            >
                Already registered?
            </a>

            <button type="submit" class="btn btn-primary px-4">
                Register
            </button>

        </x-ui.form-actions>

    </form>

</x-guest-layout>