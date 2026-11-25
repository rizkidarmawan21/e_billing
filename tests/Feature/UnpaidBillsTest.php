<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UnpaidBillsTest extends TestCase
{
    use RefreshDatabase;

    public function test_unpaid_page_shows_all_unpaid_bills_and_summary(): void
    {
        $user = User::factory()->create(['role' => 'owner']);
        $package = DB::table('packages')->insertGetId(['nama_paket' => 'Test', 'kecepatan' => '10 Mbps', 'harga' => 100]);
        $customer = DB::table('customers')->insertGetId(['kode_pelanggan' => 'TEST', 'nama' => 'Test Customer', 'no_hp' => '123', 'alamat' => 'Test', 'package_id' => $package]);
        foreach ([['Desember', 2023, 100, 'belum_bayar'], ['Januari', 2025, 200, 'belum_bayar'], ['Februari', 2025, 900, 'lunas']] as [$month, $year, $amount, $status]) {
            DB::table('payments')->insert(['customer_id' => $customer, 'package_id' => $package, 'periode' => $month, 'tahun' => $year, 'jumlah_tagihan' => $amount, 'status' => $status]);
        }

        $this->actingAs($user)->get('/payments/unpaid')->assertOk()
            ->assertSeeInOrder(['Desember 2023', 'Januari 2025'])
            ->assertDontSee('Februari 2025')
            ->assertViewHas('count', 2)->assertViewHas('total', 300);
    }

    public function test_blank_browser_fields_and_zero_search(): void
    {
        $id = $this->bill('Januari', 2025, name: 'Customer 0');
        $this->bill('Januari', 2025, name: 'Other');
        $this->actingAs(User::factory()->create(['role' => 'owner']));
        $this->get('/payments/unpaid?preset=all&month=&start=&end=&search=0')->assertOk()
            ->assertViewHas('count', 1)->assertViewHas('total', 100)
            ->assertViewHas('payments', fn ($rows) => $rows->pluck('id')->all() === [$id]);
    }

    public function test_action_query_cannot_override_bound_payment(): void
    {
        $id = $this->bill('Januari', 2025);
        $other = $this->bill('Februari', 2025);
        $this->actingAs(User::factory()->create(['role' => 'owner']));
        $this->get('/payments/'.$id.'/edit?from_unpaid=1&payment='.$other)->assertOk()
            ->assertSee('action="'.url('/payments/'.$id), false);
    }

    public function test_admin_crafted_return_flag_does_not_redirect_to_owner_page(): void
    {
        $id = $this->bill('Januari', 2025);
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $this->delete('/payments/'.$id, ['from_unpaid' => '1'])->assertRedirect('/payments');
    }

    public function test_delete_rejects_invalid_return_filters_without_mutating_payment(): void
    {
        $id = $this->bill('Januari', 2025);
        $this->actingAs(User::factory()->create(['role' => 'owner']));
        $this->delete('/payments/'.$id, ['from_unpaid' => '1', 'preset' => 'invalid'])->assertSessionHasErrors('preset');
        $this->assertDatabaseHas('payments', ['id' => $id]);
    }

    public function test_rendered_pagination_and_cross_year_rows_are_exact(): void
    {
        $ids = [];
        foreach (range(1, 12) as $n) {
            $ids[] = $this->bill($n <= 6 ? 'Desember' : 'Januari', $n <= 6 ? 2024 : 2025, $n * 100, name: sprintf('Sample %02d', $n));
        }
        $this->bill('Januari', 2025, 9999, 'lunas', 'Paid Sample');
        $this->bill('Februari', 2025, 9999, name: 'Outside Sample');
        $this->actingAs(User::factory()->create(['role' => 'owner']));
        $url = '/payments/unpaid?preset=range&start=2024-12&end=2025-01&search=Sample&month=';
        $first = $this->get($url)->assertOk()->assertViewHas('count', 12)->assertViewHas('total', 7800)->assertSee('Rp 7.800');
        $second = $this->get($url.'&page=2')->assertOk()->assertViewHas('count', 12)->assertViewHas('total', 7800);
        $first->assertViewHas('payments', fn ($rows) => $rows->pluck('id')->all() === array_slice($ids, 0, 10));
        $second->assertViewHas('payments', fn ($rows) => $rows->pluck('id')->all() === array_slice($ids, 10));
        foreach (range(1, 12) as $n) {
            $name = sprintf('Sample %02d', $n);
            ($n <= 10 ? $first : $second)->assertSee($name);
            ($n <= 10 ? $second : $first)->assertDontSee($name);
        }
        $first->assertDontSee('Paid Sample')->assertDontSee('Outside Sample')->assertSee('page=2');
        foreach ([$first, $second] as $page) {
            $page->assertSee('<ul class="pagination">', false)
                ->assertSee('&laquo; Previous', false)->assertSee('Next &raquo;', false)->assertDontSee('<svg', false)
                ->assertDontSee('Showing', false)->assertDontSee('d-sm-none', false);
        }
        $second->assertSee('Rp 1.100')->assertSee('Rp 1.200');
    }

    public function test_blank_range_requires_both_ends_and_actions_retain_blank_fields(): void
    {
        $id = $this->bill('Januari', 2025);
        $this->actingAs(User::factory()->create(['role' => 'owner']));
        $this->get('/payments/unpaid?preset=range&start=&end=')->assertSessionHasErrors(['start', 'end']);
        $this->delete('/payments/'.$id, ['from_unpaid' => '1', 'preset' => 'all', 'month' => '', 'start' => '', 'end' => '', 'search' => ''])->assertRedirect();
        $this->assertDatabaseMissing('payments', ['id' => $id]);
    }

    private function bill(string $month, int $year, int $amount = 100, string $status = 'belum_bayar', string $name = 'Test Customer'): int
    {
        $package = DB::table('packages')->insertGetId(['nama_paket' => 'Test', 'kecepatan' => '10 Mbps', 'harga' => 100]);
        $customer = DB::table('customers')->insertGetId(['kode_pelanggan' => uniqid(), 'nama' => $name, 'no_hp' => '123', 'alamat' => 'Test', 'package_id' => $package]);
        return DB::table('payments')->insertGetId(['customer_id' => $customer, 'package_id' => $package, 'periode' => $month, 'tahun' => $year, 'jumlah_tagihan' => $amount, 'status' => $status]);
    }

    public function test_filters_period_and_search_with_matching_summary(): void
    {
        $this->travelTo(\Carbon\Carbon::parse('2025-01-20'));
        $this->bill('Desember', 2024, 100, name: 'Andi');
        $this->bill('Januari', 2025, 200, name: 'Budi');
        $this->bill('Februari', 2025, 300, name: 'Andi');
        $this->bill('Januari', 2025, 900, 'lunas');
        $this->actingAs(User::factory()->create(['role' => 'owner']));

        $this->get('/payments/unpaid?preset=current')->assertOk()->assertViewHas('count', 1)->assertViewHas('total', 200);
        $this->get('/payments/unpaid?preset=month&month=2025-02')->assertOk()->assertViewHas('count', 1)->assertViewHas('total', 300);
        $this->get('/payments/unpaid?preset=range&start=2024-12&end=2025-01&search=Andi')->assertOk()->assertViewHas('count', 1)->assertViewHas('total', 100);
        $this->get('/payments/unpaid?preset=range&start=2025-02&end=2024-12')->assertSessionHasErrors('end');
        $this->get('/payments/unpaid?preset=month&month=2025-13')->assertSessionHasErrors('month');
    }

    public function test_access_and_pagination_retain_filters(): void
    {
        $this->get('/payments/unpaid')->assertRedirect('/login');
        $this->actingAs(User::factory()->create(['role' => 'guest']))->get('/payments/unpaid')->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'admin']))->get('/payments/unpaid')->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'owner']));
        foreach (range(1, 11) as $i) {
            $this->bill('Januari', 2025, 100, name: 'Budi '.$i);
        }
        $this->get('/payments/unpaid?preset=month&month=2025-01&search=Budi')->assertOk()
            ->assertViewHas('count', 11)->assertViewHas('total', 1100)
            ->assertSee('page=2')->assertSee('month=2025-01');
    }

    public function test_edit_marks_bill_paid_and_returns_to_filtered_list(): void
    {
        $id = $this->bill('Januari', 2025);
        $bill = DB::table('payments')->find($id);
        $this->actingAs(User::factory()->create(['role' => 'owner']));
        $query = 'from_unpaid=1&preset=month&month=2025-01';
        $this->get('/payments/'.$id.'/edit?'.$query)->assertOk()->assertSee('from_unpaid=1');
        $this->get('/payments/'.$id.'?'.$query)->assertOk()->assertSee('/payments/unpaid?preset=month');
        $this->put('/payments/'.$id.'?'.$query, [
            'customer_id' => $bill->customer_id, 'package_id' => $bill->package_id,
            'periode' => 'Januari', 'tahun' => 2025, 'jumlah_tagihan' => 100,
            'status' => 'lunas',
        ])->assertRedirect('/payments/unpaid?preset=month&month=2025-01');
        $this->assertDatabaseHas('payments', ['id' => $id, 'status' => 'lunas']);
        $this->get('/payments/unpaid?preset=month&month=2025-01')->assertViewHas('count', 0)->assertSee('Hapus filter');
    }

    public function test_row_actions_keep_filters_and_delete_one_bill(): void
    {
        $first = $this->bill('Januari', 2025);
        $second = $this->bill('Februari', 2025);
        $this->actingAs(User::factory()->create(['role' => 'owner']));
        $url = '/payments/unpaid?preset=range&start=2025-01&end=2025-02';
        $this->get($url)->assertSee('/payments/'.$first.'/edit?preset=range');
        $this->delete('/payments/'.$first, ['from_unpaid' => '1', 'preset' => 'range', 'start' => '2025-01', 'end' => '2025-02'])->assertRedirect($url);
        $this->assertDatabaseMissing('payments', ['id' => $first]);
        $this->assertDatabaseHas('payments', ['id' => $second]);
        $this->get($url)->assertViewHas('count', 1)->assertViewHas('total', 100);
    }
}
