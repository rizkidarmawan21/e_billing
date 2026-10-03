<?php

namespace App\Observers;

use App\Jobs\SyncToPrismJob;
use App\Models\Package;
use App\Models\SyncOutbox;
use App\Support\SyncCodec;
use Illuminate\Support\Facades\Log;

class PackageObserver
{
    public function saved(Package $package): void
    {
        $this->enqueue($package, $package->wasRecentlyCreated ? 'created' : 'updated');
    }

    public function deleted(Package $package): void
    {
        $this->enqueue($package, 'deleted');
    }

    private function enqueue(Package $package, string $event): void
    {
        if (!config('sync.enabled')) {
            return;
        }

        try {
            $row = SyncOutbox::create([
                'entity' => 'package',
                'event' => $event,
                'old_id' => $package->id,
                'event_id' => (string) \Illuminate\Support\Str::uuid(),
                'payload' => [
                    'id' => $package->id,
                    'nama_paket' => $package->nama_paket,
                    'kecepatan' => $package->kecepatan,
                    // decimal:2 cast → string "150000.00", sesuai
                    // format yang diparse Prism (parseRupiah).
                    'harga' => (string) $package->harga,
                    'deskripsi' => $package->deskripsi,
                    'code' => SyncCodec::packageCode($package->id, $package->nama_paket),
                    'updated_at' => $package->updated_at?->toISOString(),
                ],
            ]);
            SyncToPrismJob::dispatch($row->id);
        } catch (\Throwable $e) {
            // Sync tidak boleh menggagalkan operasional billing.
            Log::error('sync outbox: '.$e->getMessage());
        }
    }
}
