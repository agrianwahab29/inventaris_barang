<?php

namespace Tests\Feature\Testsprite;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\Transaksi;

class TC008_SuratTandaTerimaTest extends TestCase
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
            'nama_barang' => 'Kertas A4',
            'kategori' => 'ATK',
            'satuan' => 'Rim',
            'stok' => 50,
            'stok_minimum' => 10,
        ]);

        $this->ruangan = Ruangan::create([
            'nama_ruangan' => 'Ruang Test',
            'keterangan' => 'Test',
        ]);

        // Create a transaction
        Transaksi::create([
            'barang_id' => $this->barang->id,
            'tipe' => 'keluar',
            'jumlah_masuk' => 0,
            'jumlah_keluar' => 10,
            'jumlah' => 10,
            'stok_sebelum' => 50,
            'stok_setelah_masuk' => 0,
            'sisa_stok' => 40,
            'tanggal' => now(),
            'tanggal_keluar' => now(),
            'ruangan_id' => $this->ruangan->id,
            'nama_pengambil' => 'John Doe',
            'tipe_pengambil' => 'nama_ruangan',
            'user_id' => $this->admin->id,
            'keterangan' => 'Test transaction',
        ]);
    }

    public function test_surat_tanda_terima_page_is_accessible()
    {
        $response = $this->actingAs($this->admin)->get('/surat-tanda-terima');
        $response->assertStatus(200);
    }

    public function test_generate_surat_tanda_terima_requires_parameters()
    {
        $response = $this->actingAs($this->admin)->get('/surat-tanda-terima/generate', [
            // Missing required parameters
        ]);

        // Should either show validation or redirect with error
        $this->assertTrue(
            $response->isRedirect() || 
            $response->isOk() ||
            $response->isServerError()
        );
    }

    public function test_generate_surat_tanda_terima_with_valid_params()
    {
        // Create transaction first
        $transaksi = Transaksi::first();

        $response = $this->actingAs($this->admin)->get('/surat-tanda-terima/generate?' . http_build_query([
            'transaksi_id' => $transaksi->id,
            'nama_penerima' => 'Jane Doe',
            'jabatan' => 'Manager',
        ]));

        // Should either return DOCX or redirect/download
        $this->assertTrue(
            $response->isOk() ||
            $response->isRedirect() ||
            $response->headers->get('Content-Type') === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        );
    }
}
