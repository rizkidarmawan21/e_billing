@extends('owner.layout')

@section('header', 'Tagihan Belum Dibayar')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h2 class="h4 mb-1">Tagihan Belum Dibayar</h2><p class="text-muted mb-0">Semua tagihan belum dibayar, terurut dari periode paling lama.</p></div>
        <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">Semua tagihan</a>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted">Jumlah tagihan</div><strong class="fs-4">{{ $count }}</strong></div></div></div>
        <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted">Total nominal</div><strong class="fs-4">Rp {{ number_format((float) $total, 0, ',', '.') }}</strong></div></div></div>
    </div>
    <form method="GET" action="{{ route('payments.unpaid') }}" class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-auto dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">Filter Bulan @if($filterActive && $selectedMonths)<span class="badge bg-primary ms-1">{{ count($selectedMonths) }}</span>@endif</button>
                    <div class="dropdown-menu p-3 shadow" style="width: 320px">
                        <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" name="filter_active" value="1" id="unpaid_filter_active" {{ $filterActive ? 'checked' : '' }} onchange="document.getElementById('unpaid_months').hidden = !this.checked"><label class="form-check-label fw-semibold" for="unpaid_filter_active">Aktifkan Filter Bulan</label></div>
                        <hr>
                        <div id="unpaid_months" @if(!$filterActive) hidden @endif><p class="small text-muted mb-2">Pilih satu atau beberapa bulan:</p><div class="row">@foreach($months as $month)<div class="col-6 mb-2"><div class="form-check"><input class="form-check-input" type="checkbox" name="months[]" value="{{ $month }}" id="unpaid_month_{{ $loop->iteration }}" {{ in_array($month, $selectedMonths) ? 'checked' : '' }}><label class="form-check-label" for="unpaid_month_{{ $loop->iteration }}">{{ $month }}</label></div></div>@endforeach</div><hr><div class="d-flex justify-content-end gap-2"><a href="{{ route('payments.unpaid') }}" class="btn btn-sm btn-light">Reset</a><button type="submit" class="btn btn-sm btn-primary">Terapkan</button></div></div>
                    </div>
                </div>
                <div class="col-md-2"><label class="form-label">Cari pelanggan</label><input class="form-control" aria-label="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Nama pelanggan"></div>
                <div class="col-auto"><button class="btn btn-primary">Terapkan</button> <a class="btn btn-outline-secondary" href="{{ route('payments.unpaid') }}">Reset</a></div>
            </div>@if($errors->any())<div class="text-danger mt-2">{{ $errors->first() }}</div>@endif
        </div>
    </form>
    <div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th class="px-3">Pelanggan</th><th>No. tagihan</th><th>Periode</th><th>Nominal</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
        @forelse($payments as $payment)
            <tr><td class="px-3"><strong>{{ $payment->customer->nama }}</strong><div class="small text-muted">{{ $payment->customer->kode_pelanggan }}</div></td><td>{{ $payment->id }}</td><td>{{ $payment->periode }} {{ $payment->tahun }}</td><td>Rp {{ number_format((float) $payment->jumlah_tagihan, 0, ',', '.') }}</td><td><span class="badge text-bg-warning">Belum dibayar</span></td><td class="text-nowrap"><a class="btn btn-sm btn-outline-primary" href="{{ route('payments.show', $payment) }}?{{ http_build_query(array_merge($filters, ['from_unpaid' => '1'])) }}">Detail</a> <a class="btn btn-sm btn-outline-secondary" href="{{ route('payments.edit', $payment) }}?{{ http_build_query(array_merge($filters, ['from_unpaid' => '1'])) }}">Edit</a> <form class="d-inline" method="POST" action="{{ route('payments.destroy', $payment) }}" onsubmit="return confirm('Hapus tagihan ini?')">@csrf @method('DELETE')<input type="hidden" name="from_unpaid" value="1">@foreach($filters as $key => $value) @if(is_array($value)) @foreach($value as $item)<input type="hidden" name="{{ $key }}[]" value="{{ $item }}">@endforeach @else <input type="hidden" name="{{ $key }}" value="{{ $value }}"> @endif @endforeach<button class="btn btn-sm btn-outline-danger">Hapus</button></form></td></tr>
        @empty
            <tr><td colspan="6" class="text-center py-5"><strong>Tidak ada tagihan belum dibayar</strong><div class="text-muted">Ubah filter atau tampilkan semua periode.</div><a class="btn btn-sm btn-outline-primary mt-2" href="{{ route('payments.unpaid') }}">Hapus filter</a></td></tr>
        @endforelse
    </tbody></table></div><div class="card-body">{{ $payments->links('pagination::simple-bootstrap-5') }}</div></div>
</div>

@endsection
