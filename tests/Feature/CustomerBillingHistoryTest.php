<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CustomerBillingHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_history_is_scoped_to_the_customer_and_returns_to_the_same_page(): void
    {
        $package = DB::table('packages')->insertGetId(['nama_paket' => 'Fiber', 'kecepatan' => '10 Mbps', 'harga' => 100]);
        $customer = $this->customer('C1', $package);
        $other = $this->customer('C2', $package);
        $oldest = $this->bill($customer, $package, 'Februari', 2025);
        foreach (['Desember', 'Maret', 'Januari', 'Juni', 'April', 'Mei', 'Juli', 'Agustus', 'September', 'Oktober'] as $month) {
            $this->bill($customer, $package, $month, 2026);
        }
        $this->bill($other, $package, 'November', 2026);

        $this->get('/customers/'.$customer)->assertRedirect('/login');
        $this->actingAs(User::factory()->create(['role' => 'guest']))->get('/customers/'.$customer)->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $this->get('/customers/'.$customer)->assertOk()->assertSee('Riwayat Tagihan')
            ->assertSeeInOrder(['Desember 2026', 'Oktober 2026', 'Januari 2026'])
            ->assertDontSee('November 2026')->assertDontSee('Februari 2025');
        $history = '/customers/'.$customer.'?history_page=2';
        $this->get($history)->assertOk()->assertSee('Februari 2025')->assertDontSee('Desember 2026');
        $context = '?from_customer=1&history_page=2';
        $this->get('/payments/'.$oldest.$context)->assertOk()
            ->assertSee('Tagihan #'.$oldest)->assertDontSee('Riwayat Tagihan')
            ->assertSee(url($history).'#riwayat-tagihan', false);
        $this->delete('/payments/'.$oldest.$context)->assertRedirect($history.'#riwayat-tagihan');
        $this->assertDatabaseMissing('payments', ['id' => $oldest]);
        $this->get($history)->assertOk()->assertSee('Ke halaman pertama');
    }

    private function customer(string $code, int $package): int
    {
        return DB::table('customers')->insertGetId([
            'kode_pelanggan' => $code, 'nama' => 'Pelanggan '.$code,
            'no_hp' => '081234567890', 'alamat' => 'Alamat tes', 'package_id' => $package,
        ]);
    }

    private function bill(int $customer, int $package, string $month, int $year): int
    {
        return DB::table('payments')->insertGetId([
            'customer_id' => $customer, 'package_id' => $package, 'periode' => $month,
            'tahun' => $year, 'jumlah_tagihan' => 100, 'status' => 'belum_bayar',
        ]);
    }
}
