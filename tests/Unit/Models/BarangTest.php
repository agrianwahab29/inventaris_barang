<?php

namespace Tests\Unit\Models;

use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarangTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_barang()
    {
        $barang = Barang::factory()->create([
            'nama_barang' => 'Laptop Dell',
            'kategori' => 'Perlengkapan',
            'satuan' => 'unit',
            'stok' => 10,
            'stok_minimum' => 5,
        ]);

        $this->assertDatabaseHas('barangs', [
            'nama_barang' => 'Laptop Dell',
            'kategori' => 'Perlengkapan',
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $barang = new Barang();
        $fillable = ['nama_barang', 'kategori', 'satuan', 'stok', 'stok_minimum', 'catatan'];
        
        $this->assertEquals($fillable, $barang->getFillable());
    }

    /** @test */
    public function it_casts_stok_to_integer()
    {
        $barang = Barang::factory()->create(['stok' => '10']);
        
        $this->assertIsInt($barang->stok);
        $this->assertEquals(10, $barang->stok);
    }

    /** @test */
    public function it_has_many_transaksis()
    {
        $barang = Barang::factory()->create();
        $transaksi = Transaksi::factory()->create(['barang_id' => $barang->id]);

        $this->assertInstanceOf(Transaksi::class, $barang->transaksis->first());
        $this->assertEquals($transaksi->id, $barang->transaksis->first()->id);
    }

    /** @test */
    public function it_detects_low_stock()
    {
        $barang = Barang::factory()->create([
            'stok' => 3,
            'stok_minimum' => 5,
        ]);

        $this->assertTrue($barang->isStokRendah());
        $this->assertFalse($barang->isStokHabis());
    }

    /** @test */
    public function it_detects_empty_stock()
    {
        $barang = Barang::factory()->create([
            'stok' => 0,
            'stok_minimum' => 5,
        ]);

        $this->assertTrue($barang->isStokHabis());
        $this->assertFalse($barang->isStokRendah());
    }

    /** @test */
    public function it_does_not_detect_low_stock_when_above_minimum()
    {
        $barang = Barang::factory()->create([
            'stok' => 10,
            'stok_minimum' => 5,
        ]);

        $this->assertFalse($barang->isStokRendah());
        $this->assertFalse($barang->isStokHabis());
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Barang::create([]);
    }
}
