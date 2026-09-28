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
        // Kalau belum memilih, otomatis menggunakan tahun sekarang.
        $tahun = $request->input('tahun', now()->year);

        // Bulan yang dipilih.
        // Null berarti menggunakan bulan aktif sekarang.
        $bulan = $request->input('bulan');

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


        /*
        |--------------------------------------------------------------------------
        | PERIODE YANG DIGUNAKAN
        |--------------------------------------------------------------------------
        */

        // Jika user memilih bulan dari filter,
        // gunakan bulan tersebut.
        //
        // Jika tidak memilih bulan,
        // gunakan bulan aktif sekarang.
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

        if ($bulan) {
            $paymentQuery->where('periode', $bulan);
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
                'payments' => function ($query) use ($tahun, $periodeDashboard) {
                    $query
                        ->where('tahun', $tahun)
                        ->where('periode', $periodeDashboard);
                }
            ])
            ->get();


        $sudahBayar = 0;
        $belumBayar = 0;


        foreach ($customers as $customer) {

            // Payment customer pada periode dashboard
            $payment = $customer->payments->first();

            // Jika ada payment dan statusnya lunas
            if ($payment && $payment->status === 'lunas') {

                $sudahBayar++;

            }

            // Jika ada payment tetapi belum lunas
            elseif ($payment && $payment->status === 'belum_bayar') {

                $belumBayar++;

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
            'namaBulan',
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