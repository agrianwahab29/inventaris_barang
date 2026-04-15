<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BerkasTransaksi extends Model
{
    use HasFactory;

    protected $table = 'berkas_transaksis';

    protected $fillable = [
        'nomor_surat',
        'tanggal_surat',
        'perihal',
        'pengirim',
        'penerima',
        'user_id',
        'file_path',
        'file_name',
        'file_size',
        'file_mime',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
    ];

    /**
     * Relasi ke User (uploader)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope untuk pencarian berdasarkan nomor surat
     */
    public function scopeByNomorSurat($query, $nomor)
    {
        return $query->where('nomor_surat', 'like', '%' . $nomor . '%');
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeByTanggalRange($query, $dari, $sampai)
    {
        return $query->whereBetween('tanggal_surat', [$dari, $sampai]);
    }

    /**
     * Scope untuk filter berdasarkan bulan
     */
    public function scopeByBulan($query, $tahun, $bulan)
    {
        return $query->whereYear('tanggal_surat', $tahun)
                     ->whereMonth('tanggal_surat', $bulan);
    }

    /**
     * Scope untuk filter berdasarkan tahun
     */
    public function scopeByTahun($query, $tahun)
    {
        return $query->whereYear('tanggal_surat', $tahun);
    }

    /**
     * Accessor untuk format file size human readable
     */
    public function getFileSizeHumanAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Accessor untuk format tanggal
     */
    public function getTanggalSuratFormattedAttribute()
    {
        return $this->tanggal_surat ? $this->tanggal_surat->format('d F Y') : '-';
    }

    /**
     * Get file URL
     */
    public function getFileUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }

    /**
     * Get file extension
     */
    public function getFileExtensionAttribute()
    {
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }
}
