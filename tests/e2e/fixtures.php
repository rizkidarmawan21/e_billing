<?php

// This standalone fixture must never bootstrap against a developer database.
$owned = __DIR__.'/artifacts';
$database = getenv('DB_DATABASE');
if (getenv('APP_ENV') !== 'testing' || getenv('DB_CONNECTION') !== 'sqlite'
    || !is_dir($owned) || is_link($owned) || realpath($owned) !== $owned || !$database || !is_file($database)
    || realpath(dirname($database)) !== realpath($owned)
    || is_link($database) || basename($database) !== 'mobile.sqlite') {
    fwrite(STDERR, "Refusing fixtures outside testing and the test-owned SQLite path.\n");
    exit(1);
}

require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
if (!$app->environment('testing') || config('database.default') !== 'sqlite'
    || realpath(config('database.connections.sqlite.database')) !== realpath($database)
    || config('sync.enabled')) {
    throw new RuntimeException('Unsafe effective fixture configuration.');
}
Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force' => true]);
$now = now();
$db = Illuminate\Support\Facades\DB::class;
$password = Illuminate\Support\Facades\Hash::make('MobileTest!234');
foreach (['owner', 'admin'] as $index => $role) {
    $db::table('users')->insert([
        'id' => $index + 1, 'name' => 'Mobile '.$role.' '.str_repeat('LongName', 8),
        'email' => $role.'@mobile.test', 'role' => $role, 'password' => $password,
        'email_verified_at' => $now, 'created_at' => $now, 'updated_at' => $now,
    ]);
}
for ($i = 1; $i <= 12; $i++) {
    $db::table('packages')->insert([
        'id' => $i, 'nama_paket' => sprintf('Mobile Package %02d ', $i).str_repeat('FiberLong', 10),
        'kecepatan' => '100 Mbps', 'harga' => 150000 + $i,
        'deskripsi' => str_repeat('Deskripsi paket internet panjang. ', 12),
        'created_at' => $now, 'updated_at' => $now,
    ]);
    $db::table('customers')->insert([
        'id' => $i, 'kode_pelanggan' => sprintf('MOBILE-%02d-', $i).str_repeat('X', 70),
        'nama' => sprintf('Mobile Customer %02d ', $i).str_repeat('LongCustomer', 8),
        'no_hp' => '081234567890', 'alamat' => str_repeat('Jalan Alamat Sangat Panjang ', 15),
        'package_id' => 1, 'created_at' => $now, 'updated_at' => $now,
    ]);
    foreach (['Januari' => 'belum_bayar', 'Februari' => 'lunas'] as $month => $status) {
        $db::table('payments')->insert([
            'customer_id' => $i, 'package_id' => 1, 'periode' => $month, 'tahun' => $now->year,
            'jumlah_tagihan' => 150001, 'status' => $status,
            'tanggal_bayar' => $status === 'lunas' ? $now->toDateString() : null,
            'created_at' => $now, 'updated_at' => $now,
        ]);
    }
    $db::table('users')->insert([
        'name' => sprintf('Mobile Secondary Admin %02d ', $i).str_repeat('Long', 15),
        'email' => sprintf('secondary-%02d@mobile.test', $i), 'role' => 'admin',
        'password' => $password, 'email_verified_at' => $now,
        'created_at' => $now, 'updated_at' => $now,
    ]);
}
echo "Fixture ready: 2 roles, 12 packages, 12 customers, 24 payments, 12 secondary admins.\n";
