<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - E-Billing</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>

<body class="billing-auth bg-light">

<div class="min-vh-100 d-flex align-items-center justify-content-center py-4 py-md-5">

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-12 col-sm-10 col-md-7 col-lg-5 col-xl-4">

                {{-- CARD LOGIN --}}
                <div class="card border-0 shadow-sm">

                    <div class="card-body p-3 p-sm-4 p-md-5">

                        {{-- LOGO PT --}}
                        <div class="text-center mb-4">

                            <img
                                src="{{ asset('images/logo-pt.jpeg') }}"
                                alt="Logo PT"
                                style="width: 130px; height: auto;"
                                class="mb-3"
                            >

                            <h2 class="fw-bold mb-1">
                                E-BILLING
                            </h2>

                            <p class="text-muted mb-0">
                                Sistem Informasi Billing Pelanggan
                            </p>

                        </div>


                        {{-- JUDUL LOGIN --}}
                        <x-ui.page-header>

                            <h4 class="fw-bold mb-1">
                                Selamat Datang
                            </h4>

                            <p class="text-muted mb-0">
                                Silakan masuk untuk melanjutkan ke sistem.
                            </p>

                        </x-ui.page-header>

                        {{-- STATUS --}}
                        @if (session('status'))

                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>

                        @endif


                        {{-- ERROR --}}
                        @if ($errors->any())

                            <div class="alert alert-danger">

                                <ul class="mb-0 ps-3">

                                    @foreach ($errors->all() as $error)

                                        <li>{{ $error }}</li>

                                    @endforeach

                                </ul>

                            </div>

                        @endif


                        {{-- FORM LOGIN --}}
                        <form method="POST" action="{{ route('login') }}">

                            @csrf


                            {{-- EMAIL --}}
                            <x-ui.field name="email" label="Email" :required="true">
                                <div class="input-group">

                                    <span class="input-group-text bg-white">
                                        ✉
                                    </span>

                                    <input
                                        id="email"
                                        type="email"
                                        name="email"
                                        class="form-control"
                                        value="{{ old('email') }}"
                                        placeholder="Masukkan email"
                                        required
                                        autofocus
                                        autocomplete="username"
                                    >

                                </div>

                            </x-ui.field>

                            {{-- PASSWORD --}}
                            <div class="mb-3">

                                <label
                                    for="password"
                                    class="form-label fw-semibold"
                                >
                                    Password
                                </label>

                                <x-ui.password-input id="password" name="password"
                                    class="form-control" placeholder="Masukkan password"
                                    required autocomplete="current-password" />

                            </div>


                            {{-- REMEMBER + LUPA PASSWORD --}}
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">

                                <div class="form-check">

                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="remember"
                                        id="remember"
                                    >

                                    <label
                                        class="form-check-label"
                                        for="remember"
                                    >
                                        Ingat saya
                                    </label>

                                </div>


                                <a
                                    href="{{ route('password.request') }}"
                                    class="text-decoration-none"
                                >
                                    Lupa password?
                                </a>

                            </div>


                            {{-- BUTTON LOGIN --}}
                            <x-ui.form-actions>
                            <button
                                type="submit"
                                class="btn w-100 text-white fw-semibold py-2"
                                style="background-color: #2563a6;"
                            >
                                Login
                            </button>
                            </x-ui.form-actions>

                        </form>


                        {{-- FOOTER --}}
                        <div class="text-center mt-4">

                            <small class="text-muted">
                                E-Billing &copy; {{ date('Y') }}
                            </small>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

</body>

</html>