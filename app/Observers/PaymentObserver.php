<?php

namespace App\Observers;

use App\Jobs\SyncToPrismJob;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Payment;
use App\Models\SyncOutbox;
use App\Support\SyncCodec;
use Illuminate\Support\Facades\Log;

/**
 * PaymentObserver mengawasi tabel "payments" e_billing —
 * yang sebenarnya menyimpan TAGIHAN (periode + jumlah +
 * status bayar). Prism memisah invoices dan payments, jadi
 * event di sini menjadi satu invoice (dan satu payment row
 * ketika status lunas) di Prism.
 */
class PaymentObserver
{
    public function saved(Payment $payment): void
    {
        $this->enqueue($payment, $payment->wasRecentlyCreated ? 'created' : 'updated');
    }

    public function deleted(Payment $payment): void
    {
        $this->enqueue($payment, 'deleted');
    }

    private function enqueue(Payment $payment, string $event): void
    {
        if (!config('sync.enabled')) {
            return;
        }

        try {
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

            $row = SyncOutbox::create([
                'entity' => 'payment',
                'event' => $event,
                'old_id' => $payment->id,
                'event_id' => (string) \Illuminate\Support\Str::uuid(),
                'payload' => [
                    'id' => $payment->id,
                    'customer_code' => $customerCode,
                    'package_code' => $packageCode,
                    'periode' => $payment->periode,
                    'tahun' => (int) $payment->tahun,
                    'jumlah_tagihan' => (string) $payment->jumlah_tagihan,
                    'status' => $payment->status,
                    'tanggal_bayar' => $payment->tanggal_bayar?->format('Y-m-d'),
                    'updated_at' => $payment->updated_at?->toISOString(),
                ],
            ]);
            SyncToPrismJob::dispatch($row->id);
        } catch (\Throwable $e) {
            // Sync tidak boleh menggagalkan operasional billing.
            Log::error('sync outbox: '.$e->getMessage());
        }
    }
}
