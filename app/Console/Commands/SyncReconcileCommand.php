<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Package;
use App\Models\Payment;
use App\Support\PrismPusher;
use Illuminate\Console\Command;

/**
 * Reconcile backstop: bandingkan count e_billing
 * (MySQL) dengan count per entity di ledger Prism
 * (GET /api/sync/stats). Drift = ada event sync
 * yang terlewat (misal queue worker mati).
 *
 * Jalankan manual sebelum cutover:
 *   php artisan sync:reconcile
 */
class SyncReconcileCommand extends Command
{
    protected $signature = 'sync:reconcile';

    protected $description = 'Bandingkan count e_billing vs Prism (drift detection)';

    public function handle(): int
    {
        if (! config('sync.enabled')) {
            $this->error('SYNC_ENABLED=false');

            return self::FAILURE;
        }

        try {
            $counts = PrismPusher::fromConfig()->stats();
        } catch (\Throwable $e) {
            $this->error('Gagal ambil stats dari Prism: '.$e->getMessage());

            return self::FAILURE;
        }

        $checks = [
            'packages' => [
                'e_billing' => Package::count(),
                'prism' => (int) ($counts['package'] ?? 0),
            ],
            'customers' => [
                'e_billing' => Customer::count(),
                'prism' => (int) ($counts['customer'] ?? 0),
            ],
            'tagihan (payments)' => [
                'e_billing' => Payment::count(),
                'prism' => (int) ($counts['payment'] ?? 0),
            ],
        ];

        $drift = 0;
        foreach ($checks as $name => $c) {
            $ok = $c['e_billing'] === $c['prism'];
            if (! $ok) {
                $drift++;
            }
            $this->line(sprintf(
                '%-28s e_billing: %-6d prism: %-6d %s',
                $name,
                $c['e_billing'],
                $c['prism'],
                $ok ? 'OK' : '<error>DRIFT</error>'
            ));
        }

        if ($drift > 0) {
            $this->warn("{$drift} tabel drift — cek queue worker (php artisan queue:failed)");

            return self::FAILURE;
        }

        $this->info('Tidak ada drift.');

        return self::SUCCESS;
    }
}
