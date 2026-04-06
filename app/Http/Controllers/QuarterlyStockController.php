<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuarterlyStockOpname;
use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class QuarterlyStockController extends Controller
{
    /**
     * Display the quarterly stock opname listing
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $selectedTahun = $request->input('tahun', date('Y'));
        $selectedQuarter = $request->input('quarter', $this->getCurrentQuarter());

        // Cache the heavy queries for 5 minutes
        $cacheKey = "quarterly_stock_{$selectedTahun}_{$selectedQuarter}";
        
        $cachedData = Cache::remember($cacheKey, 300, function () use ($selectedTahun, $selectedQuarter) {
            // Get available years from transactions only
            $availableYears = Transaksi::selectRaw("strftime('%Y', tanggal) as tahun")
                ->distinct()
                ->orderBy('tahun', 'desc')
                ->pluck('tahun')
                ->toArray();
            
            // If no transactions, add current year
            if (empty($availableYears)) {
                $availableYears[] = date('Y');
            }
            $availableYears = array_unique($availableYears);
            rsort($availableYears);

            // Get quarter date range
            $quarterRange = QuarterlyStockOpname::getQuarterDateRange($selectedTahun, $selectedQuarter);
            
            // Get ACTUAL transaction date range within the quarter
            $actualStart = Transaksi::whereDate('tanggal', '>=', $quarterRange[0])
                ->whereDate('tanggal', '<=', $quarterRange[1])
                ->min('tanggal');
            
            $actualEnd = Transaksi::whereDate('tanggal', '>=', $quarterRange[0])
                ->whereDate('tanggal', '<=', $quarterRange[1])
                ->max('tanggal');

            // OPTIMIZED: Get all barang stock data in single query (fixes N+1 problem)
            $barangData = Barang::select([
                    'barangs.id',
                    'barangs.nama_barang',
                    'barangs.satuan',
                    \DB::raw('COALESCE(SUM(transaksis.jumlah_masuk), 0) - COALESCE(SUM(transaksis.jumlah_keluar), 0) as stok_opname')
                ])
                ->join('transaksis', 'barangs.id', '=', 'transaksis.barang_id')
                ->whereDate('transaksis.tanggal', '<=', $quarterRange[1])
                ->whereExists(function ($query) use ($quarterRange) {
                    $query->select(\DB::raw(1))
                        ->from('transaksis as t2')
                        ->whereColumn('t2.barang_id', 'barangs.id')
                        ->whereDate('t2.tanggal', '>=', $quarterRange[0])
                        ->whereDate('t2.tanggal', '<=', $quarterRange[1]);
                })
                ->groupBy('barangs.id', 'barangs.nama_barang', 'barangs.satuan')
                ->havingRaw('stok_opname > 0')
                ->orderBy('barangs.nama_barang')
                ->get()
                ->map(function ($item) {
                    return (object)[
                        'id' => $item->id,
                        'nama_barang' => $item->nama_barang,
                        'satuan' => $item->satuan,
                        'stok_opname' => $item->stok_opname,
                    ];
                });

            // Determine actual period label
            $periodLabel = $this->getPeriodLabel($actualStart, $actualEnd, $selectedQuarter, $selectedTahun);

            return compact('barangData', 'availableYears', 'actualStart', 'actualEnd', 'periodLabel');
        });

        // Get quarter labels (static, no need to cache)
        $quarters = [
            'Q1' => 'Januari - Maret',
            'Q2' => 'April - Juni',
            'Q3' => 'Juli - September',
            'Q4' => 'Oktober - Desember',
        ];

        return view('quarterly-stock.index', array_merge($cachedData, [
            'selectedTahun' => $selectedTahun,
            'selectedQuarter' => $selectedQuarter,
            'quarters' => $quarters,
        ]));
    }

    /**
     * Get period label based on actual transaction dates
     */
    private function getPeriodLabel($actualStart, $actualEnd, $quarter, $tahun)
    {
        $bulanIndo = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        if (!$actualStart || !$actualEnd) {
            return "Triwulan $quarter $tahun (Tidak ada transaksi)";
        }

        $startMonth = date('n', strtotime($actualStart));
        $endMonth = date('n', strtotime($actualEnd));
        $startYear = date('Y', strtotime($actualStart));
        $endYear = date('Y', strtotime($actualEnd));

        if ($startMonth == $endMonth && $startYear == $endYear) {
            // Same month
            return "Bulan {$bulanIndo[$startMonth]} $startYear";
        } elseif ($startYear == $endYear) {
            // Same year, different months
            return "Bulan {$bulanIndo[$startMonth]} - {$bulanIndo[$endMonth]} $startYear";
        } else {
            // Different years
            return "Bulan {$bulanIndo[$startMonth]} $startYear - {$bulanIndo[$endMonth]} $endYear";
        }
    }

    /**
     * Export to DOCX
     */
    public function exportDocx(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer|min:2000|max:2100',
            'quarter' => 'required|in:Q1,Q2,Q3,Q4',
            'mengetahui_jabatan' => 'required|string|max:255',
            'mengetahui_nama' => 'required|string|max:255',
            'mengetahui_nip' => 'required|string|max:50',
            'penyusun_jabatan' => 'nullable|string|max:255',
            'penyusun_nama' => 'nullable|string|max:255',
            'penyusun_nip' => 'nullable|string|max:50',
        ]);

        $selectedTahun = $request->tahun;
        $selectedQuarter = $request->quarter;

        // Get quarter date range
        $quarterRange = QuarterlyStockOpname::getQuarterDateRange($selectedTahun, $selectedQuarter);
        
        // Get ACTUAL transaction date range within the quarter
        $actualStart = Transaksi::whereDate('tanggal', '>=', $quarterRange[0])
            ->whereDate('tanggal', '<=', $quarterRange[1])
            ->min('tanggal');
        
        $actualEnd = Transaksi::whereDate('tanggal', '>=', $quarterRange[0])
            ->whereDate('tanggal', '<=', $quarterRange[1])
            ->max('tanggal');

        // Get period label for document
        $periodLabel = $this->getPeriodLabelForDoc($actualStart, $actualEnd, $selectedQuarter, $selectedTahun);

        // OPTIMIZED: Get all barang stock data in single query (fixes N+1 problem)
        $barangData = Barang::select([
                'barangs.id',
                'barangs.nama_barang',
                'barangs.satuan',
                \DB::raw('COALESCE(SUM(transaksis.jumlah_masuk), 0) - COALESCE(SUM(transaksis.jumlah_keluar), 0) as stok_opname')
            ])
            ->join('transaksis', 'barangs.id', '=', 'transaksis.barang_id')
            ->whereDate('transaksis.tanggal', '<=', $quarterRange[1])
            ->whereExists(function ($query) use ($quarterRange) {
                $query->select(\DB::raw(1))
                    ->from('transaksis as t2')
                    ->whereColumn('t2.barang_id', 'barangs.id')
                    ->whereDate('t2.tanggal', '>=', $quarterRange[0])
                    ->whereDate('t2.tanggal', '<=', $quarterRange[1]);
            })
            ->groupBy('barangs.id', 'barangs.nama_barang', 'barangs.satuan')
            ->havingRaw('stok_opname > 0')
            ->orderBy('barangs.nama_barang')
            ->get()
            ->map(function ($item) {
                return (object)[
                    'nama_barang' => $item->nama_barang,
                    'satuan' => $item->satuan,
                    'stok_opname' => $item->stok_opname,
                ];
            });

        // Create PHPWord object
        $phpWord = new PhpWord();
        
        // Set document properties
        $phpWord->getDocInfo()->setCreator('Sistem Inventaris');
        $phpWord->getDocInfo()->setTitle('Laporan Stok Opname ATK');
        
        // Add section
        $section = $phpWord->addSection([
            'marginTop' => Converter::cmToTwip(2),
            'marginBottom' => Converter::cmToTwip(2),
            'marginLeft' => Converter::cmToTwip(2.5),
            'marginRight' => Converter::cmToTwip(2.5),
        ]);

        // Set default font for the document
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(12);
        // Set default paragraph style: before after 0, line spacing 1.5
        $phpWord->setDefaultParagraphStyle([
            'spaceBefore' => 0,
            'spaceAfter' => 0,
            'lineHeight' => 1.5,
        ]);

        // Add header - centered
        $section->addText('LAPORAN STOK OPNAME ATK', [
            'bold' => true,
            'size' => 14,
            'name' => 'Times New Roman',
        ], [
            'alignment' => 'center',
            'spaceAfter' => 0,
        ]);

        $section->addText($periodLabel, [
            'bold' => true,
            'size' => 14,
            'name' => 'Times New Roman',
        ], [
            'alignment' => 'center',
            'spaceAfter' => 400,
        ]);

        // Create table with all borders
        $table = $section->addTable([
            'width' => 100 * 50,
            'unit' => 'pct',
            'borderSize' => 8,
            'borderColor' => '000000',
            'borderInsideSize' => 8,
            'borderInsideColor' => '000000',
        ]);

        // Table header row with borders
        $headerRow = $table->addRow();
        $cellStyle = ['vAlign' => 'center'];
        $textStyleBold = ['bold' => true, 'size' => 12];
        $centerAlign = ['alignment' => 'center'];

        $cell1 = $headerRow->addCell(500, $cellStyle);
        $cell1->addText('NO', $textStyleBold, $centerAlign);
        
        $cell2 = $headerRow->addCell(4000, $cellStyle);
        $cell2->addText('NAMA BARANG', $textStyleBold, $centerAlign);
        
        $cell3 = $headerRow->addCell(2000, $cellStyle);
        $cell3->addText('SATUAN', $textStyleBold, $centerAlign);
        
        $cell4 = $headerRow->addCell(2000, $cellStyle);
        $cell4->addText('STOK OPNAME (SO)', $textStyleBold, $centerAlign);

        // Data rows
        $no = 1;
        foreach ($barangData as $item) {
            $dataRow = $table->addRow();
            $dataRow->addCell(500)->addText($no++, ['size' => 12], ['alignment' => 'center']);
            $dataRow->addCell(4000)->addText($item->nama_barang, ['size' => 12], ['alignment' => 'left']);
            $dataRow->addCell(2000)->addText($item->satuan, ['size' => 12], ['alignment' => 'center']);
            $dataRow->addCell(2000)->addText(number_format($item->stok_opname), ['size' => 12], ['alignment' => 'center']);
        }

        // Add space before signature
        $section->addText('');
        $section->addText('');
        $section->addText('');

        // Signature section using text runs (no table, no borders)
        $section->addText('', ['spaceAfter' => 0]);

        // Create two-column layout using text
        $tableSig = $section->addTable([
            'width' => 100 * 50,
            'unit' => 'pct',
            'borderSize' => 0,
            'borderColor' => 'FFFFFF',
        ]);
        
        $rowSig = $tableSig->addRow();
        
        // Left column - Mengetahui
        $leftCell = $rowSig->addCell(5000, ['borderSize' => 0, 'borderColor' => 'FFFFFF']);
        $leftCell->addText('Mengetahui,', ['size' => 12], ['alignment' => 'center']);
        $leftCell->addText($request->mengetahui_jabatan, ['size' => 12], ['alignment' => 'center']);
        $leftCell->addText('');
        $leftCell->addText('');
        $leftCell->addText('');
        $leftCell->addText('');
        $leftCell->addText($request->mengetahui_nama, ['bold' => true, 'size' => 12], ['alignment' => 'center']);
        $leftCell->addText('NIP. ' . $request->mengetahui_nip, ['size' => 12], ['alignment' => 'center']);

        // Right column - Dibuat oleh with Jabatan
        $rightCell = $rowSig->addCell(5000, ['borderSize' => 0, 'borderColor' => 'FFFFFF']);
        
        // Add empty line to align with "Mengetahui," on left column
        $rightCell->addText('', ['size' => 12], ['alignment' => 'center']);
        
        // Show jabatan if provided
        if ($request->penyusun_jabatan) {
            $rightCell->addText($request->penyusun_jabatan, ['size' => 12], ['alignment' => 'center']);
        } else {
            $rightCell->addText('');
        }
        $rightCell->addText('');
        $rightCell->addText('');
        $rightCell->addText('');
        $rightCell->addText('');
        
        if ($request->penyusun_nama) {
            $rightCell->addText($request->penyusun_nama, ['bold' => true, 'size' => 12], ['alignment' => 'center']);
            if ($request->penyusun_nip) {
                $rightCell->addText('NIP. ' . $request->penyusun_nip, ['size' => 12], ['alignment' => 'center']);
            }
        } else {
            $rightCell->addText(Auth::user()->name, ['bold' => true, 'size' => 12], ['alignment' => 'center']);
        }

        // Generate filename
        $filename = 'Laporan_Stok_Opname_' . str_replace([' ', '-'], '_', $periodLabel) . '.docx';
        
        // Save to temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'stok_opname');
        $phpWord->save($tempFile, 'Word2007');

        // Download file
        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Get period label for DOCX document
     */
    private function getPeriodLabelForDoc($actualStart, $actualEnd, $quarter, $tahun)
    {
        $bulanIndo = [
            1 => 'JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI',
            'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'
        ];

        if (!$actualStart || !$actualEnd) {
            return "TRIWULAN $quarter TAHUN $tahun";
        }

        $startMonth = date('n', strtotime($actualStart));
        $endMonth = date('n', strtotime($actualEnd));
        $startYear = date('Y', strtotime($actualStart));
        $endYear = date('Y', strtotime($actualEnd));

        if ($startMonth == $endMonth && $startYear == $endYear) {
            // Same month
            return "PER BULAN {$bulanIndo[$startMonth]} TAHUN $startYear";
        } elseif ($startYear == $endYear) {
            // Same year, different months
            return "PER BULAN {$bulanIndo[$startMonth]} - {$bulanIndo[$endMonth]} TAHUN $startYear";
        } else {
            // Different years
            return "PER BULAN {$bulanIndo[$startMonth]} $startYear - {$bulanIndo[$endMonth]} $endYear";
        }
    }

    /**
     * Get current quarter
     */
    private function getCurrentQuarter()
    {
        $month = date('n');
        if ($month <= 3) return 'Q1';
        if ($month <= 6) return 'Q2';
        if ($month <= 9) return 'Q3';
        return 'Q4';
    }
}
