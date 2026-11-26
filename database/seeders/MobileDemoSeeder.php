<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class MobileDemoSeeder extends Seeder
{
    public function run(): void
    {
        $expectedPath = storage_path('app/mobile-demo.sqlite');
        $connection = DB::connection();

        if (!app()->environment('local') || $connection->getDriverName() !== 'sqlite'
            || $connection->getDatabaseName() !== $expectedPath || is_link($expectedPath)
            || config('sync.enabled') || !empty($connection->getConfig('url'))) {
            throw new RuntimeException('Demo fixtures require the dedicated local SQLite database and disabled sync.');
        }

        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $now = now('Asia/Jakarta');
        $timestamps = ['created_at' => $now, 'updated_at' => $now];

        DB::transaction(function () use ($months, $now, $timestamps) {
            if (DB::table('users')->exists() || DB::table('packages')->exists() || DB::table('customers')->exists() || DB::table('payments')->exists()) {
                throw new RuntimeException('The demo database already contains data; no records were changed.');
            }

            foreach ([
                ['Owner Demo', 'owner@demo.ebilling.test', 'owner'],
                ['Admin Operasional Demo', 'admin@demo.ebilling.test', 'admin'],
                ['Admin Penagihan Cabang Wilayah Utara dan Area Perumahan Demo', 'penagihan-wilayah-utara@demo.ebilling.test', 'admin'],
                ['Admin Layanan Demo', 'layanan@demo.ebilling.test', 'admin'],
            ] as [$name, $email, $role]) {
                DB::table('users')->insert($timestamps + [
                    'name' => $name, 'email' => $email, 'role' => $role,
                    'email_verified_at' => $now, 'password' => Hash::make('mobile-demo-only'),
                ]);
            }

            $packages = [];
            foreach ([
                ['Rumah Hemat', '20 Mbps', 125000, 'Koneksi dasar untuk browsing dan belajar.'],
                ['Rumah Aktif', '50 Mbps', 185000, 'Untuk beberapa perangkat di rumah.'],
                ['Keluarga Plus', '100 Mbps', 265000, 'Untuk streaming dan kerja dari rumah.'],
                ['Usaha Prioritas', '250 Mbps', 475000, 'Koneksi untuk aktivitas usaha harian.'],
                ['Kantor Multi-Lantai dengan Layanan Prioritas', '500 Mbps', 1250000, 'Contoh nama paket dan nominal panjang.'],
                ['Paket Cadangan', '30 Mbps', 150000, null],
            ] as [$name, $speed, $price, $description]) {
                $id = DB::table('packages')->insertGetId($timestamps + [
                    'nama_paket' => $name, 'kecepatan' => $speed, 'harga' => $price, 'deskripsi' => $description,
                ]);
                $packages[] = [$id, $price];
            }

            $names = ['Aruna', 'Bima', 'Citra', 'Damar', 'Elara', 'Farrel', 'Gita', 'Hana'];
            $areas = ['Cemara', 'Kenanga', 'Melati', 'Teratai'];
            for ($i = 0; $i < 32; $i++) {
                [$packageId, $price] = $packages[$i % 5];
                $name = $names[$i % 8].' '.$areas[intdiv($i, 8)];
                $customerId = DB::table('customers')->insertGetId($timestamps + [
                    'kode_pelanggan' => sprintf('DEMO-%05d', $i + 1),
                    'nama' => $i === 0 ? 'Aruna Cemara — Pengelola Kompleks Taman Anggrek Residence Blok Utara' : $name,
                    'no_hp' => sprintf('08000000%04d', $i + 1),
                    'alamat' => 'Alamat fiktif: Jl. '.$areas[intdiv($i, 8)].' Raya No. '.($i + 1).', Blok Utara, Kota Demo',
                    'package_id' => $i === 31 ? null : $packageId,
                ]);

                if ($i >= 30) {
                    continue;
                }

                for ($offset = 13; $offset >= 0; $offset--) {
                    if ($offset > 3 && ($i + $offset) % 3 === 0) {
                        continue;
                    }

                    $period = $now->copy()->startOfMonth()->subMonths($offset);
                    $paid = $offset === 0 ? $i % 3 === 0 : ($i + $offset * 2) % 7 !== 0;
                    DB::table('payments')->insert([
                        'customer_id' => $customerId,
                        'package_id' => $packageId,
                        'periode' => $months[$period->month - 1],
                        'tahun' => $period->year,
                        'jumlah_tagihan' => $price,
                        'status' => $paid ? 'lunas' : 'belum_bayar',
                        'tanggal_bayar' => $paid ? $period->copy()->addDays(min(5, $now->day - 1))->toDateString() : null,
                        'created_at' => $period,
                        'updated_at' => $period,
                    ]);
                }
            }
        });
    }
}
