@extends('owner.layout')

@section('header', 'Monitoring')

@section('content')

@php($monitoringUrl = 'https://mikrotik.sifnet.my.id')

<x-ui.page-header title="Monitoring" description="Jika login di panel ini tidak berfungsi, buka monitoring di tab baru.">
    <x-slot:actions>
        <a href="{{ $monitoringUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary">
            <i class="bi bi-box-arrow-up-right me-1" aria-hidden="true"></i>Buka di tab baru
        </a>
    </x-slot:actions>
</x-ui.page-header>

<div class="card border-0 shadow-sm overflow-hidden">
    <iframe
        src="{{ $monitoringUrl }}"
        title="Monitoring MikroTik Sifnet"
        class="d-block w-100 border-0"
        style="height: max(30rem, 72dvh);"
        sandbox="allow-forms allow-scripts allow-same-origin"
        referrerpolicy="no-referrer"
    ></iframe>
</div>

@endsection