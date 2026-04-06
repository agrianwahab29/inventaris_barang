<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_barang',
        'kategori',
        'satuan',
        'stok',
        'stok_minimum',
        'catatan'
    ];

    protected $casts = [
        'stok' => 'integer',
        'stok_minimum' => 'integer',
    ];

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class);
    }

    public function isStokRendah()
    {
        return $this->stok <= $this->stok_minimum && $this->stok > 0;
    }

    public function isStokHabis()
    {
        return $this->stok <= 0;
    }
}
