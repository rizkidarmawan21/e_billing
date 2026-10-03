<?php

namespace App\Support;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * Mengirim event sync ke Prism Bill via
 * POST /api/sync/events.
 *
 * Auth dua lapis: X-API-Key + X-Signature
 * (HMAC-SHA256 hex atas body PERSIS). Prism
 * memverifikasi signature atas byte body yang
 * diterima — jadi body di-encode JSON dulu,
 * baru di-sign (urutan key JSON stabil).
 *
 * Mengapa via API, bukan tulis langsung ke
 * Postgres Prism:
 * - Akses DB lintas platform bukan best practice
 * - Prism yang kontrol validasi + idempotency
 *   (ledger sync_events di sisi Prism)
 * - Endpoint generic — platform lain nanti
 *   bisa pakai endpoint yang sama
 */
class PrismPusher
{
    public function __construct(
        private string $baseUrl,
        private string $apiKey,
        private string $secret,
    ) {}

    public static function fromConfig(): self
    {
        return new self(
            rtrim((string) config('sync.prism_url'), '/'),
            (string) config('sync.prism_api_key'),
            (string) config('sync.prism_secret'),
        );
    }

    /**
     * Push satu batch event (maks 100 — batas
     * Prism). Mengembalikan hasil per event.
     *
     * @throws PrismSyncException
     */
    public function push(array $events): array
    {
        $body = json_encode(['events' => $events], JSON_UNESCAPED_UNICODE);
        if ($body === false) {
            throw new \RuntimeException('json encode gagal: '.json_last_error_msg());
        }

        $response = Http::withHeaders([
            'X-API-Key' => $this->apiKey,
            'X-Signature' => hash_hmac('sha256', $body, $this->secret),
        ])->withBody($body, 'application/json')
            ->post($this->baseUrl.'/api/sync/events');

        if ($response->failed()) {
            throw new PrismSyncException(
                'Prism sync gagal ['.$response->status().']: '.mb_substr((string) $response->body(), 0, 500),
                $response->status(),
            );
        }

        return $response->json('results') ?? [];
    }

    /**
     * Ambil count per entity dari ledger Prism
     * (GET /api/sync/stats) untuk reconcile.
     *
     * @throws PrismSyncException
     */
    public function stats(): array
    {
        $response = Http::withHeaders(['X-API-Key' => $this->apiKey])
            ->get($this->baseUrl.'/api/sync/stats');

        if ($response->failed()) {
            throw new PrismSyncException(
                'Prism stats gagal ['.$response->status().']: '.mb_substr((string) $response->body(), 0, 500),
                $response->status(),
            );
        }

        return $response->json('counts') ?? [];
    }
}

class PrismSyncException extends \RuntimeException
{
    public function __construct(string $message, private int $httpStatus)
    {
        parent::__construct($message);
    }

    public function httpStatus(): int
    {
        return $this->httpStatus;
    }
}
