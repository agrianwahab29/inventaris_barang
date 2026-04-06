<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuarterlyStockOpname extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id',
        'tahun',
        'quarter',
        'stok_opname',
        'tanggal_opname',
        'user_id',
        'catatan'
    ];

    protected $casts = [
        'tanggal_opname' => 'date',
        'tahun' => 'integer',
        'stok_opname' => 'integer',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Get quarter label
    public function getQuarterLabelAttribute()
    {
        $labels = [
            'Q1' => 'Januari - Maret',
            'Q2' => 'April - Juni',
            'Q3' => 'Juli - September',
            'Q4' => 'Oktober - Desember'
        ];
        return $labels[$this->quarter] ?? $this->quarter;
    }

    // Get quarter months
    public static function getQuarterMonths($quarter)
    {
        $months = [
            'Q1' => [1, 2, 3],
            'Q2' => [4, 5, 6],
            'Q3' => [7, 8, 9],
            'Q4' => [10, 11, 12]
        ];
        return $months[$quarter] ?? [];
    }

    // Get quarter start and end dates
    public static function getQuarterDateRange($tahun, $quarter)
    {
        $ranges = [
            'Q1' => ["{$tahun}-01-01", "{$tahun}-03-31"],
            'Q2' => ["{$tahun}-04-01", "{$tahun}-06-30"],
            'Q3' => ["{$tahun}-07-01", "{$tahun}-09-30"],
            'Q4' => ["{$tahun}-10-01", "{$tahun}-12-31"]
        ];
        return $ranges[$quarter] ?? [null, null];
    }
}
