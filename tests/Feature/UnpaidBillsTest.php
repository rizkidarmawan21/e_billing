<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UnpaidBillsTest extends TestCase
{
    use RefreshDatabase;

    public function test_month_filter_search_and_default(): void
    {
        $this->travelTo(\Carbon\Carbon::parse('2025-06-20'));
        $a = $this->bill('Januari', 2025, 100, name: 'Andi');
        $b = $this->bill('Maret', 2025, 200, name: 'Andi');
        $this->bill('Januari', 2024, 400, name: 'Andi');
        $this->bill('Februari', 2025, 800, name: 'Andi');
        $this->bill('Maret', 2025, 900, name: 'Budi');
        $this->bill('Maret', 2025, 1000, 'lunas', 'Andi');
        $this->actingAs(User::factory()->create(['role' => 'owner']));
        $url = '/payments/unpaid?filter_active=1&months[]=Januari&months[]=Maret&search=Andi';
        $this->get($url)->assertOk()->assertViewHas('count', 2)->assertViewHas('total', 300)
            ->assertViewHas('payments', fn ($rows) => $rows->pluck('id')->all() === [$a, $b]);
        $this->get('/payments/unpaid')->assertOk()->assertViewHas('count', 5)->assertViewHas('total', 2400);
        $this->get('/payments/unpaid?filter_active=1')->assertViewHas('count', 5);
        $this->get('/payments/unpaid?months[]=Januari')->assertViewHas('count', 5);
    }

    public function test_pagination_and_reset_preserve_summary(): void
    {
        $this->travelTo(\Carbon\Carbon::parse('2025-06-20'));
        foreach (range(1, 12) as $n) {
            $this->bill($n <= 11 ? 'Januari' : 'Maret', 2025, $n * 100, name: sprintf('Andi %02d', $n));
        }
        $this->bill('Januari', 2024, 5000, name: 'Andi Old');
        $this->actingAs(User::factory()->create(['role' => 'owner']));
        $url = '/payments/unpaid?filter_active=1&months[]=Januari&months[]=Maret&search=Andi';
        $first = $this->get($url)->assertOk()->assertViewHas('count', 12)->assertViewHas('total', 7800)
            ->assertSee('page=2')->assertSee('months%5B0%5D=Januari')->assertSee('months%5B1%5D=Maret');
        $second = $this->get($url.'&page=2')->assertOk()->assertViewHas('count', 12)->assertViewHas('total', 7800)
            ->assertSee('Andi 11')->assertSee('Andi 12')->assertDontSee('Andi 01');
        $first->assertSee('Andi 01')->assertDontSee('Andi 11');
        $this->get('/payments/unpaid')->assertViewHas('count', 13)->assertViewHas('total', 12800)
            ->assertSee('href="'.url('/payments/unpaid').'">Reset', false);
    }

    public function test_only_owner_and_admin_can_access_and_invalid_month_is_rejected(): void
    {
        $this->get('/payments/unpaid')->assertRedirect('/login');
        $this->actingAs(User::factory()->create(['role' => 'admin']))->get('/payments/unpaid')->assertOk();
        $this->actingAs(User::factory()->create(['role' => 'guest']))->get('/payments/unpaid')->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'owner']))
            ->get('/payments/unpaid?filter_active=1&months[]=Invalid')->assertSessionHasErrors('months.0');
    }

    public function test_actions_retain_selected_months(): void
    {
        $this->travelTo(\Carbon\Carbon::parse('2025-06-20'));
        $id = $this->bill('Januari', 2025);
        $this->actingAs(User::factory()->create(['role' => 'owner']));
        $url = '/payments/unpaid?filter_active=1&months[]=Januari';
        $this->get($url)->assertSee('months%5B0%5D=Januari');
        $this->get('/payments/'.$id.'/edit?from_unpaid=1&filter_active=1&months[]=Januari')->assertOk();
        $this->delete('/payments/'.$id, ['from_unpaid' => '1', 'filter_active' => '1', 'months' => ['Januari']])
            ->assertRedirect('/payments/unpaid?filter_active=1&months%5B0%5D=Januari');
        $this->get($url)->assertViewHas('count', 0);
    }

    public function test_admin_layout_filters_pagination_and_action_returns(): void
    {
        $this->travelTo(\Carbon\Carbon::parse('2025-06-20'));
        for ($n = 1; $n <= 11; $n++) {
            $id = $this->bill('Januari', 2025, 100, name: sprintf('Andi %02d', $n));
        }
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $query = 'filter_active=1&months%5B0%5D=Januari&search=Andi&page=2';
        $return = '/payments/unpaid?'.$query;
        $this->get('/payments/unpaid?filter_active=1&months[]=Januari&search=Andi')
            ->assertOk()->assertSee('Sistem Billing')->assertSee('Tagihan Unpaid')
            ->assertSee('page=2')->assertSee('months%5B0%5D=Januari')->assertSee('pagination');
        $this->get($return)->assertOk()->assertSee('Andi 11')->assertDontSee('Andi 01');
        $this->get('/payments/'.$id.'?from_unpaid=1&'.$query)->assertOk()->assertSee('payments/unpaid?', false);
        $this->get('/payments/'.$id.'/edit?from_unpaid=1&'.$query)->assertOk()->assertSee('payments/unpaid?', false);
        $bill = DB::table('payments')->find($id);
        $this->put('/payments/'.$id.'?from_unpaid=1&'.$query, [
            'customer_id' => $bill->customer_id, 'package_id' => $bill->package_id,
            'periode' => 'Januari', 'tahun' => 2025, 'jumlah_tagihan' => 100, 'status' => 'lunas',
        ])->assertRedirect('/payments/unpaid?filter_active=1&search=Andi&page=2&months%5B0%5D=Januari');
        $this->delete('/payments/'.$id, ['from_unpaid' => '1', 'filter_active' => '1', 'months' => ['Januari'], 'search' => 'Andi', 'page' => 2])
            ->assertRedirect('/payments/unpaid?filter_active=1&search=Andi&page=2&months%5B0%5D=Januari');
    }

    private function bill(string $month, int $year, int $amount = 100, string $status = 'belum_bayar', string $name = 'Test Customer'): int
    {
        $package = DB::table('packages')->insertGetId(['nama_paket' => 'Test', 'kecepatan' => '10 Mbps', 'harga' => 100]);
        $customer = DB::table('customers')->insertGetId(['kode_pelanggan' => uniqid(), 'nama' => $name, 'no_hp' => '123', 'alamat' => 'Test', 'package_id' => $package]);
        return DB::table('payments')->insertGetId(['customer_id' => $customer, 'package_id' => $package, 'periode' => $month, 'tahun' => $year, 'jumlah_tagihan' => $amount, 'status' => $status]);
    }

}
