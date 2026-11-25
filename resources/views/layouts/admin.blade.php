<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'E-Billing' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

<div class="d-flex min-vh-100">

    {{-- SIDEBAR --}}
    <aside class="bg-primary text-white d-flex flex-column"
           style="width: 250px; min-height: 100vh;">

       {{-- LOGO --}}
<div class="p-4 border-bottom border-light border-opacity-25 text-center">

    <img src="{{ asset('images/logo-pt.jpeg') }}"
         alt="Logo PT"
         class="img-fluid mb-3"
         style="width: 85px; height: 85px; object-fit: contain;">

    <h4 class="fw-bold mb-1">
        E-BILLING
    </h4>

    <small class="text-white-50">
        Sistem Billing
    </small>

</div>


        {{-- MENU --}}
       <nav class="nav flex-column p-3 gap-1">

            {{-- DASHBOARD --}}
            <a href="{{ route('dashboard') }}"
               class="nav-link text-white rounded px-3 py-3">

                🏠
                <span class="ms-2">Dashboard</span>

            </a>


            {{-- USER --}}
            <a href="{{ route('customers.index') }}"
               class="nav-link text-white rounded px-3 py-3">

                👥
                <span class="ms-2">User</span>

            </a>


            {{-- PAKET --}}
            <a href="{{ route('packages.index') }}"
               class="nav-link text-white rounded px-3 py-3">

                📦
                <span class="ms-2">Paket</span>

            </a>


            {{-- PAYMENT --}}
            <a href="{{ route('payments.index') }}"
               class="nav-link text-white rounded px-3 py-3">

                💳
                <span class="ms-2">Payment</span>

            </a>

        </nav>


        {{-- LOGOUT --}}
        <div class="p-3">

            <form method="POST"
                  action="{{ route('logout') }}">

                @csrf

                <button type="submit"
                        class="btn btn-danger w-100">

                    🚪
                    <span class="ms-2">Logout</span>

                </button>

            </form>

        </div>

    </aside>


    {{-- CONTENT UTAMA --}}
    <main class="flex-grow-1 bg-light">

        {{-- HEADER --}}
        <header class="bg-white border-bottom px-4 py-3">

            <div class="d-flex justify-content-between align-items-center">

                {{-- JUDUL HALAMAN --}}
                <div>

                   <h4 class="mb-0 fw-semibold">
    @yield('header', 'Dashboard')
</h4>

                </div>


                {{-- PROFIL ADMIN --}}
                <div class="dropdown">

                    <button
                        class="btn btn-light border-0 d-flex align-items-center gap-2"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">

                        {{-- INISIAL ADMIN --}}
                        <span
                            class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;">

                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}

                        </span>


                        {{-- NAMA ADMIN --}}
                        <span class="text-start">

                            <strong class="d-block">
                                {{ Auth::user()->name }}
                            </strong>

                            <small class="text-muted">
                                Administrator
                            </small>

                        </span>


                        <span class="ms-1">
                            ▾
                        </span>

                    </button>


                    {{-- DROPDOWN PROFIL --}}
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


                        {{-- PROFILE --}}
                        <li>

                            <a class="dropdown-item py-2"
                               href="{{ route('profile.edit') }}">

                                👤
                                <span class="ms-2">
                                    Profile
                                </span>

                            </a>

                        </li>


                        {{-- LOGOUT --}}
                        <li>

                            <form method="POST"
                                  action="{{ route('logout') }}">

                                @csrf

                                <button type="submit"
                                        class="dropdown-item text-danger py-2">

                                    🚪
                                    <span class="ms-2">
                                        Logout
                                    </span>

                                </button>

                            </form>

                        </li>

                    </ul>

                </div>

            </div>

        </header>


        {{-- ISI HALAMAN --}}
        <div class="p-4">

            @yield('content')

        </div>

    </main>

</div>

</body>

</html>