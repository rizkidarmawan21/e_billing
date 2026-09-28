@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    {{-- HEADER HALAMAN --}}
    <div class="mb-4">
        <h4 class="fw-bold mb-1">
            Profile Admin
        </h4>

        <p class="text-muted mb-0">
            Kelola informasi akun administrator.
        </p>
    </div>


    {{-- INFORMASI PROFILE --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-primary text-white py-3">
            <h5 class="mb-0">
                Informasi Profile
            </h5>
        </div>

        <div class="card-body p-4">

            @include('profile.partials.update-profile-information-form')

        </div>

    </div>


    {{-- UBAH PASSWORD --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-primary text-white py-3">
            <h5 class="mb-0">
                Ubah Password
            </h5>
        </div>

        <div class="card-body p-4">

            @include('profile.partials.update-password-form')

        </div>

    </div>


    {{-- HAPUS AKUN --}}
    <div class="card border-0 shadow-sm border-danger">

        <div class="card-header bg-danger text-white py-3">
            <h5 class="mb-0">
                Hapus Akun
            </h5>
        </div>

        <div class="card-body p-4">

            @include('profile.partials.delete-user-form')

        </div>

    </div>

</div>

@endsection