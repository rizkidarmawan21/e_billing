# Realtime sync e_billing → Prism Bill

e_billing (Laravel/MySQL) tetap **system of truth** sampai cutover.
Prism (Go/Postgres) menerima data secara realtime, tanpa perubahan
kode di sisi e_billing yang menyentuh schema Prism.

## Arsitektur

```
e_billing                              Prism
─────────────────────────            ─────────────────────────
Model saved/deleted                  POST /api/sync/events
  → Observer (transaction sama)        ├ SyncKeyMiddleware (X-API-Key
  → sync_outbox row                     │  + HMAC-SHA256 atas body persis)
  → SyncToPrismJob (queue)            → Handler.Ingest
  → HTTP POST + HMAC                  ├ per-event transaction:
                                       │   upsert by natural key
  GET /api/sync/stats ◄───────────────┤   + ledger sync_events
  (reconcile)                          GET /api/sync/stats (count per entity)
```

**Mengapa via API, bukan akses DB langsung ke Postgres Prism:**
akses DB lintas platform bukan best practice untuk sync jangka
panjang. Prism yang kontrol validasi + idempotency (ledger
`sync_events` di sisi Prism). Endpoint ini **generic** — platform
lain nanti bisa pakai endpoint yang sama tanpa ubah Prism lagi.

**Mengapa bukan API invoice Prism yang existing:** `invoice.Service.Create`
menurunkan amount dari harga paket SAAT INI — tagihan historis
(`jumlah_tagihan`) tidak bisa di-preserve via API itu. Endpoint
sync menulis via GORM langsung (bukan domain service) supaya
amount historis persis.

## Auth

Dua lapis, fail-closed (belum dikonfigurasi → 503):

| Lapis | Header | Sisi Prism |
|---|---|---|
| API key | `X-API-Key` | `SYNC_API_KEY` (constant-time compare) |
| Signature | `X-Signature` | HMAC-SHA256 hex atas **byte body persis**, key = `SYNC_SECRET` |

`GET /api/sync/stats` hanya pakai `X-API-Key` (GET tidak ada body
yang di-sign).

## Payload (event_id stabil per entity)

`event_id = "<entity>:<id_e_billing>"` — kunci idempotency ledger
Prism. Delivery at-least-once aman: re-delivery upsert ke baris
yang sama (konvergen, tidak pernah duplikat).

| entity | field |
|---|---|
| `package` | `code` (deterministik, lihat SyncCodec), `nama_paket`, `kecepatan`, `harga` (string decimal "150000.00"), `deskripsi` |
| `customer` | `kode_pelanggan`, `nama`, `no_hp`, `alamat`, `package_code`, `created_at` |
| `payment` | `id`, `customer_code`, `package_code`, `periode` (nama bulan Indonesia), `tahun`, `jumlah_tagihan`, `status` (`lunas`/`belum_lunas`), `tanggal_bayar`, `updated_at` |

Normalisasi terjadi **di sisi Prism** (kode customer uppercase,
telepon 62XXX, rupiah dibulatkan ke integer, periode → tanggal
awal/akhir bulan) — sesuai aturan domain Prism sendiri.

## Mapping data

- **package** → `packages` (code = 12 char nama + 3 digit id; delete = `is_active=false`, never hard delete)
- **customer** → `customers` (delete = `status=inactive`)
- **payment** → `invoices` dengan `invoice_number = EB<id>` + `payments` row
  (`reference_no = "ebilling:<id>"`) ketika `lunas`. Invoice `unpaid`
  → `paid` saat status flip; delete invoice = `status=cancelled`.
- **amount**: `jumlah_tagihan` decimal(12,2) string → int64 rupiah
  (sen dibulatkan half-up). **Presisi historis terjamin** — ini
  alasan utama sync bypass domain service Prism.

## Outbox

`sync_outbox` (MySQL, e_billing) — ditulis di **transaction yang
sama** dengan data sumber, jadi tidak ada event yang hilang.
Kolom `event_id` di outbox = UUID identitas row; event_id yang
dikirim ke Prism (`entity:old_id`) dihitung di `SyncToPrismJob`.
Queue worker: `php artisan queue:work database` (proses terpisah
di container). Job: `tries=3`, backoff 10s/60s/300s.

## Command

```bash
# Import data lama satu kali (idempoten — aman di-rerun)
php artisan sync:baseline
# Urutan: packages → customers → payments (batch 100/event)

# Reconcile: bandingkan count e_billing vs ledger Prism
php artisan sync:reconcile
# Harusnya: 0 drift
```

## Deploy

**Sisi Prism** (`.env`):
```
SYNC_API_KEY=<acak-32-byte>
SYNC_SECRET=<acak-32-byte>
SYNC_TENANT_ID=<id tenant tujuan>
```

**Sisi e_billing** (`.env`):
```
SYNC_ENABLED=true
SYNC_PRISM_URL=http://prism-internal:8080
SYNC_PRISM_API_KEY=<sama dengan SYNC_API_KEY Prism>
SYNC_PRISM_SECRET=<sama dengan SYNC_SECRET Prism>
QUEUE_CONNECTION=database
```

Urutan: create tenant di Prism → set env Prism → set env e_billing →
rebuild e_billing → `queue:work database` → `sync:baseline` →
`sync:reconcile` (nol drift) → operasional normal.

**Cutover**: freeze e_billing, jalankan `sync:reconcile` terakhir,
pindah user ke Prism. Sync bisa dimatikan setelah cutover
(`SYNC_ENABLED=false`) — tidak ada schema Prism yang perlu dibersihkan
(ledger `sync_events` bisa dibiarkan atau di-drop).

## Batas yang diketahui

- Invoice yang di-import **tidak** mengisi `invoice_status_history`
  Prism (log itu hanya terisi untuk perubahan status yang terjadi
  DI DALAM Prism) — kosmetik, tidak mempengaruhi data.
- `sync:reconcile` membandingkan **count per entity** (drift
  detection), bukan per-baris. Untuk verifikasi per-baris, bandingkan
  manual via dashboard Prism.
- Satu API key = satu tenant (config-based). Multi-platform/
  multi-tenant nanti bisa berevolusi ke tabel API key — endpoint
  tidak perlu berubah.
