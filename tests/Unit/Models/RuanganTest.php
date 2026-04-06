<?php

namespace Tests\Unit\Models;

use App\Models\Ruangan;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RuanganTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_ruangan()
    {
        $ruangan = Ruangan::factory()->create([
            'nama_ruangan' => 'Ruang Meeting A',
            'keterangan' => 'Ruang meeting untuk 10 orang',
        ]);

        $this->assertDatabaseHas('ruangans', [
            'nama_ruangan' => 'Ruang Meeting A',
            'keterangan' => 'Ruang meeting untuk 10 orang',
        ]);
    }

    /** @test */
    public function it_has_many_transaksis()
    {
        $ruangan = Ruangan::factory()->create();
        $transaksi = Transaksi::factory()->create(['ruangan_id' => $ruangan->id]);

        $this->assertInstanceOf(Transaksi::class, $ruangan->transaksis->first());
        $this->assertEquals($transaksi->id, $ruangan->transaksis->first()->id);
    }

    /** @test */
    public function nama_ruangan_is_required()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Ruangan::create(['keterangan' => 'Test']);
    }
}
