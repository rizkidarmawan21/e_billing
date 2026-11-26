@extends(Auth::user()->role === 'owner' ? 'owner.layout' : 'layouts.admin')

@section('header', 'Detail Payment')

@section('content')
<div class="container-fluid">
    <x-ui.page-header title="Tagihan #{{ $payment->id }}" description="{{ $payment->periode }} {{ $payment->tahun }}">
        <x-slot:actions>
            <a href="{{ $returnUrl }}" class="btn btn-outline-secondary">Kembali</a>
        </x-slot:actions>
    </x-ui.page-header>

    <section class="card border-0 shadow-sm" aria-labelledby="payment-details-title">
        <div class="card-body p-3 p-sm-4">
            <h2 id="payment-details-title" class="h5 fw-bold mb-3">Informasi Tagihan</h2>
            <dl class="row g-3 mb-0">
                <div class="col-sm-6">
                    <dt class="text-muted">Pelanggan</dt>
                    <dd class="mb-0">
                        <a href="{{ route('customers.show', $payment->customer) }}" class="fw-semibold">{{ $payment->customer->nama }}</a>
                        <div class="small text-muted">{{ $payment->customer->kode_pelanggan }}</div>
                    </dd>
                </div>
                <div class="col-sm-6">
                    <dt class="text-muted">Paket pada Tagihan</dt>
                    <dd class="mb-0">{{ $payment->package?->nama_paket ?? '-' }}</dd>
                </div>
                <div class="col-sm-6">
                    <dt class="text-muted">Nominal Tagihan</dt>
                    <dd class="fw-bold text-primary mb-0">Rp {{ number_format($payment->jumlah_tagihan, 0, ',', '.') }}</dd>
                </div>
                <div class="col-sm-6">
                    <dt class="text-muted">Status</dt>
                    <dd class="mb-0"><x-ui.payment-status :status="$payment->status" /></dd>
                </div>
                <div class="col-sm-6">
                    <dt class="text-muted">Tanggal Bayar</dt>
                    <dd class="mb-0">{{ $payment->tanggal_bayar?->format('d-m-Y') ?? '-' }}</dd>
                </div>
            </dl>

            @php($actionParams = ['payment' => $payment] + request()->only(['from_customer', 'history_page', 'from_unpaid', 'from_paid', 'filter_active', 'months', 'search', 'page']))
            <x-ui.form-actions class="border-top pt-3">
                <a href="{{ route('payments.edit', $actionParams) }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center">Edit Payment</a>
                <form action="{{ route('payments.destroy', $actionParams) }}" method="POST"
                      onsubmit="return confirm('Yakin ingin menghapus tagihan #{{ $payment->id }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger d-inline-flex align-items-center justify-content-center">Hapus Payment</button>
                </form>
            </x-ui.form-actions>
        </div>
    </section>
</div>
@endsection