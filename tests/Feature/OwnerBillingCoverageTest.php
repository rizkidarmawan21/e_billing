<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OwnerBillingCoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_panel_lists_only_customers_with_a_package_and_no_bill_this_month(): void
    {
        $this->travelTo(now()->setDate(2026, 1, 15));
        $package = $this->package();
        $otherPackage = $this->package('Paket Sebelumnya');
        $paid = $this->customer('Sudah Lunas', $package);
        $unpaid = $this->customer('Sudah Ada Tagihan', $package);
        $this->bill($paid, $otherPackage, 'Januari', 2026, 'lunas');
        $this->bill($unpaid, $package, 'Januari', 2026);
        $this->customer('Tanpa Paket', null);

        $missing = [];
        for ($i = 1; $i <= 6; $i++) {
            $missing[] = $this->customer('Belum Ditagih '.$i, $package);
        }
        $this->bill($missing[0], $package, 'Januari', 2025);
        $this->bill($missing[1], $package, 'Februari', 2026);

        $this->actingAs(User::factory()->create(['role' => 'owner']));
        $path = '/owner/dashboard?filter_active=1&months[]=Februari';
        $response = $this->get($path)->assertOk()
            ->assertSee('Periode berjalan: Januari 2026')
            ->assertSee('6 pelanggan')->assertSee('Belum Ditagih 1')
            ->assertSee(route('customers.show', $missing[0]), false)
            ->assertDontSee('Belum Ditagih 6')->assertDontSee('Sudah Lunas')
            ->assertDontSee('Sudah Ada Tagihan')->assertDontSee('Tanpa Paket');
        $paginator = $response->viewData('customersWithoutBills');
        $this->assertStringContainsString('months%5B0%5D=Februari', $paginator->nextPageUrl());
        $this->assertStringEndsWith('#tagihan-belum-dibuat', $paginator->nextPageUrl());
        $this->get($path.'&missing_bills_page=2')->assertOk()->assertSee('6 pelanggan')
            ->assertSee('Belum Ditagih 6')->assertDontSee('Belum Ditagih 1');
        $this->get('/owner/dashboard?all=1')->assertOk()->assertSee('6 pelanggan');
        $this->assertDatabaseCount('payments', 4);
    }

    public function test_empty_states_distinguish_no_packages_from_complete_billing(): void
    {
        $this->travelTo(now()->setDate(2026, 1, 15));
        $this->actingAs(User::factory()->create(['role' => 'owner']));
        $this->get('/owner/dashboard')->assertOk()->assertSee('Belum ada pelanggan yang memiliki paket.');

        $package = $this->package();
        $this->bill($this->customer('Pelanggan Aktif', $package), $package, 'Januari', 2026);
        $this->get('/owner/dashboard')->assertOk()
            ->assertSee('Semua pelanggan yang punya paket sudah ditagih bulan ini.');
    }

    public function test_admin_cannot_access_owner_panel(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $this->get('/owner/dashboard')->assertForbidden();
        $this->get('/dashboard')->assertOk()->assertDontSee('Tagihan Belum Dibuat');
    }

    private function package(string $name = 'Paket Fiber'): int
    {
        return DB::table('packages')->insertGetId(['nama_paket' => $name, 'kecepatan' => '100 Mbps', 'harga' => 150000]);
    }

    private function customer(string $name, ?int $package): int
    {
        return DB::table('customers')->insertGetId([
            'kode_pelanggan' => uniqid('C-'), 'nama' => $name,
            'no_hp' => '081234567890', 'alamat' => 'Alamat tes', 'package_id' => $package,
        ]);
    }

    private function bill(int $customer, int $package, string $month, int $year, string $status = 'belum_bayar'): void
    {
        DB::table('payments')->insert([
            'customer_id' => $customer, 'package_id' => $package, 'periode' => $month, 'tahun' => $year,
            'jumlah_tagihan' => 150000, 'status' => $status,
            'tanggal_bayar' => $status === 'lunas' ? now()->toDateString() : null,
        ]);
    }
}
