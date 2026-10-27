<?php
namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Payment;
use Illuminate\Console\Command;

class GenerateMonthlyPayments extends Command
{
    protected $signature = 'app:generate-monthly-payments';

    protected $description = 'Membuat tagihan otomatis untuk bulan berjalan';

    public function handle()
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
        $jumlahDibuat = 0;

        Customer::with('package')
            ->whereNotNull('package_id')
            ->chunkById(100, function ($customers) use (
                $periode,
                $tahun,
                &$jumlahDibuat
            ) {
                foreach ($customers as $customer) {
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
            });

        $this->info(
            "Generate tagihan {$periode} {$tahun} selesai. "
            . "{$jumlahDibuat} tagihan baru dibuat."
        );

        return self::SUCCESS;
    }
}
