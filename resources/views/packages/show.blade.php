@extends(Auth::user()->role === 'owner' ? 'owner.layout' : 'layouts.admin')

@section('header', 'Detail Paket')

@section('content')
<div class="container-fluid">
    <x-ui.page-header title="Detail Paket" description="Informasi paket internet">
        <x-slot:actions>
            <a href="{{ route('packages.index') }}" class="btn btn-outline-secondary">← Kembali</a>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-3 p-md-4">
            <dl class="row g-3 mb-0">
                <dt class="col-sm-4 text-muted">Nama Paket</dt>
                <dd class="col-sm-8">{{ $package->nama_paket }}</dd>
                <dt class="col-sm-4 text-muted">Kecepatan</dt>
                <dd class="col-sm-8">{{ $package->kecepatan }}</dd>
                <dt class="col-sm-4 text-muted">Harga</dt>
                <dd class="col-sm-8 fw-semibold">Rp{{ number_format($package->harga, 0, ',', '.') }}</dd>
                <dt class="col-sm-4 text-muted">Deskripsi</dt>
                <dd class="col-sm-8">{{ $package->deskripsi ?? '-' }}</dd>
            </dl>

            <x-ui.form-actions class="border-top pt-3 mt-4">
                <a href="{{ route('packages.edit', $package) }}" class="btn btn-outline-secondary">Edit Paket</a>
                <form action="{{ route('packages.destroy', $package) }}" method="POST"
                    onsubmit="return confirm('Yakin ingin menghapus paket ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus Paket</button>
                </form>
            </x-ui.form-actions>
        </div>
    </div>
</div>
@endsection
