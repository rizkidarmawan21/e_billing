@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                Edit Payment
            </h4>

            <p class="text-muted mb-0">
                Ubah data tagihan dan pembayaran customer.
            </p>
        </div>

        <a href="{{ route('payments.index') }}"
           class="btn btn-secondary">
            ← Kembali
        </a>

    </div>


    {{-- FORM --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                Data Tagihan
            </h5>
        </div>


        <div class="card-body">

            {{-- ERROR VALIDASI --}}
            @if ($errors->any())

                <div class="alert alert-danger">

                    <strong>Terjadi kesalahan:</strong>

                    <ul class="mb-0 mt-2">

                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach

                    </ul>

                </div>

            @endif


            <form action="{{ route('payments.update', $payment) }}"
                  method="POST">

                @csrf
                @method('PUT')


                {{-- CUSTOMER --}}
                <div class="mb-4">

                    <label class="form-label fw-semibold">
                        Customer
                    </label>

                    <input
                        type="text"
                        id="customer_search"
                        class="form-control"
                        value="{{ $payment->customer->nama ?? '' }} ({{ $payment->customer->kode_pelanggan ?? '' }})"
                        readonly
                    >

                    <input
                        type="hidden"
                        name="customer_id"
                        id="customer_id"
                        value="{{ $payment->customer_id }}"
                    >

                    <small class="text-muted">
                        Customer pada payment ini tidak diubah.
                    </small>

                </div>


                {{-- INFORMASI CUSTOMER --}}
                <div class="alert alert-light border mb-4">

                    <div class="row">

                        <div class="col-md-6">

                            <small class="text-muted">
                                Nama Customer
                            </small>

                            <div class="fw-semibold">
                                {{ $payment->customer->nama ?? '-' }}
                            </div>

                        </div>


                        <div class="col-md-6">

                            <small class="text-muted">
                                Kode Customer
                            </small>

                            <div class="fw-semibold">
                                {{ $payment->customer->kode_pelanggan ?? '-' }}
                            </div>

                        </div>

                    </div>

                </div>


                {{-- PAKET --}}
                <div class="mb-4">

                    <label class="form-label fw-semibold">
                        Paket Internet
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="{{ $payment->package->nama_paket ?? '-' }} - {{ $payment->package->kecepatan ?? '' }}"
                        readonly
                    >

                    <input
                        type="hidden"
                        name="package_id"
                        value="{{ $payment->package_id }}"
                    >

                    <small class="text-muted">
                        Paket mengikuti paket yang digunakan pada payment ini.
                    </small>

                </div>


                {{-- PERIODE & TAHUN --}}
                <div class="row">

                    {{-- PERIODE --}}
                    <div class="col-md-6 mb-4">

                        <label class="form-label fw-semibold">
                            Periode
                        </label>

                        <select
                            name="periode"
                            class="form-select"
                            required
                        >

                            <option value="">
                                -- Pilih Bulan --
                            </option>

                            @foreach ([
                                'Januari',
                                'Februari',
                                'Maret',
                                'April',
                                'Mei',
                                'Juni',
                                'Juli',
                                'Agustus',
                                'September',
                                'Oktober',
                                'November',
                                'Desember'
                            ] as $bulan)

                                <option
                                    value="{{ $bulan }}"
                                    {{ old('periode', $payment->periode) == $bulan ? 'selected' : '' }}
                                >
                                    {{ $bulan }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- TAHUN --}}
                    <div class="col-md-6 mb-4">

                        <label class="form-label fw-semibold">
                            Tahun
                        </label>

                        <input
                            type="number"
                            name="tahun"
                            class="form-control"
                            value="{{ old('tahun', $payment->tahun) }}"
                            min="2020"
                            max="2100"
                            required
                        >

                    </div>

                </div>


                {{-- JUMLAH TAGIHAN --}}
                <div class="mb-4">

                    <label class="form-label fw-semibold">
                        Jumlah Tagihan
                    </label>

                    <input
                        type="number"
                        name="jumlah_tagihan"
                        class="form-control"
                        value="{{ old('jumlah_tagihan', $payment->jumlah_tagihan) }}"
                        min="0"
                        required
                    >

                    <small class="text-muted">
                        Nominal tagihan dapat disesuaikan jika diperlukan.
                    </small>

                </div>


                {{-- STATUS --}}
                <div class="mb-4">

                    <label class="form-label fw-semibold">
                        Status Pembayaran
                    </label>

                    <select
                        name="status"
                        id="status"
                        class="form-select"
                        required
                    >

                        <option value="belum_bayar"
                            {{ old('status', $payment->status) == 'belum_bayar' ? 'selected' : '' }}>
                            Belum Lunas
                        </option>

                        <option value="lunas"
                            {{ old('status', $payment->status) == 'lunas' ? 'selected' : '' }}>
                            Lunas
                        </option>

                    </select>

                </div>


               {{-- TANGGAL BAYAR --}}
<div class="mb-4">

    <label class="form-label fw-semibold">
        Tanggal Bayar
        <span id="tanggal_required"
              class="text-danger"
              style="display: none;">
            *
        </span>
    </label>

    <input
        type="date"
        name="tanggal_bayar"
        id="tanggal_bayar"
        class="form-control"
        value="{{ old('tanggal_bayar', optional($payment->tanggal_bayar)->format('Y-m-d')) }}"
    >

    <small id="tanggal_help" class="text-muted">
        Tanggal bayar boleh dikosongkan jika customer belum melakukan pembayaran.
    </small>

</div>


                {{-- TOMBOL --}}
                <div class="d-flex gap-2">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        💾 Simpan Perubahan
                    </button>


                    <a
                        href="{{ route('payments.index') }}"
                        class="btn btn-secondary"
                    >
                        Batal
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

<script>

    const statusPembayaran = document.getElementById('status');
    const tanggalBayar = document.getElementById('tanggal_bayar');
    const tanggalRequired = document.getElementById('tanggal_required');
    const tanggalHelp = document.getElementById('tanggal_help');

    function updateTanggalBayar() {

        if (statusPembayaran.value === 'lunas') {

            tanggalBayar.required = true;

            tanggalRequired.style.display = 'inline';

            tanggalHelp.textContent =
                'Tanggal pembayaran wajib diisi karena status pembayaran Lunas.';

        } else {

            tanggalBayar.required = false;

            tanggalRequired.style.display = 'none';

            tanggalHelp.textContent =
                'Tanggal bayar boleh dikosongkan jika customer belum melakukan pembayaran.';

        }

    }

    statusPembayaran.addEventListener('change', updateTanggalBayar);

    updateTanggalBayar();

</script>

@endsection