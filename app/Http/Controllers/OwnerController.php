<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\Package;

class OwnerController extends Controller
{
    public function index(Request $request)
    {
        // ==========================================
        // DAFTAR BULAN
        // ==========================================

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
        $tahun = now()->year;


    // ======================================================
// FILTER DASHBOARD
// ======================================================

// ALL = semua data
$showAll = $request->boolean('all');

// Filter bulan dari form
$filterActive = $request->boolean('filter_active');

$selectedMonths = $request->input('months', []);

if (!is_array($selectedMonths)) {
    $selectedMonths = [$selectedMonths];
}

// Pastikan hanya nama bulan yang valid
$selectedMonths = array_values(
    array_intersect($selectedMonths, array_values($namaBulan))
);


// ======================================================
// QUERY PAYMENT
// ======================================================

$paymentQuery = Payment::query();


// Jika ALL dipilih
if ($showAll) {

    // Tidak ada filter bulan
    // Semua data ditampilkan

}


// Jika tidak ALL dan ada Filter Bulan
elseif ($filterActive && !empty($selectedMonths)) {

    $paymentQuery
        ->where('tahun', now()->year)
        ->whereIn('periode', $selectedMonths);

}


// Jika tidak ALL dan tidak ada Filter Bulan
else {

    // Default = bulan berjalan
    $paymentQuery
        ->where('tahun', now()->year)
        ->where('periode', $namaBulan[now()->month]);

}

        // ==========================================
        // DATA STATISTIK
        // ==========================================

        // TOTAL USER

        $totalUsers = Customer::count();

        $totalTagihan = (clone $paymentQuery)
            ->sum('jumlah_tagihan');

        $pendapatan = (clone $paymentQuery)
            ->where('status', 'lunas')
            ->sum('jumlah_tagihan');

     


// ==========================================
// STATUS PEMBAYARAN
// ==========================================

$sudahBayar = (clone $paymentQuery)
    ->where('status', 'lunas')
    ->distinct('customer_id')
    ->count('customer_id');

$belumBayar = (clone $paymentQuery)
    ->where('status', 'belum_bayar')
    ->distinct('customer_id')
    ->count('customer_id');

        $piutang = (clone $paymentQuery)
            ->where('status', 'belum_bayar')
            ->sum('jumlah_tagihan');

// ======================================================
// DATA GRAFIK PENDAPATAN PER BULAN
// ======================================================

$pendapatanPerBulan = [];

for ($bulan = 1; $bulan <= 12; $bulan++) {

    // ALL = tampilkan semua bulan
    if ($showAll) {

        $pendapatanPerBulan[] = Payment::query()
            ->where('tahun', now()->year)
            ->where('periode', $namaBulan[$bulan])
            ->where('status', 'lunas')
            ->sum('jumlah_tagihan');

    }

    // Filter bulan aktif dan ada bulan yang dipilih
    elseif ($filterActive && !empty($selectedMonths)) {

        if (in_array($namaBulan[$bulan], $selectedMonths)) {

            $pendapatanPerBulan[] = Payment::query()
                ->where('tahun', now()->year)
                ->where('periode', $namaBulan[$bulan])
                ->where('status', 'lunas')
                ->sum('jumlah_tagihan');

        } else {

            $pendapatanPerBulan[] = 0;

        }

    }

    // Default = bulan berjalan saja
    else {

        if ($bulan == now()->month) {

            $pendapatanPerBulan[] = Payment::query()
                ->where('tahun', now()->year)
                ->where('periode', $namaBulan[$bulan])
                ->where('status', 'lunas')
                ->sum('jumlah_tagihan');

        } else {

            $pendapatanPerBulan[] = 0;

        }
    }
}

// ======================================================
// DATA TUNGGAKAN CUSTOMER PER BULAN
// ======================================================

$tunggakanPerBulan = [];

for ($bulan = 1; $bulan <= 12; $bulan++) {

    $nama = $namaBulan[$bulan];

    // ALL: hitung tunggakan setiap bulan
    if ($showAll) {
        $jumlahTunggakan = Payment::where('tahun', now()->year)
            ->where('periode', $nama)
            ->where('status', 'belum_bayar')
            ->distinct('customer_id')
            ->count('customer_id');
    }

    // Filter bulan: hitung hanya bulan yang dipilih
    elseif ($filterActive && !empty($selectedMonths)) {
        if (in_array($nama, $selectedMonths)) {
            $jumlahTunggakan = Payment::where('tahun', now()->year)
                ->where('periode', $nama)
                ->where('status', 'belum_bayar')
                ->distinct('customer_id')
                ->count('customer_id');
        } else {
            $jumlahTunggakan = 0;
        }
    }

    // Default: hanya bulan berjalan
    else {
        if ($bulan == now()->month) {
            $jumlahTunggakan = Payment::where('tahun', now()->year)
                ->where('periode', $nama)
                ->where('status', 'belum_bayar')
                ->distinct('customer_id')
                ->count('customer_id');
        } else {
            $jumlahTunggakan = 0;
        }
    }

    $tunggakanPerBulan[] = $jumlahTunggakan;
}


        // ==========================================
        // STATUS PEMBAYARAN
        // ==========================================

      $statusPembayaran = [$sudahBayar, $belumBayar];

$tagihanVsPendapatan = [
    $totalTagihan,
    $pendapatan
];

$paketList = Package::orderBy('harga')->get();

$distribusiPaket = [];

foreach ($paketList as $paket) {
    $distribusiPaket[] = [
        'nama' => $paket->nama_paket,
        'jumlah' => Customer::where('package_id', $paket->id)->count(),
    ];
}

        // ==========================================
        // KIRIM DATA KE VIEW
        // ==========================================

        return view('owner.dashboard', compact(
            'totalUsers',
            'totalTagihan',
            'pendapatan',
            'sudahBayar',
            'belumBayar',
            'piutang',
            'filterActive',
            'selectedMonths',
            'namaBulan',
            'tahun',
            'pendapatanPerBulan',
            'tunggakanPerBulan',
            'statusPembayaran',
            'distribusiPaket',
            'tagihanVsPendapatan',
            'showAll'
        ));
    }
      public function laporan(Request $request)
{
    // ==========================================
    // DAFTAR BULAN
    // ==========================================

    $namaBulan = [
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
        'Desember',
    ];

    // ==========================================
    // FILTER
    // ==========================================

    $months = $request->input('months', []);

    if (!is_array($months)) {
        $months = [$months];
    }

    $months = array_values(
        array_intersect($months, $namaBulan)
    );

    $tahun = $request->input('tahun');

    $status = $request->input('status');


    // ==========================================
    // QUERY LAPORAN
    // ==========================================

    $search = $request->input('search');

$query = Payment::with(['customer', 'package']);

if (!empty($search)) {
    $query->whereHas('customer', function ($customerQuery) use ($search) {
        $customerQuery->where('nama', 'like', '%' . $search . '%')
            ->orWhere('kode_pelanggan', 'like', '%' . $search . '%');
    });
}

    // Filter bulan
    if (!empty($months)) {
        $query->whereIn('periode', $months);
    }


    // Filter tahun
    if (!empty($tahun)) {
        $query->where('tahun', $tahun);
    }


    // Filter status
    if (in_array($status, ['lunas', 'belum_bayar'])) {
        $query->where('status', $status);
    }

    // ==========================================
// RINGKASAN LAPORAN
// ==========================================

$totalTagihan = (clone $query)->sum('jumlah_tagihan');

$pendapatan = (clone $query)
    ->where('status', 'lunas')
    ->sum('jumlah_tagihan');

// ==========================================
// STATUS PEMBAYARAN CUSTOMER
// ==========================================

// Customer yang memiliki tagihan pada periode terpilih
$customerIds = (clone $paymentQuery)
    ->distinct()
    ->pluck('customer_id');

// Hitung customer yang semua tagihannya lunas
$sudahBayar = 0;
$belumBayar = 0;

foreach ($customerIds as $customerId) {
    $tagihanCustomer = (clone $paymentQuery)
        ->where('customer_id', $customerId);

    $adaTunggakan = (clone $tagihanCustomer)
        ->where('status', 'belum_bayar')
        ->exists();

    if ($adaTunggakan) {
        $belumBayar++;
    } else {
        $sudahBayar++;
    }
}

dd([
    'total_customer' => Customer::count(),
    'customer_dengan_tagihan' => $customerIds->count(),
    'jumlah_tagihan_lunas' => (clone $paymentQuery)
        ->where('status', 'lunas')
        ->count(),
    'jumlah_tagihan_belum_bayar' => (clone $paymentQuery)
        ->where('status', 'belum_bayar')
        ->count(),
    'customer_lunas' => $sudahBayar,
    'customer_belum_bayar' => $belumBayar,
]);

$piutang = (clone $query)
    ->where('status', 'belum_bayar')
    ->sum('jumlah_tagihan');

    // ==========================================
    // DATA LAPORAN
    // ==========================================

    $payments = $query
        ->latest()
        ->paginate(10)
        ->withQueryString();


    // ==========================================
    // DAFTAR TAHUN
    // ==========================================

    $tahunList = Payment::query()
        ->select('tahun')
        ->distinct()
        ->orderByDesc('tahun')
        ->pluck('tahun');


   return view('owner.laporan', compact(
    'payments',
    'namaBulan',
    'months',
    'tahun',
    'status',
    'search',
    'tahunList',
    'totalTagihan',
    'pendapatan',
    'sudahBayar',
    'belumBayar',
    'piutang' 
));
}
}