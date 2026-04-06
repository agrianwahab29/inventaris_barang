<?php

namespace Tests\Feature\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function authenticated_user_can_view_dashboard()
    {
        $response = $this->actingAs($this->user)->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
    }

    /** @test */
    public function dashboard_shows_total_barang_count()
    {
        Barang::factory()->count(5)->create();

        $response = $this->actingAs($this->user)->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('totalBarang', 5);
    }

    /** @test */
    public function dashboard_shows_low_stock_items()
    {
        Barang::factory()->create(['stok' => 10, 'stok_minimum' => 5]); // Not low
        Barang::factory()->create(['stok' => 3, 'stok_minimum' => 5]); // Low stock
        Barang::factory()->create(['stok' => 0, 'stok_minimum' => 5]); // Empty

        $response = $this->actingAs($this->user)->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('lowStockItems');
    }

    /** @test */
    public function dashboard_shows_recent_transactions()
    {
        Transaksi::factory()->count(5)->create();

        $response = $this->actingAs($this->user)->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('recentTransactions');
    }

    /** @test */
    public function dashboard_shows_monthly_transaction_summary()
    {
        Transaksi::factory()->count(3)->create([
            'tipe' => 'masuk',
            'tanggal' => now(),
        ]);
        Transaksi::factory()->count(2)->create([
            'tipe' => 'keluar',
            'tanggal' => now(),
        ]);

        $response = $this->actingAs($this->user)->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('monthlySummary');
    }

    /** @test */
    public function guest_cannot_access_dashboard()
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function dashboard_route_alias_works()
    {
        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
    }
}
