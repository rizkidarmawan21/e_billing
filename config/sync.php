<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Realtime sync ke Prism Bill
    |--------------------------------------------------------------------------
    |
    | enabled=false → observer tidak menulis outbox
    | (mode dev / sebelum Prism siap).
    |
    | prism_url      → base URL Prism di internal
    |                  network (tanpa slash akhir).
    | prism_api_key  → X-API-Key — HARUS sama dengan
    |                  SYNC_API_KEY di .env Prism.
    | prism_secret   → kunci HMAC-SHA256 body —
    |                  HARUS sama dengan SYNC_SECRET
    |                  di .env Prism.
    |
    */

    'enabled' => env('SYNC_ENABLED', false),

    'prism_url' => env('SYNC_PRISM_URL', ''),

    'prism_api_key' => env('SYNC_PRISM_API_KEY', ''),

    'prism_secret' => env('SYNC_PRISM_SECRET', ''),
];
