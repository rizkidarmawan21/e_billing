<x-guest-layout>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label fw-semibold">
                Name
            </label>

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

            @error('name')
                <div class="text-danger small mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Role -->
        <div class="mb-3">
            <label for="role" class="form-label fw-semibold">
                Role
            </label>

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

            @error('role')
                <div class="text-danger small mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">
                Email
            </label>

            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                class="form-control"
                required
                autocomplete="username"
            >

            @error('email')
                <div class="text-danger small mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Password -->
<div class="mb-3">
    <label for="password" class="form-label fw-semibold">
        Password
    </label>

    <div class="input-group">
        <input
            id="password"
            type="password"
            name="password"
            class="form-control"
            required
            autocomplete="new-password"
        >

        <button
            type="button"
            class="btn btn-outline-secondary"
            onclick="togglePassword('password', 'passwordIcon')"
        >
            <i class="bi bi-eye" id="passwordIcon"></i>
        </button>
    </div>

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

    <div class="input-group">
        <input
            id="password_confirmation"
            type="password"
            name="password_confirmation"
            class="form-control"
            required
            autocomplete="new-password"
        >

        <button
            type="button"
            class="btn btn-outline-secondary"
            onclick="togglePassword('password_confirmation', 'confirmPasswordIcon')"
        >
            <i class="bi bi-eye" id="confirmPasswordIcon"></i>
        </button>
    </div>

    @error('password_confirmation')
        <div class="text-danger small mt-1">
            {{ $message }}
        </div>
    @enderror
</div>

        <!-- Tombol -->
        <div class="d-flex justify-content-between align-items-center mt-4">

            <a
                href="{{ route('login') }}"
                class="text-decoration-none text-secondary"
            >
                Already registered?
            </a>

            <button type="submit" class="btn btn-primary px-4">
                Register
            </button>

        </div>

    </form>

    <script>
    function togglePassword(inputId, iconId) {
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

</x-guest-layout>