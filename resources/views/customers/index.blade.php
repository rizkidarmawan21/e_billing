@extends(Auth::user()->role === 'owner' ? 'owner.layout' : 'layouts.admin')

@section('header', 'Data Customer')

@section('content')

    <!-- HEADER -->
    <x-ui.page-header>

        <div>

            <p class="text-muted mb-0">
                Kelola data pelanggan E-Billing
            </p>
        </div>

        <x-slot:actions>
        <a href="{{ route('customers.create') }}"
           class="btn btn-primary">

            <i class="bi bi-plus-lg me-2"></i>
            Tambah Customer

        </a>

        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.customer-search :action="route('customers.index')" :search="$search" />

    <!-- ALERT SUCCESS -->
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show"
             role="alert">

            <i class="bi bi-check-circle me-2"></i>

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert" aria-label="Tutup notifikasi">
            </button>

        </div>

    @endif


    <!-- TABLE -->
    <div class="card border-0 shadow-sm">

        <div class="card-body p-0">

            <div class="table-responsive" tabindex="0" role="region" aria-label="Daftar pelanggan">

                <x-ui.table>

                    <thead class="table-light">

                        <tr>

                            <th class="px-4 text-center" scope="col">
                                No.
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
                                <td class="px-4" data-label="No.">

                                    {{ $customers->firstItem() + $loop->index }}

                                </td>


                                <!-- KODE -->
                                <td data-label="Kode Customer">

                                    <span class="fw-semibold">
                                        {{ $customer->kode_pelanggan }}
                                    </span>

                                </td>


                                <!-- NAMA -->
                                <td data-label="Nama Customer">

                                    <a href="{{ route('customers.show', $customer) }}"
                                       class="text-decoration-none fw-semibold">

                                        {{ $customer->nama }}

                                    </a>

                                </td>


                                <!-- NO HP -->
                                <td data-label="No. HP">

                                    {{ $customer->no_hp ?? '-' }}

                                </td>


                                <!-- PAKET -->
                                <td data-label="Paket">

                                    @if($customer->package)

                                        <span class="package-name">

                                            {{ $customer->package->nama_paket }}

                                        </span>

                                    @else

                                        <span class="text-muted">
                                            Belum ada paket
                                        </span>

                                    @endif

                                </td>


                                <!-- AKSI -->
                                <td class="text-center" data-label="Aksi">

                                    <div class="record-actions d-flex flex-wrap justify-content-center gap-2">
                                        <x-ui.row-action action="detail" :href="route('customers.show', $customer)" :label="'Detail customer '.$customer->nama" />

                                        <!-- EDIT -->
                                        <x-ui.row-action action="edit" :href="route('customers.edit', $customer)" :label="'Edit customer '.$customer->nama" />


                                        <!-- HAPUS -->
                                        <form
                                            method="POST"
                                            action="{{ route('customers.destroy', $customer) }}"
                                            onsubmit="return confirm('Yakin ingin menghapus customer ini?')">

                                            @csrf
                                            @method('DELETE')

                                            <x-ui.row-action action="delete" :label="'Hapus customer '.$customer->nama" />

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

                </x-ui.table>

            </div>

        </div>

        <!-- PAGINATION -->
        @if($customers->hasPages())

            <div class="card-footer bg-white border-0 py-3">

                <div class="payment-pagination">
                    {{ $customers->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>

            </div>

        @endif

    </div> {{-- Penutup card tabel Customer --}}

@endsection
