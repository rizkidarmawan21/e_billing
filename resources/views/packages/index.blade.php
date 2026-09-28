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
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <p class="text-muted mb-0">
                    Kelola paket internet yang tersedia untuk customer.
                </p>
            </div>

            <a href="{{ route('packages.create') }}"
               class="btn btn-primary">

                <i class="bi bi-plus-lg me-2"></i>
                Tambah Paket

            </a>

        </div>


        {{-- TABEL PAKET --}}
        <div class="card border-0 shadow-sm">

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>

                                <th class="px-4 py-3">
                                    No
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

                                    <td class="px-4">
                                        {{ $loop->iteration }}
                                    </td>


                                    <td>

                                        <strong>
                                            {{ $package->nama_paket }}
                                        </strong>

                                    </td>


                                    <td>

                                        <span class="badge bg-info text-dark">
                                            {{ $package->kecepatan }}
                                        </span>

                                    </td>


                                    <td>

                                        <strong class="text-success">
                                            Rp{{ number_format($package->harga, 0, ',', '.') }}
                                        </strong>

                                    </td>


                                    <td class="text-muted">

                                        {{ $package->deskripsi ?? '-' }}

                                    </td>


                                    <td class="text-center">

                                        {{-- EDIT --}}
                                        <a href="{{ route('packages.edit', $package) }}"
                                           class="btn btn-sm btn-warning me-1">

                                            <i class="bi bi-pencil-square me-1"></i>
                                            Edit

                                        </a>


                                        {{-- HAPUS --}}
                                        <form action="{{ route('packages.destroy', $package) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Yakin ingin menghapus paket ini?');">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-danger">

                                                <i class="bi bi-trash me-1"></i>
                                                Hapus

                                            </button>

                                        </form>

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

                    </table>

                </div>

            </div>

        </div>

    </div>

@endsection