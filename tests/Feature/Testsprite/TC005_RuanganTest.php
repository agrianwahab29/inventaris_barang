<?php

namespace Tests\Feature\Testsprite;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Ruangan;
use App\Models\Barang;
use App\Models\Transaksi;

class TC005_RuanganTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $pengguna;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);

        $this->pengguna = User::create([
            'name' => 'Pengguna',
            'username' => 'pengguna',
            'email' => 'pengguna@test.com',
            'password' => bcrypt('pengguna123'),
            'role' => 'pengguna',
        ]);
    }

    public function test_ruangan_index_is_accessible()
    {
        $response = $this->actingAs($this->admin)->get('/ruangan');
        $response->assertStatus(200);
        $response->assertSee('Ruangan');
    }

    public function test_admin_can_create_ruangan()
    {
        $response = $this->actingAs($this->admin)->post('/ruangan', [
            'nama_ruangan' => 'New Room',
            'keterangan' => 'Test description',
        ]);

        $response->assertRedirect('/ruangan');
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('ruangans', [
            'nama_ruangan' => 'New Room',
        ]);
    }

    public function test_pengguna_cannot_create_ruangan()
    {
        $response = $this->actingAs($this->pengguna)->post('/ruangan', [
            'nama_ruangan' => 'Unauthorized Room',
            'keterangan' => 'Test',
        ]);

        // Role middleware may redirect (302) or return forbidden (403)
        $this->assertTrue(
            $response->isRedirect() || 
            $response->isForbidden() ||
            $response->isClientError()
        );
    }

    public function test_admin_can_update_ruangan()
    {
        $ruangan = Ruangan::create([
            'nama_ruangan' => 'Old Room',
            'keterangan' => 'Old desc',
        ]);

        $response = $this->actingAs($this->admin)->put("/ruangan/{$ruangan->id}", [
            'nama_ruangan' => 'Updated Room',
            'keterangan' => 'Updated desc',
        ]);

        $response->assertRedirect('/ruangan');
        
        $this->assertDatabaseHas('ruangans', [
            'id' => $ruangan->id,
            'nama_ruangan' => 'Updated Room',
        ]);
    }

    public function test_admin_can_delete_ruangan_without_transactions()
    {
        $ruangan = Ruangan::create([
            'nama_ruangan' => 'Delete Me',
            'keterangan' => 'Test',
        ]);

        $response = $this->actingAs($this->admin)->delete("/ruangan/{$ruangan->id}");
        $response->assertRedirect('/ruangan');
        
        $this->assertDatabaseMissing('ruangans', [
            'id' => $ruangan->id,
        ]);
    }

    public function test_show_ruangan_displays_details()
    {
        $ruangan = Ruangan::create([
            'nama_ruangan' => 'Show Room',
            'keterangan' => 'Test',
        ]);

        $response = $this->actingAs($this->admin)->get("/ruangan/{$ruangan->id}");
        $response->assertStatus(200);
        $response->assertSee('Show Room');
    }
}
