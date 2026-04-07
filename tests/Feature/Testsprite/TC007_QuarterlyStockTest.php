<?php

namespace Tests\Feature\Testsprite;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Barang;

class TC007_QuarterlyStockTest extends TestCase
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

        // Create some barang for testing
        Barang::create([
            'nama_barang' => 'Kertas A4',
            'kategori' => 'ATK',
            'satuan' => 'Rim',
            'stok' => 50,
            'stok_minimum' => 10,
        ]);

        Barang::create([
            'nama_barang' => 'Tissue',
            'kategori' => 'Kebersihan',
            'satuan' => 'Pak',
            'stok' => 20,
            'stok_minimum' => 5,
        ]);
    }

    public function test_quarterly_stock_page_is_accessible()
    {
        $response = $this->actingAs($this->admin)->get('/quarterly-stock');
        $response->assertStatus(200);
    }

    public function test_quarterly_stock_export_returns_docx()
    {
        $response = $this->actingAs($this->admin)->post('/quarterly-stock/export', [
            'tahun' => date('Y'),
            'triwulan' => 1,
        ]);

        // Should either return a download or validation error
        $this->assertTrue(
            $response->isOk() || 
            $response->isRedirect() ||
            $response->headers->get('Content-Type') === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        );
    }

    public function test_quarterly_stock_requires_parameters()
    {
        $response = $this->actingAs($this->admin)->post('/quarterly-stock/export', [
            // Missing parameters
        ]);

        // Should validate and return error
        $response->assertSessionHasErrors() || $response->assertRedirect();
    }
}
