<?php

namespace Tests\Feature\Api;

use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransaksiApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $barang;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->barang = Barang::factory()->create(['stok' => 100]);
    }

    /** @test */
    public function it_returns_barang_info_via_api()
    {
        $response = $this->actingAs($this->user)
            ->getJson("/api/barang/{$this->barang->id}/info");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $this->barang->id,
            'nama_barang' => $this->barang->nama_barang,
            'stok' => $this->barang->stok,
        ]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_barang()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/barang/99999/info');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_checks_transaction_updates_via_api()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/transactions/check-updates');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'has_updates',
            'last_update',
        ]);
    }

    /** @test */
    public function api_requires_authentication()
    {
        $response = $this->getJson("/api/barang/{$this->barang->id}/info");

        $response->assertStatus(401);
    }

    /** @test */
    public function it_returns_barang_info_with_relations()
    {
        $ruangan = Ruangan::factory()->create();
        $transaksi = Transaksi::factory()->create([
            'barang_id' => $this->barang->id,
            'ruangan_id' => $ruangan->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/barang/{$this->barang->id}/info");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'nama_barang' => $this->barang->nama_barang,
        ]);
    }
}
