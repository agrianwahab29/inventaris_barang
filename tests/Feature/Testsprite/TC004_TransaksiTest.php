<?php

namespace Tests\Feature\Testsprite;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\Transaksi;

class TC004_TransaksiTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $barang;
    protected $ruangan;

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

        $this->barang = Barang::create([
            'nama_barang' => 'Test Barang',
            'kategori' => 'ATK',
            'satuan' => 'Buah',
            'stok' => 100,
            'stok_minimum' => 10,
        ]);

        $this->ruangan = Ruangan::create([
            'nama_ruangan' => 'Test Room',
            'keterangan' => 'Test',
        ]);
    }

    public function test_transaksi_index_is_accessible()
    {
        $response = $this->actingAs($this->admin)->get('/transaksi');
        $response->assertStatus(200);
        $response->assertSee('Transaksi');
    }

    public function test_create_transaksi_masuk()
    {
        $response = $this->actingAs($this->admin)->post('/transaksi', [
            'barang_id' => $this->barang->id,
            'jumlah_masuk' => 50,
            'jumlah_keluar' => 0,
            'tanggal_masuk' => now()->format('Y-m-d'),
            'keterangan' => 'Test masuk',
        ]);

        $response->assertRedirect('/transaksi');
        $response->assertSessionHas('success');
        
        // Check barang stock increased
        $this->barang->refresh();
        $this->assertEquals(150, $this->barang->stok); // 100 + 50
        
        // Check transaction created
        $this->assertDatabaseHas('transaksis', [
            'barang_id' => $this->barang->id,
            'tipe' => 'masuk',
            'jumlah_masuk' => 50,
        ]);
    }

    public function test_create_transaksi_keluar()
    {
        $response = $this->actingAs($this->admin)->post('/transaksi', [
            'barang_id' => $this->barang->id,
            'jumlah_masuk' => 0,
            'jumlah_keluar' => 30,
            'tanggal_keluar' => now()->format('Y-m-d'),
            'ruangan_id' => $this->ruangan->id,
            'nama_pengambil' => 'John Doe',
            'tipe_pengambil' => 'nama_ruangan',
            'keterangan' => 'Test keluar',
        ]);

        $response->assertRedirect('/transaksi');
        
        // Check barang stock decreased
        $this->barang->refresh();
        $this->assertEquals(70, $this->barang->stok); // 100 - 30
        
        // Check transaction created
        $this->assertDatabaseHas('transaksis', [
            'barang_id' => $this->barang->id,
            'tipe' => 'keluar',
            'jumlah_keluar' => 30,
            'ruangan_id' => $this->ruangan->id,
        ]);
    }

    public function test_create_transaksi_masuk_keluar()
    {
        $response = $this->actingAs($this->admin)->post('/transaksi', [
            'barang_id' => $this->barang->id,
            'jumlah_masuk' => 50,
            'jumlah_keluar' => 20,
            'tanggal_masuk' => now()->format('Y-m-d'),
            'tanggal_keluar' => now()->format('Y-m-d'),
            'ruangan_id' => $this->ruangan->id,
            'keterangan' => 'Test masuk keluar',
        ]);

        $response->assertRedirect('/transaksi');
        
        // Check barang stock: 100 + 50 - 20 = 130
        $this->barang->refresh();
        $this->assertEquals(130, $this->barang->stok);
        
        // Check transaction type
        $this->assertDatabaseHas('transaksis', [
            'barang_id' => $this->barang->id,
            'tipe' => 'masuk_keluar',
        ]);
    }

    public function test_cannot_create_transaksi_with_insufficient_stock()
    {
        $response = $this->actingAs($this->admin)->post('/transaksi', [
            'barang_id' => $this->barang->id,
            'jumlah_masuk' => 0,
            'jumlah_keluar' => 200, // More than stock (100)
            'tanggal_keluar' => now()->format('Y-m-d'),
            'ruangan_id' => $this->ruangan->id,
        ]);

        $response->assertSessionHasErrors('jumlah_keluar');
        
        // Stock should remain unchanged
        $this->barang->refresh();
        $this->assertEquals(100, $this->barang->stok);
    }

    public function test_delete_transaksi_recalculates_stock()
    {
        // Create transaction
        $transaksi = Transaksi::create([
            'barang_id' => $this->barang->id,
            'tipe' => 'masuk',
            'jumlah_masuk' => 50,
            'jumlah_keluar' => 0,
            'jumlah' => 50,
            'stok_sebelum' => 100,
            'stok_setelah_masuk' => 150,
            'sisa_stok' => 150,
            'tanggal' => now(),
            'user_id' => $this->admin->id,
        ]);

        $this->barang->update(['stok' => 150]);

        $response = $this->actingAs($this->admin)->delete("/transaksi/{$transaksi->id}");
        $response->assertRedirect('/transaksi');
        
        // Stock should be recalculated back to original
        $this->barang->refresh();
        $this->assertEquals(100, $this->barang->stok);
    }

    public function test_api_barang_info()
    {
        $response = $this->actingAs($this->admin)->getJson("/api/barang/{$this->barang->id}/info");
        
        $response->assertStatus(200);
        $response->assertJson([
            'stok' => 100,
            'satuan' => 'Buah',
            'stok_minimum' => 10,
        ]);
    }
}
