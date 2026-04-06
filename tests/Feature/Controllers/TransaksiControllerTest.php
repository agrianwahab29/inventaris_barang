<?php

namespace Tests\Feature\Controllers;

use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransaksiControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $barang;
    protected $ruangan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->barang = Barang::factory()->create(['stok' => 50]);
        $this->ruangan = Ruangan::factory()->create();
    }

    /** @test */
    public function authenticated_user_can_view_transaksi_list()
    {
        Transaksi::factory()->count(3)->create();

        $response = $this->actingAs($this->user)->get('/transaksi');

        $response->assertStatus(200);
        $response->assertViewIs('transaksi.index');
        $response->assertViewHas('transaksis');
    }

    /** @test */
    public function authenticated_user_can_view_transaksi_details()
    {
        $transaksi = Transaksi::factory()->create();

        $response = $this->actingAs($this->user)->get("/transaksi/{$transaksi->id}");

        $response->assertStatus(200);
        $response->assertViewIs('transaksi.show');
        $response->assertViewHas('transaksi');
    }

    /** @test */
    public function authenticated_user_can_create_transaksi_masuk()
    {
        $response = $this->actingAs($this->user)->post('/transaksi', [
            'barang_id' => $this->barang->id,
            'tipe' => 'masuk',
            'jumlah' => 10,
            'jumlah_masuk' => 10,
            'jumlah_keluar' => 0,
            'stok_sebelum' => 50,
            'stok_setelah_masuk' => 60,
            'tanggal' => now()->format('Y-m-d'),
            'ruangan_id' => $this->ruangan->id,
            'keterangan' => 'Barang masuk dari supplier',
        ]);

        $response->assertRedirect('/transaksi');
        $this->assertDatabaseHas('transaksis', [
            'barang_id' => $this->barang->id,
            'tipe' => 'masuk',
            'jumlah' => 10,
        ]);
    }

    /** @test */
    public function authenticated_user_can_create_transaksi_keluar()
    {
        $response = $this->actingAs($this->user)->post('/transaksi', [
            'barang_id' => $this->barang->id,
            'tipe' => 'keluar',
            'jumlah' => 5,
            'jumlah_masuk' => 0,
            'jumlah_keluar' => 5,
            'stok_sebelum' => 50,
            'stok_setelah_masuk' => 50,
            'sisa_stok' => 45,
            'tanggal' => now()->format('Y-m-d'),
            'tanggal_keluar' => now()->format('Y-m-d'),
            'ruangan_id' => $this->ruangan->id,
            'nama_pengambil' => 'John Doe',
            'tipe_pengambil' => 'nama_ruangan',
            'keterangan' => 'Barang keluar untuk keperluan meeting',
        ]);

        $response->assertRedirect('/transaksi');
        $this->assertDatabaseHas('transaksis', [
            'barang_id' => $this->barang->id,
            'tipe' => 'keluar',
            'jumlah_keluar' => 5,
        ]);
    }

    /** @test */
    public function creating_transaksi_requires_barang_id()
    {
        $response = $this->actingAs($this->user)->post('/transaksi', [
            'barang_id' => '',
            'tipe' => 'masuk',
            'jumlah' => 10,
        ]);

        $response->assertSessionHasErrors('barang_id');
    }

    /** @test */
    public function creating_transaksi_requires_valid_tipe()
    {
        $response = $this->actingAs($this->user)->post('/transaksi', [
            'barang_id' => $this->barang->id,
            'tipe' => 'invalid_type',
            'jumlah' => 10,
        ]);

        $response->assertSessionHasErrors('tipe');
    }

    /** @test */
    public function creating_transaksi_requires_jumlah()
    {
        $response = $this->actingAs($this->user)->post('/transaksi', [
            'barang_id' => $this->barang->id,
            'tipe' => 'masuk',
            'jumlah' => '',
        ]);

        $response->assertSessionHasErrors('jumlah');
    }

    /** @test */
    public function jumlah_must_be_positive_integer()
    {
        $response = $this->actingAs($this->user)->post('/transaksi', [
            'barang_id' => $this->barang->id,
            'tipe' => 'masuk',
            'jumlah' => -5,
        ]);

        $response->assertSessionHasErrors('jumlah');
    }

    /** @test */
    public function authenticated_user_can_update_transaksi()
    {
        $transaksi = Transaksi::factory()->create([
            'barang_id' => $this->barang->id,
            'jumlah' => 10,
        ]);

        $response = $this->actingAs($this->user)->put("/transaksi/{$transaksi->id}", [
            'barang_id' => $this->barang->id,
            'tipe' => 'masuk',
            'jumlah' => 15,
            'jumlah_masuk' => 15,
            'jumlah_keluar' => 0,
            'stok_sebelum' => 50,
            'stok_setelah_masuk' => 65,
            'tanggal' => now()->format('Y-m-d'),
            'ruangan_id' => $this->ruangan->id,
            'keterangan' => 'Updated transaction',
        ]);

        $response->assertRedirect('/transaksi');
        $this->assertDatabaseHas('transaksis', [
            'id' => $transaksi->id,
            'jumlah' => 15,
        ]);
    }

    /** @test */
    public function authenticated_user_can_delete_transaksi()
    {
        $transaksi = Transaksi::factory()->create();

        $response = $this->actingAs($this->user)->delete("/transaksi/{$transaksi->id}");

        $response->assertRedirect('/transaksi');
        $this->assertDatabaseMissing('transaksis', [
            'id' => $transaksi->id,
        ]);
    }

    /** @test */
    public function authenticated_user_can_bulk_delete_transaksi()
    {
        $transaksis = Transaksi::factory()->count(3)->create();
        $ids = $transaksis->pluck('id')->toArray();

        $response = $this->actingAs($this->user)->delete('/transaksi/bulk/delete', [
            'ids' => $ids,
        ]);

        $response->assertRedirect('/transaksi');
        foreach ($ids as $id) {
            $this->assertDatabaseMissing('transaksis', ['id' => $id]);
        }
    }

    /** @test */
    public function guest_cannot_access_transaksi_routes()
    {
        $response = $this->get('/transaksi');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function it_calculates_stok_correctly_for_masuk()
    {
        $initialStok = $this->barang->stok;
        $jumlahMasuk = 20;

        $this->actingAs($this->user)->post('/transaksi', [
            'barang_id' => $this->barang->id,
            'tipe' => 'masuk',
            'jumlah' => $jumlahMasuk,
            'jumlah_masuk' => $jumlahMasuk,
            'jumlah_keluar' => 0,
            'stok_sebelum' => $initialStok,
            'stok_setelah_masuk' => $initialStok + $jumlahMasuk,
            'tanggal' => now()->format('Y-m-d'),
            'ruangan_id' => $this->ruangan->id,
        ]);

        $this->barang->refresh();
        $this->assertEquals($initialStok + $jumlahMasuk, $this->barang->stok);
    }
}
