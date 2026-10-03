<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Package;
use App\Models\Payment;
use App\Support\PrismPusher;
use App\Support\SyncCodec;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Import satu kali semua data lama e_billing ke
 * Prism (baseline) — via POST /api/sync/events.
 *
 * Observer hanya menangkap perubahan BARU setelah
 * SYNC_ENABLED=true — data lama diimpor terpisah
 * dengan command ini.
 *
 * IDEMPOTEN: event_id stabil per entity + upsert
 * natural key di Prism, jadi command ini aman
 * di-rerun (misal setelah memperbaiki baris gagal).
 *
 * Urutan penting: packages → customers →
 * payments (tagihan butuh customer + package
 * sudah ada di Prism).
 */
class SyncBaselineCommand extends Command
{
    protected $signature = 'sync:baseline';

    protected $description = 'Import semua data lama e_billing ke Prism via API (idempoten)';

    public function handle(): int
    {
        if (! config('sync.enabled')) {
            $this->error('SYNC_ENABLED=false — aktifkan dulu di .env');

            return self::FAILURE;
        }

        $pusher = PrismPusher::fromConfig();
        $failed = 0;

        // 1. packages
        $this->info('Mengirim packages...');
        $events = Package::all()->map(fn ($package) => [
            'event_id' => 'package:'.$package->id,
            'entity' => 'package',
            'action' => 'created',
            'data' => [
                'id' => $package->id,
                'code' => SyncCodec::packageCode($package->id, $package->nama_paket),
                'nama_paket' => $package->nama_paket,
                'kecepatan' => $package->kecepatan,
                'harga' => (string) $package->harga,
                'deskripsi' => $package->deskripsi,
            ],
        ])->chunk(100);
        foreach ($events as $chunk) {
            $failed += $this->pushChunk($pusher, $chunk);
        }

        // 2. customers
        $this->info('Mengirim customers...');
        $events = Customer::all()->map(function ($customer) {
            $packageCode = null;
            if ($customer->package_id) {
                $package = Package::find($customer->package_id);
                $packageCode = $package
                    ? SyncCodec::packageCode($package->id, $package->nama_paket)
                    : null;
            }

            return [
                'event_id' => 'customer:'.$customer->id,
                'entity' => 'customer',
                'action' => 'created',
                'data' => [
                    'id' => $customer->id,
                    'kode_pelanggan' => $customer->kode_pelanggan,
                    'nama' => $customer->nama,
                    'no_hp' => $customer->no_hp,
                    'alamat' => $customer->alamat,
                    'package_code' => $packageCode,
                    'created_at' => optional($customer->created_at)->toISOString(),
                ],
            ];
        })->chunk(100);
        foreach ($events as $chunk) {
            $failed += $this->pushChunk($pusher, $chunk);
        }

        // 3. payments / tagihan
        $this->info('Mengirim tagihan...');
        $events = Payment::all()->map(function ($payment) {
            $customerCode = null;
            if ($payment->customer_id) {
                $customer = Customer::find($payment->customer_id);
                $customerCode = $customer?->kode_pelanggan;
            }

            $packageCode = null;
            if ($payment->package_id) {
                $package = Package::find($payment->package_id);
                $packageCode = $package
                    ? SyncCodec::packageCode($package->id, $package->nama_paket)
                    : null;
            }

            return [
                'event_id' => 'payment:'.$payment->id,
                'entity' => 'payment',
                'action' => 'created',
                'data' => [
                    'id' => $payment->id,
                    'customer_code' => $customerCode,
                    'package_code' => $packageCode,
                    'periode' => $payment->periode,
                    'tahun' => (int) $payment->tahun,
                    'jumlah_tagihan' => (string) $payment->jumlah_tagihan,
                    'status' => $payment->status,
                    'tanggal_bayar' => optional($payment->tanggal_bayar)->format('Y-m-d'),
                    'updated_at' => optional($payment->updated_at)->toISOString(),
                ],
            ];
        })->chunk(100);
        foreach ($events as $chunk) {
            $failed += $this->pushChunk($pusher, $chunk);
        }

        if ($failed > 0) {
            $this->warn("{$failed} event gagal — cek storage/logs/laravel.log, lalu re-run (idempoten).");

            return self::FAILURE;
        }

        $this->info('Baseline import selesai. Jalankan php artisan sync:reconcile untuk verifikasi.');

        return self::SUCCESS;
    }

    /**
     * Kirim satu chunk (maks 100 event). Mengembalikan
     * jumlah event yang gagal.
     */
    private function pushChunk(PrismPusher $pusher, $chunk): int
    {
        // chunk() preserve keys (array_chunk
        // dengan preserve_keys=true) — chunk ke-2+
        // dimulai dari key 100, dst. Reindex supaya
        // $chunk[0] dan $chunk[$i] valid di semua chunk.
        $chunk = $chunk->values();

        if ($chunk->isEmpty()) {
            return 0;
        }

        try {
            $results = $pusher->push($chunk->all());
        } catch (\Throwable $e) {
            // Gagal jaringan/5xx — seluruh chunk bisa
            // dikirim ulang saat command di-rerun.
            Log::error('baseline chunk gagal', [
                'entity' => $chunk[0]['entity'],
                'error' => $e->getMessage(),
            ]);

            return $chunk->count();
        }

        $failed = 0;
        foreach ($results as $i => $res) {
            if (! is_array($res) || ! ($res['ok'] ?? false)) {
                $failed++;
                Log::error('baseline event gagal', [
                    'event_id' => $chunk[$i]['event_id'] ?? '?',
                    'error' => is_array($res) ? ($res['error'] ?? 'unknown') : 'no result',
                ]);
            }
        }

        return $failed;
    }
}
