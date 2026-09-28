<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Customer;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
   /**
 * Menampilkan daftar tagihan bulan aktif.
 */
public function index(Request $request)
{
    $search = $request->search;

    $now = now();

    $namaBulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    // Bulan dan tahun yang sedang aktif
    $bulanAktif = $now->month;
    $periode = $namaBulan[$bulanAktif];
    $tahun = $now->year;

    $customers = Customer::with([
        'package',
        'payments' => function ($query) {
            $query->with('package')
                ->orderBy('tahun')
                ->orderBy('id');
        }
    ])
    ->when($search, function ($query, $search) {
        $query->where('nama', 'like', '%' . $search . '%');
    })
    ->whereNotNull('package_id')
    ->latest()
    ->paginate(10)
    ->withQueryString();

    foreach ($customers as $customer) {

        // Ambil payment yang periodenya tidak melebihi bulan aktif
        $paymentsSampaiBulanAktif = $customer->payments
            ->filter(function ($payment) use ($bulanAktif, $tahun, $namaBulan) {


                $bulanPayment = array_search(
                    $payment->periode,
                    $namaBulan
                );

                  if ($bulanPayment === false) {
                    return false;
                }

                 // Tahun sebelumnya tetap termasuk
                if ($payment->tahun < $tahun) {
                    return true;
                }

                 // Tahun setelah tahun aktif tidak termasuk
                if ($payment->tahun > $tahun) {
                    return false;
                }

                  // Tahun sama: hanya sampai bulan aktif
                return $bulanPayment <= $bulanAktif;
            });

            // Cari tunggakan paling lama
        $tunggakan = $paymentsSampaiBulanAktif
            ->where('status', 'belum_bayar')
            ->sortBy(function ($payment) use ($namaBulan) {

                $bulanPayment = array_search(
                    $payment->periode,
                    $namaBulan
                );

                return ($payment->tahun * 100)
                    + ($bulanPayment ?: 0);
            })
            ->first();

        if ($tunggakan) {

            // Jika ada tunggakan, tampilkan tunggakan paling lama
            $customer->currentPayment = $tunggakan;

        } else {

            // Jika tidak ada tunggakan,
            // tampilkan payment bulan aktif
            $customer->currentPayment = $paymentsSampaiBulanAktif
                ->first(function ($payment) use ($periode, $tahun) {

                    return $payment->periode === $periode
                        && $payment->tahun == $tahun;
                });
        }
    }

    return view('payments.index', compact(
        'customers',
        'search',
        'periode',
        'tahun'
    ));
}

    /**
     * Form tambah tagihan.
     */
    public function create()
{
    $customers = Customer::with('package')
        ->orderBy('nama')
        ->get();

    $packages = Package::orderBy('harga')->get();

    return view('payments.create', compact('customers', 'packages'));
}

    /**
     * Menyimpan tagihan.
     */
    public function store(Request $request)
    {
      $validated = $request->validate([
    'customer_id' => [
        'required',
        'exists:customers,id',
        Rule::unique('payments')->where(function ($query) use ($request) {
            return $query
                ->where('periode', $request->periode)
                ->where('tahun', $request->tahun);
        }),
    ],
    'package_id' => ['required', 'exists:packages,id'],
    'periode' => ['required', 'string', 'max:20'],
    'tahun' => ['required', 'integer', 'min:2020', 'max:2100'],
    'jumlah_tagihan' => ['required', 'numeric', 'min:0'],
    'status' => ['required', 'in:belum_bayar,lunas'],
    'tanggal_bayar' => ['nullable', 'date', 'required_if:status,lunas'],
]);

        Payment::create($validated);

        return redirect()
            ->route('payments.index')
            ->with('success', 'Tagihan berhasil ditambahkan.');
    }

    public function generateMonthly()
{
    $now = now();

    $namaBulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    $periode = $namaBulan[$now->month];
    $tahun = $now->year;

    $customers = Customer::with('package')
        ->orderBy('nama')
        ->get();

    $jumlahDibuat = 0;

    foreach ($customers as $customer) {

        // Customer yang belum memiliki paket dilewati
        if (!$customer->package) {
            continue;
        }

        $payment = Payment::firstOrCreate(
            [
                'customer_id' => $customer->id,
                'periode' => $periode,
                'tahun' => $tahun,
            ],
            [
                'package_id' => $customer->package_id,
                'jumlah_tagihan' => $customer->package->harga,
                'status' => 'belum_bayar',
                'tanggal_bayar' => null,
            ]
        );

        if ($payment->wasRecentlyCreated) {
            $jumlahDibuat++;
        }
    }

    return redirect()
        ->route('payments.index')
        ->with(
            'success',
            "Generate tagihan {$periode} {$tahun} berhasil. {$jumlahDibuat} tagihan baru dibuat."
        );
}
    /**
     * Menampilkan detail tagihan.
     */
    public function show(Payment $payment)
{
    $payment->load(['customer', 'package']);

    $customer = $payment->customer;

    $customer->load([
        'package',
        'payments' => function ($query) {
            $query->with('package')
                  ->orderByDesc('tahun')
                  ->orderByDesc('id');
        }
    ]);

    return view('payments.show', compact(
        'payment',
        'customer'
    ));
}

    /**
     * Form edit tagihan.
     */
    public function edit(Payment $payment)
    {
        $customers = Customer::orderBy('nama')->get();
        $packages = Package::orderBy('harga')->get();

        return view('payments.edit', compact(
            'payment',
            'customers',
            'packages'
        ));
    }

    /**
     * Mengupdate tagihan.
     */
    public function update(Request $request, Payment $payment)
    {
      $validated = $request->validate([
    'customer_id' => [
        'required',
        'exists:customers,id',
        Rule::unique('payments')
            ->where(function ($query) use ($request) {
                return $query
                    ->where('periode', $request->periode)
                    ->where('tahun', $request->tahun);
            })
            ->ignore($payment->id),
    ],
    'package_id' => ['required', 'exists:packages,id'],
    'periode' => ['required', 'string', 'max:20'],
    'tahun' => ['required', 'integer', 'min:2020', 'max:2100'],
    'jumlah_tagihan' => ['required', 'numeric', 'min:0'],
    'status' => ['required', 'in:belum_bayar,lunas'],
    'tanggal_bayar' => ['nullable', 'date', 'required_if:status,lunas'],
]);

        $payment->update($validated);

        return redirect()
            ->route('payments.index')
            ->with('success', 'Tagihan berhasil diperbarui.');
    }

    /**
     * Menghapus tagihan.
     */
    public function destroy(Payment $payment)
    {
        $payment->delete();

        return redirect()
            ->route('payments.index')
            ->with('success', 'Tagihan berhasil dihapus.');
    }
}