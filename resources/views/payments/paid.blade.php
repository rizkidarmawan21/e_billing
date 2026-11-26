@extends(Auth::user()->role === 'owner' ? 'owner.layout' : 'layouts.admin')

@section('header', 'Tagihan Lunas')

@section('content')
<div class="container-fluid py-3">
    <x-ui.page-header title="Tagihan Lunas" description="Daftar tagihan yang sudah dibayar, diurutkan dari pembayaran terbaru." />
    @if(session('success'))
        <div class="alert alert-success" role="status">{{ session('success') }}</div>
    @endif

    <x-ui.customer-search :action="route('payments.paid')" :search="$filters['search'] ?? null">
        <x-slot:feedback>
            @error('search')<p class="text-danger mb-0 mt-2">{{ $message }}</p>@enderror
        </x-slot:feedback>
    </x-ui.customer-search>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive" tabindex="0" role="region" aria-label="Daftar tagihan lunas">
            <x-ui.table>
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="text-center">No.</th>
                        <th scope="col">Pelanggan</th>
                        <th scope="col">No. tagihan</th>
                        <th scope="col">Periode</th>
                        <th scope="col" class="text-end">Nominal</th>
                        <th scope="col">Tanggal Bayar</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td data-label="No.">{{ $payments->firstItem() + $loop->index }}</td>
                            <td data-label="Pelanggan"><strong>{{ $payment->customer->nama }}</strong></td>
                            <td data-label="No. tagihan">{{ $payment->id }}</td>
                            <td data-label="Periode">{{ $payment->periode }} {{ $payment->tahun }}</td>
                            <td data-label="Nominal">Rp {{ number_format((float) $payment->jumlah_tagihan, 0, ',', '.') }}</td>
                            <td data-label="Tanggal Bayar">{{ $payment->tanggal_bayar?->format('d/m/Y') ?? '-' }}</td>
                            <td data-label="Status"><x-ui.payment-status :status="$payment->status" /></td>
                            <td data-label="Aksi">
                                <div class="record-actions">
                                    <x-ui.row-action action="detail" :href="route('payments.show', ['payment' => $payment, 'from_paid' => '1', 'page' => $payments->currentPage()] + $filters)" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-5">
                            <x-ui.empty-state title="Tidak ada tagihan lunas" :description="filled($filters['search'] ?? null) ? 'Coba nama pelanggan yang lain.' : 'Tagihan akan muncul di sini setelah dibayar.'" />
                        </td></tr>
                    @endforelse
                </tbody>
            </x-ui.table>
        </div>
        <div class="card-body">{{ $payments->links('pagination::simple-bootstrap-5') }}</div>
    </div>
</div>
@endsection
