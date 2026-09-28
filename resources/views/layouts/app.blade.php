<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ config('app.name', 'E-Billing') }}</title>

    @vite(['resources/js/app.js'])
</head>

<body class="bg-light">

<div class="d-flex min-vh-100">

    <!-- SIDEBAR -->
    <aside class="bg-primary text-white d-flex flex-column"
           style="width: 250px; min-height: 100vh;">

        <!-- LOGO -->
        <div class="p-4 border-bottom border-light border-opacity-25">

            <h3 class="fw-bold mb-1">
                E-BILLING
            </h3>

            <small class="text-white-50">
                Sistem Billing
            </small>

        </div>


        <!-- MENU UTAMA -->
        <nav class="nav flex-column p-3 gap-1 flex-grow-1">

            <!-- DASHBOARD -->
            <a href="{{ route('dashboard') }}"
               class="nav-link text-white rounded px-3 py-3">

                <i class="bi bi-grid-1x2-fill me-3"></i>
                Dashboard

            </a>


            <!-- USER -->
            <a href="{{ route('customers.index') }}"
               class="nav-link text-white rounded px-3 py-3">

                <i class="bi bi-people-fill me-3"></i>
                User

            </a>


            <!-- PAKET -->
            <a href="{{ route('packages.index') }}"
               class="nav-link text-white rounded px-3 py-3">

                <i class="bi bi-box-seam-fill me-3"></i>
                Paket

            </a>


            <!-- PAYMENT -->
            <a href="{{ route('payments.index') }}"
               class="nav-link text-white rounded px-3 py-3">

                <i class="bi bi-credit-card-fill me-3"></i>
                Payment

            </a>


            <!-- ISOLIR -->
            <a href="#"
               class="nav-link text-white rounded px-3 py-3">

                <i class="bi bi-lock-fill me-3"></i>
                Isolir

            </a>

        </nav>


        <!-- LOGOUT -->
        <div class="p-3 border-top border-light border-opacity-25">

            <form method="POST" action="{{ route('logout') }}">

                @csrf

                <button type="submit"
                        class="nav-link text-white rounded px-3 py-3 w-100 text-start border-0 bg-transparent">

                    <i class="bi bi-box-arrow-right me-3"></i>
                    Logout

                </button>

            </form>

        </div>

    </aside>


    <!-- KONTEN UTAMA -->
    <main class="flex-grow-1">

        <!-- HEADER -->
        <header class="bg-white border-bottom px-4 py-3">

            <div class="d-flex justify-content-between align-items-center">

                <!-- JUDUL -->
                <div>

                    <h4 class="mb-0 fw-semibold">
                        {{ $header ?? 'Dashboard' }}
                    </h4>

                </div>


                <!-- PROFIL ADMIN -->
                <div class="dropdown">

                    <button
                        class="btn btn-light d-flex align-items-center gap-2 border-0"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">

                        <!-- INISIAL -->
                        <span class="rounded-circle bg-primary text-white
                                     d-flex align-items-center justify-content-center"
                              style="width: 40px; height: 40px;">

                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}

                        </span>


                        <!-- NAMA -->
                        <span class="text-start">

                            <strong class="d-block">
                                {{ Auth::user()->name }}
                            </strong>

                            <small class="text-muted">
                                Administrator
                            </small>

                        </span>

                        <i class="bi bi-chevron-down ms-1"></i>

                    </button>


                    <!-- DROPDOWN PROFIL -->
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">

                        <li>
                            <div class="px-3 py-2">

                                <strong>
                                    {{ Auth::user()->name }}
                                </strong>

                                <small class="d-block text-muted">
                                    {{ Auth::user()->email }}
                                </small>

                            </div>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>


                        <li>

                            <a class="dropdown-item py-2"
                               href="{{ route('profile.edit') }}">

                                <i class="bi bi-person-circle me-2"></i>
                                Profile

                            </a>

                        </li>

                    </ul>

                </div>

            </div>

        </header>


        <!-- ISI HALAMAN -->
        <div class="p-4 p-lg-5">

    @yield('content')

</div>

    </main>

</div>

</body>
</html>