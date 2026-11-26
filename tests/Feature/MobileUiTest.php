<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class MobileUiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['sync.enabled' => false]);
        DB::table('packages')->insert([
            'id' => 1, 'nama_paket' => str_repeat('Paket Fiber Panjang ', 8),
            'kecepatan' => '100 Mbps', 'harga' => 150000, 'deskripsi' => str_repeat('Deskripsi panjang ', 20),
        ]);
        for ($i = 1; $i <= 12; $i++) {
            DB::table('customers')->insert([
                'id' => $i, 'kode_pelanggan' => sprintf('MOBILE-%02d', $i),
                'nama' => sprintf('Mobile Customer %02d ', $i).str_repeat('LongName', 12),
                'no_hp' => '081234567890', 'alamat' => str_repeat('Alamat panjang ', 30), 'package_id' => 1,
            ]);
            DB::table('payments')->insert([
                'id' => $i, 'customer_id' => $i, 'package_id' => 1,
                'periode' => 'Januari', 'tahun' => now()->year, 'jumlah_tagihan' => 150000,
                'status' => $i === 12 ? 'lunas' : 'belum_bayar',
                'tanggal_bayar' => $i === 12 ? now()->toDateString() : null,
            ]);
        }
        User::factory()->create(['id' => 100, 'role' => 'admin']);
    }

    public static function pages(): iterable
    {
        $shared = ['/dashboard', '/customers', '/customers/create', '/customers/1', '/customers/1/edit',
            '/packages', '/packages/create', '/packages/1', '/packages/1/edit',
            '/payments', '/payments/create', '/payments/1', '/payments/1/edit', '/payments/unpaid', '/profile'];
        foreach (['admin', 'owner'] as $role) {
            foreach ($shared as $url) {
                yield $role.' '.$url => [$role, $url];
            }
        }
        foreach (['/owner/dashboard', '/owner/laporan', '/owner/monitoring', '/owner/admin', '/owner/admin/create', '/owner/admin/100/edit'] as $url) {
            yield 'owner '.$url => ['owner', $url];
        }
    }

    #[DataProvider('pages')]
    public function test_active_pages_render_the_accessible_mobile_contract(string $role, string $url): void
    {
        $this->actingAs(User::factory()->create(['role' => $role]));
        $response = $this->get($url)->assertOk()
            ->assertSee('id="panelNavigation"', false)
            ->assertSee('aria-label="Buka menu navigasi"', false)
            ->assertSee('aria-label="Tutup menu navigasi"', false)
            ->assertSee('aria-label="Menu akun"', false)
            ->assertSee('name="viewport"', false);
        $document = new \DOMDocument;
        @$document->loadHTML($response->getContent());
        $xpath = new \DOMXPath($document);
        $ids = [];
        foreach ($xpath->query('//*[@id]') as $node) {
            $id = $node->getAttribute('id');
            $this->assertNotContains($id, $ids, 'Duplicate ID: '.$id.' at '.$url);
            $ids[] = $id;
        }
        foreach ($xpath->query('//table[contains(concat(" ", normalize-space(@class), " "), " billing-table ")]//tbody/tr/td[not(@colspan)]') as $cell) {
            $this->assertNotSame('', $cell->getAttribute('data-label'), 'Missing record label at '.$url);
        }
        foreach ($xpath->query('//input[@type="password"]') as $password) {
            $id = $password->getAttribute('id');
            $this->assertNotSame('', $id);
            $this->assertSame(1, $xpath->query('//button[@aria-controls="'.$id.'"]')->length,
                'Password must have one linked visibility control: '.$id);
            $this->assertFalse($password->hasAttribute('value'), 'Never render password values');
        }
    }

    public function test_owner_only_pages_remain_forbidden_to_admin(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        foreach (['/owner/dashboard', '/owner/laporan', '/owner/monitoring', '/owner/admin', '/owner/admin/create', '/owner/admin/100/edit'] as $url) {
            $this->get($url)->assertForbidden();
        }
    }

    public function test_invalid_crud_input_does_not_create_records_and_preserves_old_input(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'owner']));
        $this->from('/customers/create')->post('/customers', ['kode_pelanggan' => 'MOBILE-01', 'nama' => 'Retained name'])
            ->assertRedirect('/customers/create')->assertSessionHasErrors('kode_pelanggan')
            ->assertSessionHasInput('nama', 'Retained name');
        $this->post('/packages', ['nama_paket' => 'Invalid', 'harga' => -1])->assertSessionHasErrors(['harga', 'kecepatan']);
        $this->post('/payments', ['status' => 'invalid'])->assertSessionHasErrors(['customer_id', 'package_id', 'periode', 'tahun', 'jumlah_tagihan', 'status']);
        $this->post('/owner/admin', ['name' => 'Invalid', 'email' => 'not-email', 'password' => 'short'])
            ->assertSessionHasErrors(['email', 'password']);
        $this->assertDatabaseCount('customers', 12);
        $this->assertDatabaseCount('packages', 1);
        $this->assertDatabaseCount('payments', 12);
    }

    public function test_unpaid_edit_retains_filters_and_pagination(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $query = 'from_unpaid=1&filter_active=1&months%5B0%5D=Januari&search=Mobile&page=2';
        $this->put('/payments/11?'.$query, [
            'customer_id' => 11, 'package_id' => 1, 'periode' => 'Januari', 'tahun' => now()->year,
            'jumlah_tagihan' => 160000, 'status' => 'belum_bayar',
        ])->assertRedirect('/payments/unpaid?filter_active=1&search=Mobile&page=2&months%5B0%5D=Januari');
        $this->assertDatabaseHas('payments', ['id' => 11, 'jumlah_tagihan' => 160000]);
    }
}
