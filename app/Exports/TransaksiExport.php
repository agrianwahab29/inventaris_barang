<?php

namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class TransaksiExport implements FromCollection, WithHeadings, WithStyles, WithMapping, ShouldAutoSize, WithStrictNullComparison
{
    protected $exportType;
    protected $tanggalDari;
    protected $tanggalSampai;
    protected $tanggalList;
    protected $userId;
    protected $tahun;
    protected $tahunDari;
    protected $tahunSampai;
    protected $bulan;
    protected $bulanDari;
    protected $bulanSampai;
    protected $tahunBulan;
    private $rowNumber = 0;

    public function __construct(
        $exportType = 'all', 
        $tanggalDari = null, 
        $tanggalSampai = null, 
        $tanggalList = null, 
        $userId = null,
        $tahun = null,
        $tahunDari = null,
        $tahunSampai = null,
        $bulan = null,
        $bulanDari = null,
        $bulanSampai = null,
        $tahunBulan = null
    )
    {
        $this->exportType = $exportType;
        $this->tanggalDari = $tanggalDari;
        $this->tanggalSampai = $tanggalSampai;
        $this->tanggalList = $tanggalList;
        $this->userId = $userId;
        $this->tahun = $tahun;
        $this->tahunDari = $tahunDari;
        $this->tahunSampai = $tahunSampai;
        $this->bulan = $bulan;
        $this->bulanDari = $bulanDari;
        $this->bulanSampai = $bulanSampai;
        $this->tahunBulan = $tahunBulan;
    }

    public function collection()
    {
        $query = Transaksi::with(['barang', 'ruangan', 'user']);

        if ($this->userId) {
            $query->where('user_id', $this->userId);
        }

        switch ($this->exportType) {
            case 'range':
                if ($this->tanggalDari) {
                    $query->whereDate('tanggal', '>=', $this->tanggalDari);
                }
                if ($this->tanggalSampai) {
                    $query->whereDate('tanggal', '<=', $this->tanggalSampai);
                }
                break;

            case 'dates':
                if ($this->tanggalList) {
                    $tanggalArray = explode(',', $this->tanggalList);
                    $query->whereIn(DB::raw('DATE(tanggal)'), $tanggalArray);
                }
                break;

            case 'year':
                if ($this->tahun) {
                    // SQLite: use strftime instead of whereYear
                    $query->whereRaw("strftime('%Y', tanggal) = ?", [(string)$this->tahun]);
                }
                break;

            case 'year_range':
                if ($this->tahunDari) {
                    $query->whereRaw("strftime('%Y', tanggal) >= ?", [(string)$this->tahunDari]);
                }
                if ($this->tahunSampai) {
                    $query->whereRaw("strftime('%Y', tanggal) <= ?", [(string)$this->tahunSampai]);
                }
                break;

            case 'month':
                if ($this->tahunBulan && $this->bulan) {
                    // SQLite: use strftime for both year and month
                    $bulanPadded = str_pad($this->bulan, 2, '0', STR_PAD_LEFT);
                    $query->whereRaw("strftime('%Y', tanggal) = ?", [(string)$this->tahunBulan])
                          ->whereRaw("strftime('%m', tanggal) = ?", [$bulanPadded]);
                }
                break;

            case 'month_range':
                // Month range: from (tahun_dari, bulan_dari) to (tahun_sampai, bulan_sampai)
                // Use the correct parameters: tahun_dari, bulan_dari, tahun_sampai, bulan_sampai
                if ($this->tahunDari && $this->bulanDari && $this->tahunSampai && $this->bulanSampai) {
                    // Build date range for SQLite
                    // Start date: first day of start month
                    $startDate = $this->tahunDari . '-' . str_pad($this->bulanDari, 2, '0', STR_PAD_LEFT) . '-01';
                    
                    // End date: we need to get all records until the end of the end month
                    // So we use < first day of next month
                    $endMonth = (int)$this->bulanSampai;
                    $endYear = (int)$this->tahunSampai;
                    
                    if ($endMonth == 12) {
                        // December -> January of next year
                        $endDate = ($endYear + 1) . '-01-01';
                    } else {
                        $endDate = $endYear . '-' . str_pad($endMonth + 1, 2, '0', STR_PAD_LEFT) . '-01';
                    }
                    
                    $query->whereRaw("date(tanggal) >= date(?)", [$startDate])
                          ->whereRaw("date(tanggal) < date(?)", [$endDate]);
                }
                break;
        }

        $data = $query->orderBy('created_at', 'desc')->get();
        
        // Ensure integer values for numeric fields
        $data->transform(function ($item) {
            $item->jumlah_masuk = (int) $item->jumlah_masuk;
            $item->jumlah_keluar = (int) $item->jumlah_keluar;
            $item->sisa_stok = (int) ($item->sisa_stok ?? 0);
            return $item;
        });
        
        return $data;
    }

    public function map($transaksi): array
    {
        $this->rowNumber++;
        
        $jumlahMasuk = $transaksi->jumlah_masuk ?? 0;
        $jumlahKeluar = $transaksi->jumlah_keluar ?? 0;
        
        // Format pengambil: nama_pengambil atau ruangan atau keduanya
        $pengambil = '-';
        if ($transaksi->nama_pengambil && $transaksi->ruangan) {
            $pengambil = $transaksi->nama_pengambil . ' - ' . $transaksi->ruangan->nama_ruangan;
        } elseif ($transaksi->nama_pengambil) {
            $pengambil = $transaksi->nama_pengambil;
        } elseif ($transaksi->ruangan) {
            $pengambil = $transaksi->ruangan->nama_ruangan;
        }
        
        return [
            $this->rowNumber,
            $transaksi->tanggal->format('d/m/Y'),
            $transaksi->barang->nama_barang,
            $jumlahMasuk,
            $jumlahKeluar,
            (int) ($transaksi->sisa_stok ?? 0),
            $transaksi->barang->satuan,
            $transaksi->tanggal_keluar ? $transaksi->tanggal_keluar->format('d/m/Y') : '-',
            $pengambil,
            $transaksi->user->name,
            '', // Paraf dikosongkan untuk dicetak
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tanggal Input',
            'Nama Barang',
            'Jumlah Barang Masuk',
            'Jumlah Barang Keluar',
            'Sisa Stok Barang',
            'Satuan',
            'Tanggal Keluar',
            'Nama atau Bagian/Ruang yang Mengambil',
            'User Input',
            'Paraf',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['rgb' => '4F46E5'],
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ]);

        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:K' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => 'thin',
                    'color' => ['rgb' => 'E5E7EB'],
                ],
            ],
        ]);

        for ($row = 2; $row <= $lastRow; $row++) {
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':K' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => 'F9FAFB'],
                    ],
                ]);
            }
        }

        return [];
    }
}
