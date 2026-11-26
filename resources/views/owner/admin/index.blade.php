@extends('owner.layout')

@section('header', 'Akses Admin')

@section('content')

<div class="card border-0 shadow-sm">

    <div class="card-body p-3 p-md-4">

        {{-- HEADER --}}
        <x-ui.page-header>

            <div>
                <h5 class="fw-semibold mb-1">
                    Daftar Admin
                </h5>

                <p class="text-muted mb-0">
                    Kelola akun administrator yang dapat mengakses sistem.
                </p>
            </div>

            <x-slot:actions>
            <a href="{{ route('owner.admin.create') }}"
               class="btn btn-primary">

                + Tambah Admin

            </a>

            </x-slot:actions>
        </x-ui.page-header>

        {{-- TABLE --}}
        <div class="table-responsive" tabindex="0" role="region" aria-label="Daftar akses admin">

            <x-ui.table>

                <thead class="table-light">

                    <tr>
                        <th class="text-center" scope="col">No.</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th class="text-center">Aksi</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($admins as $admin)

                        <tr>

                            <td data-label="No.">
                                {{ $admins->firstItem() + $loop->index }}
                            </td>

                            <td class="fw-semibold" data-label="Nama">
                                {{ $admin->name }}
                            </td>

                            <td data-label="Email">
                                {{ $admin->email }}
                            </td>

                            <td data-label="Role">
                                Admin
                            </td>

                            <td class="text-center" data-label="Aksi">

    <div class="record-actions d-flex flex-wrap justify-content-center gap-2">

        <x-ui.row-action action="edit" :href="route('owner.admin.edit', $admin->id)" />

        <form action="{{ route('owner.admin.destroy', $admin->id) }}"
              method="POST"
              onsubmit="return confirm('Yakin ingin menghapus admin ini?');"
              class="d-inline">
            @csrf
            @method('DELETE')

            <x-ui.row-action action="delete" />

        </form>

    </div>

</td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5"
                                class="text-center text-muted py-4">

                                Belum ada akun Admin.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </x-ui.table>

        </div>


        {{-- PAGINATION --}}
        @if($admins->hasPages())

            <div class="mt-4">

                {{ $admins->links() }}

            </div>

        @endif

    </div>

</div>

@endsection