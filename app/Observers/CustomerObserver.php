<?php

namespace App\Observers;

use App\Models\Customer;
use App\Models\Package;
use App\Models\SyncOutbox;
use App\Support\SyncCodec;
use Illuminate\Support\Facades\Log;

class CustomerObserver
{
    public function saved(Customer $customer): void
    {
        $this->enqueue($customer, $customer->wasRecentlyCreated ? 'created' : 'updated');
    }

    public function deleted(Customer $customer): void
    {
        $this->enqueue($customer, 'deleted');
    }

    private function enqueue(Customer $customer, string $event): void
    {
        if (!config('sync.enabled')) {
            return;
        }

        try {
            // package_id numerik tidak ditransfer — kirim kode
            // paket deterministik sebagai join key.
            $packageCode = null;
            if ($customer->package_id) {
                $package = Package::find($customer->package_id);
                $packageCode = $package
                    ? SyncCodec::packageCode($package->id, $package->nama_paket)
                    : null;
            }

            $row = SyncOutbox::create([
                'entity' => 'customer',
                'event' => $event,
                'old_id' => $customer->id,
                'event_id' => (string) \Illuminate\Support\Str::uuid(),
                'payload' => [
                    'id' => $customer->id,
                    'kode_pelanggan' => $customer->kode_pelanggan,
                    'nama' => $customer->nama,
                    'no_hp' => $customer->no_hp,
                    'alamat' => $customer->alamat,
                    'package_code' => $packageCode,
                    'created_at' => $customer->created_at?->toISOString(),
                    'updated_at' => $customer->updated_at?->toISOString(),
                ],
            ]);
            SyncToPrismJob::dispatch($row->id);
        } catch (\Throwable $e) {
            // Sync tidak boleh menggagalkan operasional billing.
            Log::error('sync outbox: '.$e->getMessage());
        }
    }
}
