<?php

namespace Tests\Feature\Controllers;

use App\Models\Ruangan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RuanganControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'pengguna']);
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    /** @test */
    public function authenticated_user_can_view_ruangan_list()
    {
        Ruangan::factory()->count(3)->create();

        $response = $this->actingAs($this->user)->get('/ruangan');

        $response->assertStatus(200);
        $response->assertViewIs('ruangan.index');
        $response->assertViewHas('ruangans');
    }

    /** @test */
    public function authenticated_user_can_view_ruangan_details()
    {
        $ruangan = Ruangan::factory()->create();
        Transaksi::factory()->count(2)->create(['ruangan_id' => $ruangan->id]);

        $response = $this->actingAs($this->user)->get("/ruangan/{$ruangan->id}");

        $response->assertStatus(200);
        $response->assertViewIs('ruangan.show');
        $response->assertViewHas('ruangan');
        $response->assertViewHas('transaksis');
    }

    /** @test */
    public function admin_can_create_ruangan()
    {
        $response = $this->actingAs($this->admin)->post('/ruangan', [
            'nama_ruangan' => 'Ruang Meeting Baru',
            'keterangan' => 'Ruang untuk meeting tim',
        ]);

        $response->assertRedirect('/ruangan');
        $this->assertDatabaseHas('ruangans', [
            'nama_ruangan' => 'Ruang Meeting Baru',
        ]);
    }

    /** @test */
    public function non_admin_cannot_create_ruangan()
    {
        $response = $this->actingAs($this->user)->post('/ruangan', [
            'nama_ruangan' => 'Ruang Meeting Baru',
            'keterangan' => 'Ruang untuk meeting tim',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function creating_ruangan_requires_nama_ruangan()
    {
        $response = $this->actingAs($this->admin)->post('/ruangan', [
            'nama_ruangan' => '',
            'keterangan' => 'Test',
        ]);

        $response->assertSessionHasErrors('nama_ruangan');
    }

    /** @test */
    public function admin_can_update_ruangan()
    {
        $ruangan = Ruangan::factory()->create();

        $response = $this->actingAs($this->admin)->put("/ruangan/{$ruangan->id}", [
            'nama_ruangan' => 'Updated Name',
            'keterangan' => 'Updated keterangan',
        ]);

        $response->assertRedirect('/ruangan');
        $this->assertDatabaseHas('ruangans', [
            'id' => $ruangan->id,
            'nama_ruangan' => 'Updated Name',
        ]);
    }

    /** @test */
    public function non_admin_cannot_update_ruangan()
    {
        $ruangan = Ruangan::factory()->create();

        $response = $this->actingAs($this->user)->put("/ruangan/{$ruangan->id}", [
            'nama_ruangan' => 'Updated Name',
            'keterangan' => 'Updated keterangan',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_delete_ruangan()
    {
        $ruangan = Ruangan::factory()->create();

        $response = $this->actingAs($this->admin)->delete("/ruangan/{$ruangan->id}");

        $response->assertRedirect('/ruangan');
        $this->assertDatabaseMissing('ruangans', [
            'id' => $ruangan->id,
        ]);
    }

    /** @test */
    public function non_admin_cannot_delete_ruangan()
    {
        $ruangan = Ruangan::factory()->create();

        $response = $this->actingAs($this->user)->delete("/ruangan/{$ruangan->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_bulk_delete_ruangan()
    {
        $ruangans = Ruangan::factory()->count(3)->create();
        $ids = $ruangans->pluck('id')->toArray();

        $response = $this->actingAs($this->admin)->delete('/ruangan/bulk/delete', [
            'ids' => $ids,
        ]);

        $response->assertRedirect('/ruangan');
        foreach ($ids as $id) {
            $this->assertDatabaseMissing('ruangans', ['id' => $id]);
        }
    }

    /** @test */
    public function guest_cannot_access_ruangan_routes()
    {
        $response = $this->get('/ruangan');
        $response->assertRedirect('/login');
    }
}
