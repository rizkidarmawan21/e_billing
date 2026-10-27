<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Payment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
       // Tahun yang dipilih
    $tahun = $request->input('tahun', now()->year);

        // Bulan yang dipilih
        $bulan = $request->input('bulan');

        // ALL = menampilkan semua data
        $showAll = $request->boolean('all');

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

        $diagramLabels = array_values($namaBulan);

        /*
        |--------------------------------------------------------------------------
        | PERIODE YANG DIGUNAKAN
        |--------------------------------------------------------------------------
        */

       // Jika ALL dipilih, tidak menggunakan satu periode tertentu.
// Jika memilih bulan, gunakan bulan tersebut.
// Jika tidak memilih apa-apa, gunakan bulan sekarang.
$periodeDashboard = $bulan ?? $namaBulan[now()->month];


        /*
        |--------------------------------------------------------------------------
        | TOTAL USER
        |--------------------------------------------------------------------------
        */

        // User dihitung dari data Customer,
        // bukan dari jumlah payment.
        //
        // Customer yang belum memiliki paket tidak dimasukkan
        // karena belum termasuk customer billing.
        $totalUser = Customer::whereNotNull('package_id')->count();


        /*
        |--------------------------------------------------------------------------
        | DATA PAYMENT
        |--------------------------------------------------------------------------
        */

        $paymentQuery = Payment::where('tahun', $tahun);

        // Jika bukan ALL, gunakan bulan yang dipilih.
        // Jika tidak ada bulan yang dipilih, gunakan bulan sekarang.
        if (!$showAll) {
             $paymentQuery->where(
             'periode',
             $bulan ?? $namaBulan[now()->month]
             );
        }


        /*
        |--------------------------------------------------------------------------
        | TOTAL TAGIHAN
        |--------------------------------------------------------------------------
        */

        $totalTagihan = (clone $paymentQuery)
            ->sum('jumlah_tagihan');


        /*
        |--------------------------------------------------------------------------
        | STATUS CUSTOMER
        |--------------------------------------------------------------------------
        */

        // Ambil customer yang memiliki paket.
       $customers = Customer::whereNotNull('package_id')
    ->with([
        'payments' => function ($query) use ($tahun, $showAll, $periodeDashboard) {

            $query->where('tahun', $tahun);

            // Jika bukan ALL, gunakan periode yang dipilih
            if (!$showAll) {
                $query->where('periode', $periodeDashboard);
            }
        }
    ])
    ->get();


       $sudahBayar = 0;
$belumBayar = 0;

foreach ($customers as $customer) {

    if ($showAll) {

        // ALL: cek seluruh payment customer pada tahun tersebut
        $hasLunas = $customer->payments
            ->contains('status', 'lunas');

        $hasBelumBayar = $customer->payments
            ->contains('status', 'belum_bayar');

        if ($hasLunas) {
            $sudahBayar++;
        } elseif ($hasBelumBayar) {
            $belumBayar++;
        }

    } else {

        // Jika memilih bulan tertentu
        $payment = $customer->payments->first();

        if ($payment && $payment->status === 'lunas') {
            $sudahBayar++;
        } elseif ($payment && $payment->status === 'belum_bayar') {
            $belumBayar++;
        }
    }
}


        /*
        |--------------------------------------------------------------------------
        | DATA DIAGRAM PER BULAN
        |--------------------------------------------------------------------------
        */

        $tunggakanPerBulan = [];

        $tagihanPerBulan = [];

        $lunasPerBulan = [];

        $belumBayarPerBulan = [];


        foreach ($namaBulan as $nomorBulan => $nama) {

            // Jumlah customer yang memiliki tagihan
            // pada bulan tersebut.
           $tunggakanPerBulan[] = Payment::where('tahun', $tahun)
    ->where('periode', $nama)
    ->where('status', 'belum_bayar')
    ->distinct('customer_id')
    ->count('customer_id');


            // Total nominal tagihan pada bulan tersebut.
            $tagihanPerBulan[] = Payment::where('tahun', $tahun)
                ->where('periode', $nama)
                ->sum('jumlah_tagihan');


            // Jumlah customer yang sudah membayar
            // pada bulan tersebut.
            $lunasPerBulan[] = Payment::where('tahun', $tahun)
                ->where('periode', $nama)
                ->where('status', 'lunas')
                ->distinct('customer_id')
                ->count('customer_id');


            // Jumlah customer yang belum membayar
            // pada bulan tersebut.
            $belumBayarPerBulan[] = Payment::where('tahun', $tahun)
                ->where('periode', $nama)
                ->where('status', 'belum_bayar')
                ->distinct('customer_id')
                ->count('customer_id');
        }

        /*
|--------------------------------------------------------------------------
| DATA DIAGRAM
|--------------------------------------------------------------------------
*/

if ($showAll) {

    // ALL: tampilkan seluruh bulan
    $diagramLabels = array_values($namaBulan);
    $diagramData = $tunggakanPerBulan;

} else {

    // Bulan sekarang / bulan yang dipilih:
    // tampilkan hanya satu bulan
    $diagramLabels = [$periodeDashboard];

    $indexBulan = array_search($periodeDashboard, $namaBulan);

    $diagramData = [
        $tunggakanPerBulan[$indexBulan - 1] ?? 0
    ];
}

        /*
        |--------------------------------------------------------------------------
        | DAFTAR TAHUN
        |--------------------------------------------------------------------------
        */

        $tahunList = Payment::select('tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');


        // Kalau database belum memiliki pembayaran,
        // tetap tampilkan tahun sekarang.
        if ($tahunList->isEmpty()) {
            $tahunList = collect([now()->year]);
        }


        return view('dashboard', compact(
        'tahun',
        'bulan',
        'showAll',
        'periodeDashboard',
        'namaBulan',
        'diagramLabels',
        'diagramData',
        'totalUser',
        'totalTagihan',
        'sudahBayar',
        'belumBayar',
        'tunggakanPerBulan',
        'tagihanPerBulan',
        'lunasPerBulan',
        'belumBayarPerBulan',
        'tahunList'
    ));
    }
}