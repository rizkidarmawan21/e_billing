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
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <p class="text-muted mb-0">
                Kelola tagihan dan pembayaran customer.
            </p>

        </div>


        <div class="d-flex gap-2">

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

        </div>

    </div>


    {{-- SEARCH --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <form action="{{ route('payments.index') }}"
                  method="GET">

                <div class="row g-2">

                    <div class="col-md-6">

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Cari nama customer..."
                            value="{{ $search ?? '' }}"
                        >

                    </div>


                    <div class="col-auto">

                        <button type="submit"
                                class="btn btn-primary">

                            🔍 Cari

                        </button>

                    </div>


                    @if (!empty($search))

                        <div class="col-auto">

                            <a href="{{ route('payments.index') }}"
                               class="btn btn-secondary">

                                Reset

                            </a>

                        </div>

                    @endif

                </div>

            </form>

        </div>

    </div>


    {{-- TABEL PAYMENT --}}
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
                                <td class="px-4">

                                    {{ $customers->firstItem() + $loop->index }}

                                </td>


                                {{-- CUSTOMER --}}
                                <td>

                                    <strong>
                                        {{ $customer->nama }}
                                    </strong>

                                    <div class="small text-muted">

                                        {{ $customer->kode_pelanggan }}

                                    </div>

                                </td>


                                {{-- PAKET --}}
                                <td>

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
                              <td>
    @if ($payment)
        {{ $payment->periode }}
        {{ $payment->tahun }}
    @else
        <span class="text-muted">-</span>
    @endif
</td>


                                {{-- TAGIHAN --}}
                                <td>

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
                                <td>

                                    @if ($payment && $payment->status === 'lunas')

                                        <span class="badge bg-success px-3 py-2">

                                            ✓ Lunas

                                        </span>

                                    @else

                                        <span class="badge bg-danger px-3 py-2">

                                            ✕ Belum Lunas

                                        </span>

                                    @endif

                                </td>


                                {{-- TANGGAL BAYAR --}}
                                <td>

                                    @if ($payment && $payment->tanggal_bayar)

                                        {{ $payment->tanggal_bayar->format('d-m-Y') }}

                                    @else

                                        <span class="text-muted">
                                            -
                                        </span>

                                    @endif

                                </td>


                                {{-- AKSI --}}
                                <td class="text-center">

                                    @if ($payment)

                                        {{-- DETAIL --}}
                                        <a href="{{ route('payments.show', $payment) }}"
                                           class="btn btn-sm btn-info text-white me-1">

                                            Detail

                                        </a>


                                        {{-- EDIT --}}
                                        <a href="{{ route('payments.edit', $payment) }}"
                                           class="btn btn-sm btn-warning me-1">

                                            Edit

                                        </a>


                                        {{-- HAPUS --}}
                                        <form action="{{ route('payments.destroy', $payment) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Yakin ingin menghapus pembayaran ini?');">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-danger">

                                                Hapus

                                            </button>

                                        </form>

                                    @else

                                        <span class="text-muted small">
                                            Belum ada tagihan
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="8"
                                    class="text-center py-5">

                                    <div class="text-muted">

                                        <div class="fs-1 mb-3">
                                            💳
                                        </div>

                                        <h5>
                                            Belum ada customer
                                        </h5>

                                        <p class="mb-3">
                                            Belum ada customer yang memiliki paket.
                                        </p>

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- PAGINATION --}}
        @if ($customers->hasPages())

            <div class="card-footer bg-white">

                {{ $customers->links() }}

            </div>

        @endif

    </div>

</div>

@endsection