<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'E-Billing' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="billing-app bg-light">
@php
    $isOwner = Auth::user()->role === 'owner';
    $navigation = [
        ['route' => $isOwner ? 'owner.dashboard' : 'dashboard', 'match' => $isOwner ? 'owner.dashboard' : 'dashboard', 'icon' => 'house', 'label' => 'Dashboard'],
        ['route' => 'customers.index', 'match' => 'customers.*', 'icon' => 'people', 'label' => 'User'],
        ['route' => 'packages.index', 'match' => 'packages.*', 'icon' => 'box-seam', 'label' => 'Paket'],
    ];
    $paymentNavigation = [
        'payments.index' => 'All',
        'payments.unpaid' => 'Belum Bayar',
        'payments.paid' => 'Lunas',
    ];
    $paymentSection = match (true) {
        request()->routeIs('payments.unpaid') => 'payments.unpaid',
        request()->routeIs('payments.paid') => 'payments.paid',
        request()->routeIs('payments.*') && request()->query('from_unpaid') === '1' => 'payments.unpaid',
        request()->routeIs('payments.*') && request()->query('from_paid') === '1' => 'payments.paid',
        request()->routeIs('payments.*') => 'payments.index',
        default => null,
    };
    if ($isOwner) {
        $ownerNavigation = [
            ['route' => 'owner.monitoring', 'match' => 'owner.monitoring', 'icon' => 'bar-chart', 'label' => 'Monitoring'],
            ['route' => 'owner.admin.index', 'match' => 'owner.admin.*', 'icon' => 'person-gear', 'label' => 'Akses Admin'],
        ];
    }
@endphp
<a class="visually-hidden-focusable skip-link" href="#main-content">Lewati navigasi</a>
<div class="panel-shell">
    <aside class="offcanvas-lg offcanvas-start panel-sidebar text-bg-primary" tabindex="-1" id="panelNavigation" aria-labelledby="panelNavigationLabel">
        <div class="offcanvas-header border-bottom border-white border-opacity-25">
            <h2 class="offcanvas-title h5" id="panelNavigationLabel">Menu E-Billing</h2>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#panelNavigation" aria-label="Tutup menu navigasi"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column p-0">
            <div class="panel-brand border-bottom border-white border-opacity-25">
                <img src="{{ asset('images/logo-pt.jpeg') }}" alt="Logo PT" width="64" height="64" class="panel-logo mb-3">
                <p class="h4 fw-bold mb-1">E-BILLING</p>
                <p class="mb-0">Sistem Billing</p>
            </div>
            <nav class="nav flex-column p-3 gap-1" aria-label="Navigasi utama">
                @foreach($navigation as $item)
                    <x-ui.nav-item :href="route($item['route'])" :active="request()->routeIs($item['match'])" :icon="$item['icon']">{{ $item['label'] }}</x-ui.nav-item>
                @endforeach
                <div role="group" aria-labelledby="payment-navigation-label">
                    <div id="payment-navigation-label" class="panel-nav-link fw-semibold">
                        <i class="bi bi-credit-card" aria-hidden="true"></i>Payments
                    </div>
                    <ul class="list-unstyled ms-4 ps-2 border-start border-white border-opacity-25 mb-0">
                        @foreach($paymentNavigation as $route => $label)
                            <li><x-ui.nav-item :href="route($route)" :active="$paymentSection === $route">{{ $label }}</x-ui.nav-item></li>
                        @endforeach
                    </ul>
                </div>
                @foreach($ownerNavigation ?? [] as $item)
                    <x-ui.nav-item :href="route($item['route'])" :active="request()->routeIs($item['match'])" :icon="$item['icon']">{{ $item['label'] }}</x-ui.nav-item>
                @endforeach
            </nav>
            <div class="p-3 mt-auto">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-light w-100"><i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i>Logout</button>
                </form>
            </div>
        </div>
    </aside>
    <div class="panel-workspace">
        <header class="panel-header bg-white border-bottom">
            <button class="btn btn-outline-primary d-lg-none flex-shrink-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#panelNavigation" aria-controls="panelNavigation" aria-label="Buka menu navigasi">
                <i class="bi bi-list me-1" aria-hidden="true"></i>Menu
            </button>
            <h1 class="panel-title h5 fw-semibold mb-0">@yield('header', $isOwner ? 'Dashboard Owner' : 'Dashboard')</h1>
            <div class="dropdown ms-auto flex-shrink-0">
                <button class="btn btn-light d-flex align-items-center gap-2 panel-account" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menu akun">
                    <span class="panel-avatar bg-primary text-white" aria-hidden="true">{{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</span>
                    <span class="d-none d-md-block text-start panel-account-name"><strong class="d-block text-truncate">{{ Auth::user()->name }}</strong><small class="text-muted">{{ $isOwner ? 'Owner' : 'Administrator' }}</small></span>
                    <i class="bi bi-chevron-down d-none d-md-inline" aria-hidden="true"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow panel-account-menu">
                    <li class="px-3 py-2"><strong>{{ Auth::user()->name }}</strong><small class="d-block text-muted">{{ Auth::user()->email }}</small></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2" aria-hidden="true"></i>Profile</a></li>
                    <li><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i>Logout</button></form></li>
                </ul>
            </div>
        </header>
        <main id="main-content" class="panel-content" tabindex="-1">
            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
