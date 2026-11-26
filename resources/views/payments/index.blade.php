@extends(Auth::user()->role === 'owner' ? 'owner.layout' : 'layouts.admin')

@section('header', 'Data Payment')

@section('content')

<div class="container-fluid">

    {{-- PESAN SUKSES --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">

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

            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>
    @endif


    {{-- HEADER --}}
    <x-ui.page-header description="Kelola tagihan dan pembayaran customer.">
        <x-slot:actions>

            <form action="{{ route('payments.generate-monthly') }}"
                  method="POST">

                @csrf

                <button type="submit" class="btn btn-primary">

                    <i class="bi bi-arrow-repeat me-1"></i>

                    Generate Tagihan Bulanan

                </button>

            </form>


            <a href="{{ route('payments.create') }}"
               class="btn btn-outline-primary">

                <i class="bi bi-plus-lg me-1"></i>

                Tambah Payment

            </a>

        </x-slot:actions>
    </x-ui.page-header>


    <x-ui.customer-search :action="route('payments.index')" :search="$search" />


    {{-- TABEL PAYMENT --}}
    <div class="card border-0 shadow-sm">

        <div class="card-body p-0">

            <div class="table-responsive" tabindex="0" role="region" aria-label="Daftar payment">

                <x-ui.table>

                    <thead class="table-light">

                        <tr>

                            <th class="px-4 py-3 text-center" scope="col">
                                No.
                            </th>

                            <th>
                                Customer
                            </th>

                            <th>
                                Paket
                            </th>

                            <th>
                                Periode
                            </th>

                            <th>
                                Tagihan
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Tanggal Bayar
                            </th>

                            <th class="text-center">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse ($customers as $customer)

                            @php
                                 $payment = $customer->currentPayment;
                            @endphp


                            <tr>

                                {{-- NO --}}
                                <td data-label="No." class="px-4">

                                    {{ $customers->firstItem() + $loop->index }}

                                </td>


                                {{-- CUSTOMER --}}
                                <td data-label="Customer">

                                    <strong>
                                        {{ $customer->nama }}
                                    </strong>

                                    <div class="small text-muted">

                                        {{ $customer->kode_pelanggan }}

                                    </div>

                                </td>


                                {{-- PAKET --}}
                                <td data-label="Paket">

                                    @if ($customer->package)

                                        {{ $customer->package->nama_paket }}

                                        <div class="small text-muted">

                                            {{ $customer->package->kecepatan }}

                                        </div>

                                    @else

                                        <span class="text-muted">
                                            -
                                        </span>

                                    @endif

                                </td>


                                {{-- PERIODE --}}
                                <td data-label="Periode">
                                    @if ($payment)
                                        {{ $payment->periode }} {{ $payment->tahun }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>


                                {{-- TAGIHAN --}}
                                <td data-label="Tagihan">

                                    <strong class="text-primary">

                                        Rp
                                        {{ number_format(
                                            $payment?->jumlah_tagihan ?? $customer->package?->harga ?? 0,
                                            0,
                                            ',',
                                            '.'
                                        ) }}

                                    </strong>

                                </td>


                                {{-- STATUS --}}
                                <td data-label="Status">

                                    <x-ui.payment-status :status="$payment?->status" />

                                </td>


                                {{-- TANGGAL BAYAR --}}
                                <td data-label="Tanggal Bayar">

                                    @if ($payment && $payment->tanggal_bayar)

                                        {{ $payment->tanggal_bayar->format('d-m-Y') }}

                                    @else

                                        <span class="text-muted">
                                            -
                                        </span>

                                    @endif

                                </td>


                                {{-- AKSI --}}
                                <td data-label="Aksi" class="text-center">
                                    <div class="record-actions">
                                    @if ($payment)

                                        <x-ui.row-action action="detail" :href="route('payments.show', $payment)" />
                                        <x-ui.row-action action="edit" :href="route('payments.edit', $payment)" />


                                        {{-- HAPUS --}}
                                        <form action="{{ route('payments.destroy', $payment) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Yakin ingin menghapus pembayaran ini?');">

                                            @csrf
                                            @method('DELETE')

                                            <x-ui.row-action action="delete" />

                                        </form>

                                    @else

                                        <span class="text-muted small">
                                            Belum ada tagihan
                                        </span>

                                    @endif
                                    </div>
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="8"
                                    class="text-center py-5">

                                    <x-ui.empty-state title="Belum ada customer" description="Belum ada customer yang memiliki paket." />

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </x-ui.table>

            </div>

        </div>


       {{-- PAGINATION --}}
@if ($customers->hasPages())
    <div class="card-footer bg-white">
        <div class="payment-pagination">
           {{ $customers->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endif

    </div>

</div>

@endsection