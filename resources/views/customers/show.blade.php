@extends(Auth::user()->role === 'owner' ? 'owner.layout' : 'layouts.admin')

@section('header', 'Detail Customer')

@section('content')
<div class="container-fluid">
    <x-ui.page-header title="Profil Pelanggan" description="Detail informasi pelanggan">
        <x-slot:actions>
            <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">Kembali</a>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-8">
                    <h2 class="h5 fw-bold mb-3">Informasi Pelanggan</h2>
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted">Kode Pelanggan</dt>
                        <dd class="col-sm-8 fw-semibold">{{ $customer->kode_pelanggan }}</dd>
                        <dt class="col-sm-4 text-muted">Nama</dt>
                        <dd class="col-sm-8 fw-semibold">{{ $customer->nama }}</dd>
                        <dt class="col-sm-4 text-muted">No. HP</dt>
                        <dd class="col-sm-8">{{ $customer->no_hp ?? '-' }}</dd>
                        <dt class="col-sm-4 text-muted">Alamat</dt>
                        <dd class="col-sm-8">{{ $customer->alamat ?? '-' }}</dd>
                    </dl>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light border-0 h-100">
                        <div class="card-body">
                            <h2 class="h5 fw-bold mb-3">Paket Internet</h2>
                            @if($customer->package)
                                <p class="fw-semibold mb-1">{{ $customer->package->nama_paket }}</p>
                                <p class="text-muted mb-2">{{ $customer->package->kecepatan }}</p>
                                <p class="text-primary fw-bold mb-0">Rp {{ number_format($customer->package->harga, 0, ',', '.') }}</p>
                                @if($customer->package->deskripsi)
                                    <p class="small text-muted mt-3 mb-0">{{ $customer->package->deskripsi }}</p>
                                @endif
                            @else
                                <p class="text-muted mb-0">Pelanggan belum memiliki paket.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <x-ui.form-actions class="border-top mt-4 pt-4">
                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline-secondary">Edit Data</a>
                <form action="{{ route('customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pelanggan ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus Pelanggan</button>
                </form>
            </x-ui.form-actions>
        </div>
    </div>

    <section id="riwayat-tagihan" class="card border-0 shadow-sm mt-4" aria-labelledby="riwayat-tagihan-title">
        <div class="card-header bg-light">
            <h2 id="riwayat-tagihan-title" class="h5 fw-bold mb-1">Riwayat Tagihan</h2>
            <p class="small text-muted mb-0">Tagihan pelanggan, dari periode terbaru.</p>
        </div>
        @if(session('success'))
            <div class="alert alert-success m-3 mb-0" role="status">{{ session('success') }}</div>
        @endif
        @if($payments->isNotEmpty())
            <div class="table-responsive" tabindex="0" role="region" aria-label="Daftar riwayat tagihan">
                <x-ui.table>
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="text-center">No.</th>
                            <th scope="col">Periode</th>
                            <th scope="col">Paket</th>
                            <th scope="col" class="text-end">Nominal</th>
                            <th scope="col">Status</th>
                            <th scope="col">Tanggal Bayar</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            @php($context = ['payment' => $payment, 'from_customer' => '1', 'history_page' => $payments->currentPage()])
                            <tr>
                                <td data-label="No.">{{ $payments->firstItem() + $loop->index }}</td>
                                <td data-label="Periode"><strong>{{ $payment->periode }} {{ $payment->tahun }}</strong></td>
                                <td data-label="Paket"><span class="package-name">{{ $payment->package?->nama_paket ?? '-' }}</span></td>
                                <td data-label="Nominal">Rp {{ number_format($payment->jumlah_tagihan, 0, ',', '.') }}</td>
                                <td data-label="Status"><x-ui.payment-status :status="$payment->status" /></td>
                                <td data-label="Tanggal Bayar">{{ $payment->tanggal_bayar?->format('d-m-Y') ?? '-' }}</td>
                                <td data-label="Aksi">
                                    <div class="record-actions">
                                        <x-ui.row-action action="detail" :href="route('payments.show', $context)" />
                                        <x-ui.row-action action="edit" :href="route('payments.edit', $context)" />
                                        <form action="{{ route('payments.destroy', $context) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus tagihan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.row-action action="delete" />
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-ui.table>
            </div>
            @if($payments->hasPages())
                <div class="card-body payment-pagination">{{ $payments->links('pagination::bootstrap-5') }}</div>
            @endif
        @elseif($payments->total() > 0)
            <x-ui.empty-state title="Tidak ada tagihan di halaman ini">
                <a class="btn btn-outline-secondary" href="{{ $payments->url(1) }}">Ke halaman pertama</a>
            </x-ui.empty-state>
        @else
            <x-ui.empty-state title="Belum ada riwayat tagihan" description="Tagihan yang dibuat untuk pelanggan ini akan tampil di sini." />
        @endif
    </section>
</div>
@endsection
