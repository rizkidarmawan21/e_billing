@extends('owner.layout')

@section('header', 'Akses Admin')

@section('content')

<div class="card border-0 shadow-sm">

    <div class="card-body p-4">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h5 class="fw-semibold mb-1">
                    Daftar Admin
                </h5>

                <p class="text-muted mb-0">
                    Kelola akun administrator yang dapat mengakses sistem.
                </p>
            </div>

            <a href="{{ route('owner.admin.create') }}"
               class="btn btn-primary">

                + Tambah Admin

            </a>

        </div>


        {{-- TABLE --}}
        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th class="text-center">Aksi</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($admins as $admin)

                        <tr>

                            <td>
                                {{ $admins->firstItem() + $loop->index }}
                            </td>

                            <td class="fw-semibold">
                                {{ $admin->name }}
                            </td>

                            <td>
                                {{ $admin->email }}
                            </td>

                            <td>
                                <span class="badge bg-primary">
                                    Admin
                                </span>
                            </td>

                            <td class="text-center">

    <div class="d-flex justify-content-center gap-2">

        <a href="{{ route('owner.admin.edit', $admin->id) }}"
           class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-pencil-square me-1"></i>
            Edit
        </a>

        <form action="{{ route('owner.admin.destroy', $admin->id) }}"
              method="POST"
              onsubmit="return confirm('Yakin ingin menghapus admin ini?');"
              class="d-inline">
            @csrf
            @method('DELETE')

            <button type="submit"
                    class="btn btn-sm btn-outline-danger">
                <i class="bi bi-trash me-1"></i>
                Hapus
            </button>

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

            </table>

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