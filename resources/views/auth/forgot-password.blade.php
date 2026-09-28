<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Lupa Password - E-Billing</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>

<body class="bg-light">

<div class="min-vh-100 d-flex align-items-center justify-content-center py-5">

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-12 col-sm-10 col-md-7 col-lg-5 col-xl-4">

                {{-- CARD --}}
                <div class="card border-0 shadow-sm">

                    <div class="card-body p-4 p-md-5">

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


                        {{-- JUDUL --}}
                        <div class="mb-4">

                            <h4 class="fw-bold mb-1">
                                Lupa Password?
                            </h4>

                            <p class="text-muted mb-0">
                                Masukkan email akun administrator untuk mendapatkan link reset password.
                            </p>

                        </div>


                        {{-- STATUS BERHASIL --}}
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


                        {{-- FORM --}}
                        <form method="POST" action="{{ route('password.email') }}">

                            @csrf

                            {{-- EMAIL --}}
                            <div class="mb-4">

                                <label
                                    for="email"
                                    class="form-label fw-semibold"
                                >
                                    Email
                                </label>

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
                                        autocomplete="email"
                                    >

                                </div>

                            </div>


                            {{-- BUTTON --}}
                            <button
                                type="submit"
                                class="btn w-100 text-white fw-semibold py-2"
                                style="background-color: #2563a6;"
                            >
                                Kirim Link Reset Password
                            </button>


                            {{-- KEMBALI LOGIN --}}
                            <div class="text-center mt-3">

                                <a
                                    href="{{ route('login') }}"
                                    class="text-decoration-none"
                                >
                                    ← Kembali ke Login
                                </a>

                            </div>

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