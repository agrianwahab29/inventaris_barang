<?php

namespace Tests\Feature\Controllers;

use App\Models\Barang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarangControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function authenticated_user_can_view_barang_list()
    {
        $barang = Barang::factory()->count(3)->create();

        $response = $this->actingAs($this->user)->get('/barang');

        $response->assertStatus(200);
        $response->assertViewIs('barang.index');
        $response->assertViewHas('barangs');
    }

    /** @test */
    public function authenticated_user_can_view_barang_details()
    {
        $barang = Barang::factory()->create();

        $response = $this->actingAs($this->user)->get("/barang/{$barang->id}");

        $response->assertStatus(200);
        $response->assertViewIs('barang.show');
        $response->assertViewHas('barang');
    }

    /** @test */
    public function authenticated_user_can_create_barang()
    {
        $response = $this->actingAs($this->user)->post('/barang', [
            'nama_barang' => 'Laptop Dell',
            'kategori' => 'Elektronik',
            'satuan' => 'unit',
            'stok' => 10,
            'stok_minimum' => 5,
            'catatan' => 'Barang baru',
        ]);

        $response->assertRedirect('/barang');
        $this->assertDatabaseHas('barangs', [
            'nama_barang' => 'Laptop Dell',
            'kategori' => 'Elektronik',
        ]);
    }

    /** @test */
    public function creating_barang_requires_nama_barang()
    {
        $response = $this->actingAs($this->user)->post('/barang', [
            'nama_barang' => '',
            'kategori' => 'Elektronik',
            'satuan' => 'unit',
            'stok' => 10,
        ]);

        $response->assertSessionHasErrors('nama_barang');
    }

    /** @test */
    public function creating_barang_requires_kategori()
    {
        $response = $this->actingAs($this->user)->post('/barang', [
            'nama_barang' => 'Laptop Dell',
            'kategori' => '',
            'satuan' => 'unit',
            'stok' => 10,
        ]);

        $response->assertSessionHasErrors('kategori');
    }

    /** @test */
    public function admin_can_update_barang()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $barang = Barang::factory()->create();

        $response = $this->actingAs($admin)->put("/barang/{$barang->id}", [
            'nama_barang' => 'Updated Name',
            'kategori' => $barang->kategori,
            'satuan' => $barang->satuan,
            'stok' => $barang->stok,
            'stok_minimum' => $barang->stok_minimum,
        ]);

        $response->assertRedirect('/barang');
        $this->assertDatabaseHas('barangs', [
            'id' => $barang->id,
            'nama_barang' => 'Updated Name',
        ]);
    }

    /** @test */
    public function non_admin_cannot_update_barang()
    {
        $pengguna = User::factory()->create(['role' => 'pengguna']);
        $barang = Barang::factory()->create();

        $response = $this->actingAs($pengguna)->put("/barang/{$barang->id}", [
            'nama_barang' => 'Updated Name',
            'kategori' => $barang->kategori,
            'satuan' => $barang->satuan,
            'stok' => $barang->stok,
            'stok_minimum' => $barang->stok_minimum,
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_delete_barang()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $barang = Barang::factory()->create();

        $response = $this->actingAs($admin)->delete("/barang/{$barang->id}");

        $response->assertRedirect('/barang');
        $this->assertDatabaseMissing('barangs', [
            'id' => $barang->id,
        ]);
    }

    /** @test */
    public function non_admin_cannot_delete_barang()
    {
        $pengguna = User::factory()->create(['role' => 'pengguna']);
        $barang = Barang::factory()->create();

        $response = $this->actingAs($pengguna)->delete("/barang/{$barang->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function authenticated_user_can_update_stok()
    {
        $barang = Barang::factory()->create(['stok' => 10]);

        $response = $this->actingAs($this->user)->post("/barang/{$barang->id}/update-stok", [
            'stok' => 20,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('barangs', [
            'id' => $barang->id,
            'stok' => 20,
        ]);
    }

    /** @test */
    public function admin_can_bulk_delete_barang()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $barangs = Barang::factory()->count(3)->create();
        $ids = $barangs->pluck('id')->toArray();

        $response = $this->actingAs($admin)->delete('/barang/bulk/delete', [
            'ids' => $ids,
        ]);

        $response->assertRedirect('/barang');
        foreach ($ids as $id) {
            $this->assertDatabaseMissing('barangs', ['id' => $id]);
        }
    }

    /** @test */
    public function guest_cannot_access_barang_routes()
    {
        $response = $this->get('/barang');
        $response->assertRedirect('/login');
    }
}
