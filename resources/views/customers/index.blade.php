@extends(Auth::user()->role === 'owner' ? 'owner.layout' : 'layouts.admin')

@section('header', 'Data Customer')

@section('content')

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <p class="text-muted mb-0">
                Kelola data pelanggan E-Billing
            </p>
        </div>

        <a href="{{ route('customers.create') }}"
           class="btn btn-primary">

            <i class="bi bi-plus-lg me-2"></i>
            Tambah Customer

        </a>

    </div>


    <!-- SEARCH -->
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <form method="GET"
                  action="{{ route('customers.index') }}">

                <div class="row align-items-end">

                    <div class="col-md-8">

                        <label for="search"
                               class="form-label fw-semibold">

                            Cari Nama Customer

                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>

                            <input
                                type="text"
                                id="search"
                                name="search"
                                value="{{ $search }}"
                                class="form-control"
                                placeholder="Ketik nama customer...">

                        </div>

                    </div>

                    <div class="col-md-4 mt-3 mt-md-0">

                        <button type="submit"
                                class="btn btn-primary me-2">

                            <i class="bi bi-search me-1"></i>
                            Cari

                        </button>

                        @if($search)

                            <a href="{{ route('customers.index') }}"
                               class="btn btn-outline-secondary">

                                Reset

                            </a>

                        @endif

                    </div>

                </div>

            </form>

        </div>

    </div>


    <!-- ALERT SUCCESS -->
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show"
             role="alert">

            <i class="bi bi-check-circle me-2"></i>

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    <!-- TABLE -->
    <div class="card border-0 shadow-sm">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th class="px-4">
                                No
                            </th>

                            <th>
                                Kode Customer
                            </th>

                            <th>
                                Nama Customer
                            </th>

                            <th>
                                No. HP
                            </th>

                            <th>
                                Paket
                            </th>

                            <th class="text-center">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($customers as $customer)

                            <tr>

                                <!-- NOMOR -->
                                <td class="px-4">

                                    {{ $customers->firstItem() + $loop->index }}

                                </td>


                                <!-- KODE -->
                                <td>

                                    <span class="fw-semibold">
                                        {{ $customer->kode_pelanggan }}
                                    </span>

                                </td>


                                <!-- NAMA -->
                                <td>

                                    <a href="{{ route('customers.show', $customer) }}"
                                       class="text-decoration-none fw-semibold">

                                        {{ $customer->nama }}

                                    </a>

                                </td>


                                <!-- NO HP -->
                                <td>

                                    {{ $customer->no_hp ?? '-' }}

                                </td>


                                <!-- PAKET -->
                                <td>

                                    @if($customer->package)

                                        <span class="badge bg-primary">

                                            {{ $customer->package->nama_paket }}

                                        </span>

                                    @else

                                        <span class="text-muted">
                                            Belum ada paket
                                        </span>

                                    @endif

                                </td>


                                <!-- AKSI -->
                                <td class="text-center">

                                    <div class="d-flex justify-content-center gap-2">

                                        <!-- EDIT -->
                                        <a href="{{ route('customers.edit', $customer) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Edit">

                                            <i class="bi bi-pencil"></i>

                                        </a>


                                        <!-- HAPUS -->
                                        <form
                                            method="POST"
                                            action="{{ route('customers.destroy', $customer) }}"
                                            onsubmit="return confirm('Yakin ingin menghapus customer ini?')">

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Hapus">

                                                <i class="bi bi-trash"></i>

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6"
                                    class="text-center py-5">

                                    <div class="text-muted">

                                        <i class="bi bi-person-x"
                                           style="font-size: 40px;">
                                        </i>

                                        <p class="mt-3 mb-0">

                                            @if($search)

                                                Customer dengan nama
                                                "{{ $search }}"
                                                tidak ditemukan.

                                            @else

                                                Belum ada data customer.

                                            @endif

                                        </p>

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        <!-- PAGINATION -->
        @if($customers->hasPages())

            <div class="card-footer bg-white border-0 py-3">

                {{ $customers->links() }}

            </div>

        @endif

    </div>

@endsection