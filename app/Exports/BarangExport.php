<?php

namespace App\Exports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BarangExport implements FromCollection, WithHeadings, WithStyles
{
    protected $kategori;
    protected $status;

    public function __construct($kategori = null, $status = null)
    {
        $this->kategori = $kategori;
        $this->status = $status;
    }

    public function collection()
    {
        $query = Barang::query();

        if ($this->kategori) {
            $query->where('kategori', $this->kategori);
        }

        if ($this->status) {
            if ($this->status == 'habis') {
                $query->where('stok', '<=', 0);
            } elseif ($this->status == 'rendah') {
                $query->whereColumn('stok', '<=', 'stok_minimum')
                    ->where('stok', '>', 0);
            }
        }

        return $query->get()->map(function ($barang, $index) {
            return [
                'No' => $index + 1,
                'Nama Barang' => $barang->nama_barang,
                'Kategori' => $barang->kategori,
                'Satuan' => $barang->satuan,
                'Stok' => $barang->stok,
                'Stok Minimum' => $barang->stok_minimum,
                'Status' => $barang->isStokHabis() ? 'Habis' : ($barang->isStokRendah() ? 'Rendah' : 'Aman'),
                'Catatan' => $barang->catatan ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Barang',
            'Kategori',
            'Satuan',
            'Stok',
            'Stok Minimum',
            'Status',
            'Catatan',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => 'E0E0E0'],
                ],
            ],
        ];
    }
}
