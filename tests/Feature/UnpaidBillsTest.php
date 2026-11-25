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
