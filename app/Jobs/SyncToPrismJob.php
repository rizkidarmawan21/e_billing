<?php

namespace App\Jobs;

use App\Models\SyncOutbox;
use App\Support\PrismPusher;
use App\Support\PrismSyncException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Mengirim satu baris sync_outbox ke Prism Bill
 * via POST /api/sync/events (auth X-API-Key +
 * HMAC-SHA256 atas body persis).
 *
 * IDEMPOTEN: event_id stabil per entity
 * ("<entity>:<id_sumber>") — Prism upsert by
 * natural key + ledger sync_events. Retry
 * (queue tries=3) aman: konvergen, tidak
 * pernah duplikat.
 */
class SyncToPrismJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [10, 60, 300];

    public function __construct(public int $outboxId) {}

    public function handle(): void
    {
        $row = SyncOutbox::find($this->outboxId);
        if (! $row || $row->processed) {
            return;
        }

        if (! config('sync.enabled')) {
            // Jangan pernah tandai processed saat sync
            // dimatikan — itu akan kehilangan event.
            // Biarkan gagal & retry sampai diaktifkan.
            throw new \RuntimeException('sync belum diaktifkan (SYNC_ENABLED=false)');
        }

        // event_id stabil per entity — kunci idempotency
        // ledger Prism (sync_events). UUID di kolom
        // sync_outbox.event_id hanya identitas row.
        $event = [
            'event_id' => $row->entity.':'.$row->old_id,
            'entity' => $row->entity,
            'action' => $row->event,
            'data' => $row->payload,
        ];

        try {
            $results = PrismPusher::fromConfig()->push([$event]);

            $result = $results[0] ?? null;
            if (! is_array($result) || ! ($result['ok'] ?? false)) {
                $error = is_array($result)
                    ? ($result['error'] ?? 'tidak ada hasil dari Prism')
                    : 'tidak ada hasil dari Prism';
                throw new \RuntimeException($error);
            }

            $row->update(['processed' => true, 'last_error' => null]);
        } catch (PrismSyncException $e) {
            // 4xx/5xx dari Prism — retry lewat queue.
            $this->recordFailure($row, 'HTTP '.$e->httpStatus().': '.$e->getMessage());
            throw $e;
        } catch (\Throwable $e) {
            // Error validasi (misal customer belum ada di
            // Prism) juga retry — biasanya event pendahulu
            // belum sampai.
            $this->recordFailure($row, $e->getMessage());
            throw $e;
        }
    }

    private function recordFailure(SyncOutbox $row, string $message): void
    {
        $row->update([
            'attempts' => $row->attempts + 1,
            'last_error' => mb_substr($message, 0, 1000),
        ]);

        Log::warning('sync ke Prism gagal', [
            'outbox_id' => $row->id,
            'entity' => $row->entity,
            'old_id' => $row->old_id,
            'error' => $message,
        ]);
    }
}
