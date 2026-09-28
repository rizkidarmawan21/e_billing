@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Tambah Payment</h4>
            <p class="text-muted mb-0">
                Tambahkan tagihan pembayaran customer.
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
            <h5 class="mb-0">Data Tagihan</h5>
        </div>

        <div class="card-body">

            {{-- ERROR --}}
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


            <form action="{{ route('payments.store') }}"
                  method="POST">

                @csrf


                {{-- CUSTOMER --}}
                <div class="mb-4">

                    <label class="form-label fw-semibold">
                        Customer
                    </label>

                    <input
                        type="text"
                        id="customer_search"
                        class="form-control"
                        placeholder="Ketik nama atau kode customer..."
                        autocomplete="off"
                    >

                    <input
                        type="hidden"
                        name="customer_id"
                        id="customer_id"
                        value="{{ old('customer_id') }}"
                    >

                    {{-- HASIL PENCARIAN --}}
                    <div
                        id="customer_results"
                        class="list-group mt-1 shadow-sm"
                        style="display: none; max-height: 220px; overflow-y: auto;"
                    >
                    </div>

                    <small class="text-muted">
                        Ketik nama atau kode customer untuk mencari.
                    </small>

                </div>


                {{-- INFO CUSTOMER --}}
                <div
                    id="customer_info"
                    class="alert alert-light border mb-4"
                    style="display: none;"
                >

                    <div class="row">

                        <div class="col-md-6">
                            <small class="text-muted">
                                Nama Customer
                            </small>

                            <div id="selected_customer_name"
                                 class="fw-semibold">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted">
                                Kode Customer
                            </small>

                            <div id="selected_customer_code"
                                 class="fw-semibold">
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
                        id="package_display"
                        class="form-control"
                        value=""
                        placeholder="Paket akan otomatis muncul setelah customer dipilih"
                        readonly
                    >

                    <input
                        type="hidden"
                        name="package_id"
                        id="package_id"
                        value="{{ old('package_id') }}"
                    >

                </div>


                {{-- PERIODE DAN TAHUN --}}
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
                                    {{ old('periode') == $bulan ? 'selected' : '' }}
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
                            value="{{ old('tahun', date('Y')) }}"
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
                        id="jumlah_tagihan"
                        class="form-control"
                        value="{{ old('jumlah_tagihan') }}"
                        placeholder="Jumlah tagihan otomatis dari harga paket"
                        min="0"
                        required
                    >

                    <small class="text-muted">
                        Nominal otomatis mengikuti harga paket customer.
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
                            {{ old('status', 'belum_bayar') == 'belum_bayar' ? 'selected' : '' }}>
                            Belum Lunas
                        </option>

                        <option value="lunas"
                            {{ old('status') == 'lunas' ? 'selected' : '' }}>
                            Lunas
                        </option>

                    </select>

                </div>


                {{-- TANGGAL BAYAR --}}
                <div class="mb-4">
    <label class="form-label fw-semibold">
        Tanggal Bayar
        <span id="tanggal_required" class="text-danger" style="display: none;">*</span>
    </label>

    <input type="date"
           name="tanggal_bayar"
           id="tanggal_bayar"
           class="form-control"
           value="{{ old('tanggal_bayar') }}">

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
                        💾 Simpan Payment
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


{{-- JAVASCRIPT --}}
<script>

    const customers = @json($customers);

    const customerSearch = document.getElementById('customer_search');
    const customerResults = document.getElementById('customer_results');

    const customerId = document.getElementById('customer_id');

    const customerInfo = document.getElementById('customer_info');
    const selectedCustomerName = document.getElementById('selected_customer_name');
    const selectedCustomerCode = document.getElementById('selected_customer_code');

    const packageId = document.getElementById('package_id');
    const packageDisplay = document.getElementById('package_display');

    const jumlahTagihan = document.getElementById('jumlah_tagihan');


    // ==========================================
    // PENCARIAN CUSTOMER
    // ==========================================

    customerSearch.addEventListener('input', function () {

        const keyword = this.value.toLowerCase().trim();

        customerResults.innerHTML = '';

        if (keyword.length === 0) {

            customerResults.style.display = 'none';

            return;
        }


        const filteredCustomers = customers.filter(customer => {

            const nama = (customer.nama ?? '').toLowerCase();
            const kode = (customer.kode_pelanggan ?? '').toLowerCase();

            return nama.includes(keyword) || kode.includes(keyword);

        });


        if (filteredCustomers.length === 0) {

            customerResults.innerHTML = `
                <div class="list-group-item text-muted">
                    Customer tidak ditemukan.
                </div>
            `;

            customerResults.style.display = 'block';

            return;
        }


        filteredCustomers.forEach(customer => {

            const button = document.createElement('button');

            button.type = 'button';

            button.className =
                'list-group-item list-group-item-action';

            const packageName =
                customer.package
                    ? customer.package.nama_paket
                    : 'Belum memiliki paket';

            button.innerHTML = `
                <div class="fw-semibold">
                    ${customer.nama}
                </div>

                <div class="small text-muted">
                    ${customer.kode_pelanggan}
                    • ${packageName}
                </div>
            `;


            button.addEventListener('click', function () {

                pilihCustomer(customer);

            });


            customerResults.appendChild(button);

        });


        customerResults.style.display = 'block';

    });


    // ==========================================
    // PILIH CUSTOMER
    // ==========================================

    function pilihCustomer(customer) {

        customerSearch.value =
            customer.nama + ' (' + customer.kode_pelanggan + ')';

        customerId.value = customer.id;


        // Tampilkan informasi customer
        selectedCustomerName.textContent =
            customer.nama;

        selectedCustomerCode.textContent =
            customer.kode_pelanggan;

        customerInfo.style.display = 'block';


        // ======================================
        // PAKET CUSTOMER
        // ======================================

        if (customer.package) {

            packageId.value =
                customer.package.id;

            packageDisplay.value =
                customer.package.nama_paket +
                ' - ' +
                customer.package.kecepatan;


            // ==================================
            // HARGA PAKET
            // ==================================

            jumlahTagihan.value =
                customer.package.harga;

        } else {

            packageId.value = '';

            packageDisplay.value =
                'Customer belum memiliki paket';

            jumlahTagihan.value = '';

        }


        customerResults.style.display = 'none';

    }


    // ==========================================
    // KLIK DI LUAR HASIL PENCARIAN
    // ==========================================

    document.addEventListener('click', function (event) {

        if (
            !customerSearch.contains(event.target) &&
            !customerResults.contains(event.target)
        ) {

            customerResults.style.display = 'none';

        }

    });
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