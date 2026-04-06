<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id',
        'tipe',
        'jumlah',
        'jumlah_masuk',
        'jumlah_keluar',
        'stok_sebelum',
        'stok_setelah_masuk',
        'tanggal',
        'ruangan_id',
        'user_id',
        'sisa_stok',
        'nama_pengambil',
        'tipe_pengambil',
        'tanggal_keluar',
        'keterangan'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_keluar' => 'date',
        'jumlah' => 'integer',
        'jumlah_masuk' => 'integer',
        'jumlah_keluar' => 'integer',
        'stok_sebelum' => 'integer',
        'stok_setelah_masuk' => 'integer',
        'sisa_stok' => 'integer',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeMasuk($query)
    {
        return $query->where('tipe', 'masuk')->orWhere('tipe', 'masuk_keluar');
    }

    public function scopeKeluar($query)
    {
        return $query->where('tipe', 'keluar')->orWhere('tipe', 'masuk_keluar');
    }

    // Accessor untuk format pengambil
    public function getPengambilFormattedAttribute()
    {
        if ($this->jumlah_keluar <= 0) {
            return '-';
        }

        $ruangan = $this->ruangan ? $this->ruangan->nama_ruangan : null;
        $nama = $this->nama_pengambil;

        if ($this->tipe_pengambil === 'nama_ruangan' && $nama && $ruangan) {
            return $nama . ' / ' . $ruangan;
        } elseif ($ruangan) {
            return $ruangan;
        } elseif ($nama) {
            return $nama;
        }

        return '-';
    }
}
