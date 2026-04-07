<?php

namespace Tests\Feature\Testsprite;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\Transaksi;

class TC003_BarangCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

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
    }

    public function test_barang_index_is_accessible()
    {
        $response = $this->actingAs($this->admin)->get('/barang');
        $response->assertStatus(200);
        $response->assertSee('Barang');
    }

    public function test_create_barang_with_valid_data()
    {
        $response = $this->actingAs($this->admin)->post('/barang', [
            'nama_barang' => 'Test Barang',
            'kategori' => 'ATK',
            'satuan' => 'Buah',
            'stok' => 10,
            'stok_minimum' => 5,
            'catatan' => 'Test catatan',
        ]);

        $response->assertRedirect('/barang');
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('barangs', [
            'nama_barang' => 'Test Barang',
            'kategori' => 'ATK',
        ]);
    }

    public function test_create_barang_with_duplicate_name_adds_stock()
    {
        // Create existing barang
        Barang::create([
            'nama_barang' => 'Kertas A4',
            'kategori' => 'ATK',
            'satuan' => 'Rim',
            'stok' => 10,
            'stok_minimum' => 5,
        ]);

        // Post same name - should add stock
        $response = $this->actingAs($this->admin)->post('/barang', [
            'nama_barang' => 'Kertas A4',
            'kategori' => 'ATK',
            'satuan' => 'Rim',
            'stok' => 5,
            'stok_minimum' => 5,
        ]);

        $response->assertRedirect('/barang');
        
        // Stock should be updated to 15 (10 + 5)
        $this->assertDatabaseHas('barangs', [
            'nama_barang' => 'Kertas A4',
            'stok' => 15,
        ]);
    }

    public function test_show_barang_displays_details()
    {
        $barang = Barang::create([
            'nama_barang' => 'Test Barang',
            'kategori' => 'ATK',
            'satuan' => 'Buah',
            'stok' => 10,
            'stok_minimum' => 5,
        ]);

        $response = $this->actingAs($this->admin)->get("/barang/{$barang->id}");
        $response->assertStatus(200);
        $response->assertSee('Test Barang');
    }

    public function test_update_barang()
    {
        $barang = Barang::create([
            'nama_barang' => 'Old Name',
            'kategori' => 'ATK',
            'satuan' => 'Buah',
            'stok' => 10,
            'stok_minimum' => 5,
        ]);

        $response = $this->actingAs($this->admin)->put("/barang/{$barang->id}", [
            'nama_barang' => 'New Name',
            'kategori' => 'Kebersihan',
            'satuan' => 'Pak',
            'stok_minimum' => 3,
        ]);

        $response->assertRedirect('/barang');
        
        $this->assertDatabaseHas('barangs', [
            'id' => $barang->id,
            'nama_barang' => 'New Name',
            'kategori' => 'Kebersihan',
        ]);
    }

    public function test_delete_barang_without_transactions()
    {
        $barang = Barang::create([
            'nama_barang' => 'Delete Me',
            'kategori' => 'ATK',
            'satuan' => 'Buah',
            'stok' => 10,
            'stok_minimum' => 5,
        ]);

        $response = $this->actingAs($this->admin)->delete("/barang/{$barang->id}");
        $response->assertRedirect('/barang');
        $response->assertSessionHas('success');
        
        $this->assertDatabaseMissing('barangs', [
            'id' => $barang->id,
        ]);
    }

    public function test_cannot_delete_barang_with_transactions()
    {
        $ruangan = Ruangan::create(['nama_ruangan' => 'Test Room', 'keterangan' => 'Test']);
        
        $barang = Barang::create([
            'nama_barang' => 'Cannot Delete',
            'kategori' => 'ATK',
            'satuan' => 'Buah',
            'stok' => 10,
            'stok_minimum' => 5,
        ]);

        // Create transaction
        Transaksi::create([
            'barang_id' => $barang->id,
            'tipe' => 'masuk',
            'jumlah_masuk' => 10,
            'jumlah_keluar' => 0,
            'jumlah' => 10,
            'stok_sebelum' => 0,
            'stok_setelah_masuk' => 10,
            'sisa_stok' => 10,
            'tanggal' => now(),
            'user_id' => $this->admin->id,
            'ruangan_id' => $ruangan->id,
        ]);

        $response = $this->actingAs($this->admin)->delete("/barang/{$barang->id}");
        $response->assertSessionHas('error');
        
        $this->assertDatabaseHas('barangs', [
            'id' => $barang->id,
        ]);
    }

    public function test_update_stok_endpoint()
    {
        $barang = Barang::create([
            'nama_barang' => 'Stock Test',
            'kategori' => 'ATK',
            'satuan' => 'Buah',
            'stok' => 10,
            'stok_minimum' => 5,
        ]);

        $response = $this->actingAs($this->admin)->postJson("/barang/{$barang->id}/update-stok", [
            'stok_baru' => 20,
            'keterangan' => 'Test update stok',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        
        $this->assertDatabaseHas('barangs', [
            'id' => $barang->id,
            'stok' => 20,
        ]);
    }
}
