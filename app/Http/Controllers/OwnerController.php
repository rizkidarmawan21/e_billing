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

        // ==========================================
        // FILTER BULAN
        // ==========================================

        $filterActive = $request->boolean('filter_active');

        $selectedMonths = $request->input('months', []);

        // Pastikan selalu berupa array
        if (!is_array($selectedMonths)) {
            $selectedMonths = [$selectedMonths];
        }

        // Hanya izinkan nama bulan yang tersedia
        $selectedMonths = array_values(
            array_intersect($selectedMonths, array_values($namaBulan))
        );

        // ==========================================
        // QUERY PAYMENT
        // ==========================================

        $paymentQuery = Payment::query();

        // Filter hanya diterapkan jika:
        // 1. Filter diaktifkan
        // 2. Ada bulan yang dipilih
        if ($filterActive && !empty($selectedMonths)) {
            $paymentQuery->whereIn('periode', $selectedMonths);
        }

        // ==========================================
        // DATA STATISTIK
        // ==========================================

        $totalUsers = Customer::count();

        $totalTagihan = (clone $paymentQuery)
            ->sum('jumlah_tagihan');

        $pendapatan = (clone $paymentQuery)
            ->where('status', 'lunas')
            ->sum('jumlah_tagihan');

        $sudahBayar = (clone $paymentQuery)
            ->where('status', 'lunas')
            ->count();

        $belumBayar = (clone $paymentQuery)
            ->where('status', 'belum_bayar')
            ->count();

        $piutang = (clone $paymentQuery)
            ->where('status', 'belum_bayar')
            ->sum('jumlah_tagihan');

        // ==========================================
        // DATA GRAFIK PENDAPATAN
        // ==========================================

        $pendapatanPerBulan = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {

            $query = Payment::where('tahun', now()->year)
                ->where('status', 'lunas');

            // Jika filter aktif dan bulan tersebut
            // tidak dipilih, nilainya dibuat 0
            if ($filterActive && !empty($selectedMonths)) {

                if (!in_array($namaBulan[$bulan], $selectedMonths)) {
                    $pendapatanPerBulan[] = 0;
                    continue;
                }
            }

            $pendapatanPerBulan[] = $query
                ->where('periode', $namaBulan[$bulan])
                ->sum('jumlah_tagihan');
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
            'pendapatanPerBulan',
            'statusPembayaran',
            'distribusiPaket',
            'tagihanVsPendapatan'
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

$sudahBayar = (clone $query)
    ->where('status', 'lunas')
    ->count();

$belumBayar = (clone $query)
    ->where('status', 'belum_bayar')
    ->count();

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