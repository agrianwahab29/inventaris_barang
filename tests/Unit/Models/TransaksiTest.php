<?php

namespace Tests\Unit\Models;

use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransaksiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_transaksi()
    {
        $barang = Barang::factory()->create();
        $ruangan = Ruangan::factory()->create();
        $user = User::factory()->create();

        $transaksi = Transaksi::factory()->create([
            'barang_id' => $barang->id,
            'ruangan_id' => $ruangan->id,
            'user_id' => $user->id,
            'tipe' => 'masuk',
            'jumlah' => 10,
            'jumlah_masuk' => 10,
            'jumlah_keluar' => 0,
            'stok_sebelum' => 5,
            'stok_setelah_masuk' => 15,
            'tanggal' => now(),
        ]);

        $this->assertDatabaseHas('transaksis', [
            'barang_id' => $barang->id,
            'tipe' => 'masuk',
            'jumlah' => 10,
        ]);
    }

    /** @test */
    public function it_belongs_to_barang()
    {
        $barang = Barang::factory()->create();
        $transaksi = Transaksi::factory()->create(['barang_id' => $barang->id]);

        $this->assertInstanceOf(Barang::class, $transaksi->barang);
        $this->assertEquals($barang->id, $transaksi->barang->id);
    }

    /** @test */
    public function it_belongs_to_ruangan()
    {
        $ruangan = Ruangan::factory()->create();
        $transaksi = Transaksi::factory()->create(['ruangan_id' => $ruangan->id]);

        $this->assertInstanceOf(Ruangan::class, $transaksi->ruangan);
        $this->assertEquals($ruangan->id, $transaksi->ruangan->id);
    }

    /** @test */
    public function it_belongs_to_user()
    {
        $user = User::factory()->create();
        $transaksi = Transaksi::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $transaksi->user);
        $this->assertEquals($user->id, $transaksi->user->id);
    }

    /** @test */
    public function it_has_masuk_scope()
    {
        Transaksi::factory()->create(['tipe' => 'masuk']);
        Transaksi::factory()->create(['tipe' => 'keluar']);
        Transaksi::factory()->create(['tipe' => 'masuk_keluar']);

        $masukTransaksis = Transaksi::masuk()->get();

        $this->assertCount(2, $masukTransaksis);
    }

    /** @test */
    public function it_has_keluar_scope()
    {
        Transaksi::factory()->create(['tipe' => 'masuk']);
        Transaksi::factory()->create(['tipe' => 'keluar']);
        Transaksi::factory()->create(['tipe' => 'masuk_keluar']);

        $keluarTransaksis = Transaksi::keluar()->get();

        $this->assertCount(2, $keluarTransaksis);
    }

    /** @test */
    public function it_casts_tanggal_to_date()
    {
        $transaksi = Transaksi::factory()->create([
            'tanggal' => '2026-04-06',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $transaksi->tanggal);
    }

    /** @test */
    public function it_casts_integer_fields_correctly()
    {
        $transaksi = Transaksi::factory()->create([
            'jumlah' => '10',
            'jumlah_masuk' => '5',
            'jumlah_keluar' => '3',
            'stok_sebelum' => '20',
            'stok_setelah_masuk' => '25',
            'sisa_stok' => '22',
        ]);

        $this->assertIsInt($transaksi->jumlah);
        $this->assertIsInt($transaksi->jumlah_masuk);
        $this->assertIsInt($transaksi->jumlah_keluar);
        $this->assertIsInt($transaksi->stok_sebelum);
        $this->assertIsInt($transaksi->stok_setelah_masuk);
        $this->assertIsInt($transaksi->sisa_stok);
    }

    /** @test */
    public function it_formats_pengambil_with_nama_and_ruangan()
    {
        $ruangan = Ruangan::factory()->create(['nama_ruangan' => 'Ruang Meeting']);
        $transaksi = Transaksi::factory()->create([
            'ruangan_id' => $ruangan->id,
            'nama_pengambil' => 'John Doe',
            'tipe_pengambil' => 'nama_ruangan',
            'jumlah_keluar' => 5,
        ]);

        $this->assertEquals('John Doe / Ruang Meeting', $transaksi->pengambil_formatted);
    }

    /** @test */
    public function it_formats_pengambil_with_only_ruangan()
    {
        $ruangan = Ruangan::factory()->create(['nama_ruangan' => 'Ruang Meeting']);
        $transaksi = Transaksi::factory()->create([
            'ruangan_id' => $ruangan->id,
            'nama_pengambil' => null,
            'tipe_pengambil' => 'ruangan_saja',
            'jumlah_keluar' => 5,
        ]);

        $this->assertEquals('Ruang Meeting', $transaksi->pengambil_formatted);
    }

    /** @test */
    public function it_formats_pengambil_with_only_nama()
    {
        $transaksi = Transaksi::factory()->create([
            'ruangan_id' => null,
            'nama_pengambil' => 'John Doe',
            'tipe_pengambil' => null,
            'jumlah_keluar' => 5,
        ]);

        $this->assertEquals('John Doe', $transaksi->pengambil_formatted);
    }

    /** @test */
    public function it_returns_dash_when_no_keluar()
    {
        $transaksi = Transaksi::factory()->create([
            'jumlah_keluar' => 0,
        ]);

        $this->assertEquals('-', $transaksi->pengambil_formatted);
    }
}
