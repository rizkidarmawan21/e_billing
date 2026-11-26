@extends(Auth::user()->role === 'owner' ? 'owner.layout' : 'layouts.admin')
@section('header', 'Data Paket')
@section('content')

    <div class="container-fluid">

        {{-- PESAN SUKSES --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                </button>
            </div>
        @endif

        {{-- PESAN ERROR --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                </button>
            </div>
        @endif


        {{-- HEADER HALAMAN --}}
        <x-ui.page-header>

            <div>

                <p class="text-muted mb-0">
                    Kelola paket internet yang tersedia untuk customer.
                </p>
            </div>

            <x-slot:actions>
            <a href="{{ route('packages.create') }}"
               class="btn btn-primary">

                <i class="bi bi-plus-lg me-2"></i>
                Tambah Paket

            </a>

            </x-slot:actions>
        </x-ui.page-header>

        {{-- TABEL PAKET --}}
        <div class="card border-0 shadow-sm">

            <div class="card-body p-0">

                <div class="table-responsive" tabindex="0" role="region" aria-label="Daftar paket">

                    <x-ui.table>

                        <thead class="table-light">

                            <tr>

                                <th class="px-4 py-3 text-center" scope="col">
                                    No.
                                </th>

                                <th>
                                    Nama Paket
                                </th>

                                <th>
                                    Kecepatan
                                </th>

                                <th>
                                    Harga
                                </th>

                                <th>
                                    Deskripsi
                                </th>

                                <th class="text-center">
                                    Aksi
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse ($packages as $package)

                                <tr>

                                    <td class="px-4" data-label="No.">
                                        {{ $loop->iteration }}
                                    </td>


                                    <td data-label="Nama Paket">

                                        <a href="{{ route('packages.show', $package) }}" class="fw-bold text-decoration-none">
                                            {{ $package->nama_paket }}
                                        </a>

                                    </td>


                                    <td data-label="Kecepatan">

                                        <span class="badge bg-info text-dark">
                                            {{ $package->kecepatan }}
                                        </span>

                                    </td>


                                    <td data-label="Harga">

                                        <strong class="text-success">
                                            Rp{{ number_format($package->harga, 0, ',', '.') }}
                                        </strong>

                                    </td>


                                    <td class="text-muted table-description" data-label="Deskripsi">

                                        <div>{{ $package->deskripsi ?? '-' }}</div>

                                    </td>


                                    <td class="text-center" data-label="Aksi">
                                        <div class="record-actions d-flex flex-wrap justify-content-center gap-2">

                                        {{-- EDIT --}}
                                        <x-ui.row-action action="edit" :href="route('packages.edit', $package)" />


                                        {{-- HAPUS --}}
                                        <form action="{{ route('packages.destroy', $package) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Yakin ingin menghapus paket ini?');">

                                            @csrf
                                            @method('DELETE')

                                            <x-ui.row-action action="delete" />

                                        </form>
                                        </div>
                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="6"
                                        class="text-center py-5">

                                        <div class="text-muted">

                                            <i class="bi bi-box-seam fs-1 d-block mb-3"></i>

                                            <h5>
                                                Belum ada paket
                                            </h5>

                                            <p class="mb-3">
                                                Silakan tambahkan paket internet terlebih dahulu.
                                            </p>

                                            <a href="{{ route('packages.create') }}"
                                               class="btn btn-primary">

                                                <i class="bi bi-plus-lg me-2"></i>
                                                Tambah Paket

                                            </a>

                                        </div>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </x-ui.table>

                </div>

            </div>

        </div>

    </div>

@endsection