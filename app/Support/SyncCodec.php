<?php

namespace App\Support;

/**
 * Kode deterministik untuk join key sync e_billing ↔ Prism.
 *
 * Prism tidak menerima numeric ID e_billing (auto-increment
 * kedua system tidak boleh dianggap sama). Sebagai gantinya
 * setiap entity punya kode deterministik:
 *
 *  - package: 12 char pertama dari nama_paket (uppercase,
 *    hanya A-Z0-9) + 3 digit suffix dari id. Selalu unik
 *    (id unik), tidak perlu cek tabrakan ke DB.
 *
 * ATURAN PENTING: Go (cmd/migrate_ebilling.go, func packageCode)
 * dan PHP (file ini) HARUS menghasilkan kode yang identik
 * untuk (id, nama_paket) yang sama — kode ini adalah join key.
 */
class SyncCodec
{
    public static function packageCode(int $id, string $namaPaket): string
    {
        $base = preg_replace('/[^A-Z0-9]/', '', strtoupper($namaPaket));
        $base = substr($base ?: 'PKG', 0, 12);

        return $base.sprintf('%03d', $id % 1000);
    }

    public static function invoiceNumber(int $id): string
    {
        return 'EB'.$id;
    }
}
