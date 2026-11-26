@extends(Auth::user()->role === 'owner' ? 'owner.layout' : 'layouts.admin')

@section('header', 'Tagihan Belum Dibayar')

@section('content')
<div class="container-fluid py-3">
    <x-ui.page-header title="Tagihan Belum Dibayar" description="Semua tagihan belum dibayar, terurut dari periode paling lama.">
        <x-slot:actions>
            <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">Semua tagihan</a>
        </x-slot:actions>
    </x-ui.page-header>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted">Jumlah tagihan</div>
                    <strong class="fs-4">{{ $count }}</strong>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted">Total nominal</div>
                    <strong class="fs-4">Rp {{ number_format((float) $total, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>
    </div>
    <x-ui.customer-search :action="route('payments.unpaid')" :search="$filters['search'] ?? null" :show-reset="true">
        <x-slot:extraFilters>
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                        Filter Bulan
                        @if($filterActive && $selectedMonths)
                            <span class="badge bg-primary ms-1">{{ count($selectedMonths) }}</span>
                        @endif
                    </button>
                    <div class="dropdown-menu filter-menu p-3 shadow">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="filter_active" value="1" id="unpaid_filter_active" {{ $filterActive ? 'checked' : '' }} onchange="document.getElementById('unpaid_months').hidden = !this.checked">
                            <label class="form-check-label fw-semibold" for="unpaid_filter_active">Aktifkan Filter Bulan</label>
                        </div>
                        <hr>
                        <div id="unpaid_months" @if(!$filterActive) hidden @endif>
                            <p class="small text-muted mb-2">Pilih satu atau beberapa bulan:</p>
                            <div class="row">
                                @foreach($months as $month)
                                    <div class="col-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="months[]" value="{{ $month }}" id="unpaid_month_{{ $loop->iteration }}" {{ in_array($month, $selectedMonths) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="unpaid_month_{{ $loop->iteration }}">{{ $month }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <hr>
                            <x-ui.form-actions class="justify-content-end">
                                <a href="{{ route('payments.unpaid') }}" class="btn btn-sm btn-light">Reset</a>
                                <button type="submit" class="btn btn-sm btn-primary">Terapkan</button>
                            </x-ui.form-actions>
                        </div>
                    </div>
                </div>
        </x-slot:extraFilters>
        <x-slot:feedback>
            @if($errors->any())
                <p class="text-danger mb-0 mt-2">{{ $errors->first() }}</p>
            @endif
        </x-slot:feedback>
    </x-ui.customer-search>
    <div class="card border-0 shadow-sm">
        <div class="table-responsive" tabindex="0" role="region" aria-label="Tagihan belum dibayar">
            <x-ui.table>
                <thead class="table-light">
                    <tr>
                        <th class="text-center" scope="col">No.</th>
                        <th class="px-3">Pelanggan</th>
                        <th>No. tagihan</th>
                        <th>Periode</th>
                        <th class="text-end">Nominal</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td data-label="No.">{{ $payments->firstItem() + $loop->index }}</td>
                            <td data-label="Pelanggan" class="px-3">
                                <strong>{{ $payment->customer->nama }}</strong>
                                <div class="small text-muted">{{ $payment->customer->kode_pelanggan }}</div>
                            </td>
                            <td data-label="No. tagihan">{{ $payment->id }}</td>
                            <td data-label="Periode">{{ $payment->periode }} {{ $payment->tahun }}</td>
                            <td data-label="Nominal">Rp {{ number_format((float) $payment->jumlah_tagihan, 0, ',', '.') }}</td>
                            <td data-label="Status"><x-ui.payment-status :status="$payment->status" /></td>
                            <td data-label="Aksi">
                                <div class="record-actions">
                                    <x-ui.row-action action="detail" :href="route('payments.show', $payment).'?'.http_build_query(array_merge($filters, ['from_unpaid' => '1']))" />
                                    <x-ui.row-action action="edit" :href="route('payments.edit', $payment).'?'.http_build_query(array_merge($filters, ['from_unpaid' => '1']))" />
                                    <form class="d-inline" method="POST" action="{{ route('payments.destroy', $payment) }}" onsubmit="return confirm('Hapus tagihan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="from_unpaid" value="1">
                                        @foreach($filters as $key => $value)
                                            @if(is_array($value))
                                                @foreach($value as $item)
                                                    <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                                                @endforeach
                                            @else
                                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                            @endif
                                        @endforeach
                                        <x-ui.row-action action="delete" />
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <x-ui.empty-state title="Tidak ada tagihan belum dibayar" description="Ubah filter atau tampilkan semua periode.">
                                    <a class="btn btn-sm btn-outline-primary mt-2" href="{{ route('payments.unpaid') }}">Hapus filter</a>
                                </x-ui.empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.table>
        </div>
        <div class="card-body">{{ $payments->links('pagination::simple-bootstrap-5') }}</div>
    </div>
</div>

@endsection
