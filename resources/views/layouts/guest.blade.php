<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'E-Billing') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-light">

    <div class="min-vh-100 d-flex align-items-center justify-content-center py-5">

        <div class="w-100" style="max-width: 450px;">

            <!-- Logo -->
<div class="text-center mb-3">
    <a href="/">
        <x-application-logo
            style="width: 90px; height: 90px;"
        />
    </a>
</div>

<!-- Kotak Form -->
<div class="bg-white shadow rounded-3 p-4">

    {{ $slot }}

</div>

        </div>

    </div>

</body>
</html>