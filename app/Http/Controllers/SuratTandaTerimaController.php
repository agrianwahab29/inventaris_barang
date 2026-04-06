<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;
use Carbon\Carbon;

class SuratTandaTerimaController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::with(['barang', 'ruangan', 'user'])
            ->where('jumlah_keluar', '>', 0);

        // Filter by date
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_keluar', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_keluar', '<=', $request->tanggal_sampai);
        }

        $transaksis = $query->orderBy('tanggal_keluar', 'desc')->get();

        // Group by nama_pengambil + tanggal_keluar
        $grouped = $transaksis->groupBy(function ($transaksi) {
            $pengambil = $transaksi->nama_pengambil ?: 'Tidak Diketahui';
            $tanggal = $transaksi->tanggal_keluar ? $transaksi->tanggal_keluar->format('Y-m-d') : 'Tidak Diketahui';
            return $pengambil . '|' . $tanggal;
        })->map(function ($group) {
            $first = $group->first();
            return [
                'nama_pengambil' => $first->nama_pengambil ?: 'Tidak Diketahui',
                'tanggal_keluar' => $first->tanggal_keluar,
                'ruangan' => $first->ruangan,
                'items' => $group,
                'total_items' => $group->count(),
                'total_qty' => $group->sum('jumlah_keluar'),
            ];
        })->values();

        return view('surat-tanda-terima.index', compact('grouped'));
    }

    public function generateDocx(Request $request)
    {
        $request->validate([
            'nama_pengambil' => 'required|string',
            'tanggal_keluar' => 'required|date',
        ]);

        $namaPengambil = $request->nama_pengambil;
        $tanggalKeluar = Carbon::parse($request->tanggal_keluar);

        $transaksis = Transaksi::with(['barang', 'ruangan', 'user'])
            ->where('jumlah_keluar', '>', 0)
            ->where('nama_pengambil', $namaPengambil)
            ->whereDate('tanggal_keluar', $tanggalKeluar->format('Y-m-d'))
            ->orderBy('created_at', 'asc')
            ->get();

        if ($transaksis->isEmpty()) {
            return back()->with('error', 'Tidak ada data transaksi untuk surat tanda terima ini.');
        }

        $ruangan = $transaksis->first()->ruangan;

        // Create PHPWord
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(11);

        $section = $phpWord->addSection([
            'marginTop' => Converter::cmToTwip(2),
            'marginBottom' => Converter::cmToTwip(2),
            'marginLeft' => Converter::cmToTwip(2.5),
            'marginRight' => Converter::cmToTwip(2.5),
        ]);

        // Title: TANDA TERIMA BARANG
        $section->addText('TANDA TERIMA BARANG', [
            'bold' => true,
            'size' => 16,
        ], [
            'alignment' => 'center',
            'spaceAfter' => 200,
        ]);

        // Info section
        $section->addText('Nama Penerima    : ' . $namaPengambil, ['size' => 11]);
        $ruanganName = $ruangan ? $ruangan->nama_ruangan : '-';
        $section->addText('Ruangan          : ' . $ruanganName, ['size' => 11]);

        $bulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $tanggalFormatted = $tanggalKeluar->format('d') . ' ' . ($bulanIndo[$tanggalKeluar->month] ?? '') . ' ' . $tanggalKeluar->format('Y');
        $section->addText('Tanggal          : ' . $tanggalFormatted, ['size' => 11]);
        $section->addText('');

        // Create table with all borders
        $table = $section->addTable([
            'width' => 100 * 50,
            'unit' => 'pct',
            'borderSize' => 8,
            'borderColor' => '000000',
            'borderInsideSize' => 8,
            'borderInsideColor' => '000000',
        ]);

        // Table header row
        $headerRow = $table->addRow();
        $cellStyle = ['vAlign' => 'center'];
        $textStyleBold = ['bold' => true, 'size' => 10];
        $centerAlign = ['alignment' => 'center'];
        $leftAlign = ['alignment' => 'left'];

        $headers = ['No', 'Nama Barang', 'Jumlah', 'Satuan', 'Paraf (Penerima)', 'Paraf (Penyerah)'];
        $widths = [600, 2800, 1000, 1000, 2200, 2200];

        foreach ($headers as $i => $header) {
            $cell = $headerRow->addCell($widths[$i], $cellStyle);
            $cell->addText($header, $textStyleBold, $centerAlign);
        }

        // Data rows
        $no = 1;
        foreach ($transaksis as $transaksi) {
            $dataRow = $table->addRow();
            
            $dataRow->addCell($widths[0], $cellStyle)->addText($no++, ['size' => 10], $centerAlign);
            $dataRow->addCell($widths[1], $cellStyle)->addText($transaksi->barang->nama_barang ?? '-', ['size' => 10], $leftAlign);
            $dataRow->addCell($widths[2], $cellStyle)->addText((string) $transaksi->jumlah_keluar, ['size' => 10], $centerAlign);
            $dataRow->addCell($widths[3], $cellStyle)->addText($transaksi->barang->satuan ?? '-', ['size' => 10], $centerAlign);
            $dataRow->addCell($widths[4], $cellStyle)->addText('', ['size' => 10]);
            $dataRow->addCell($widths[5], $cellStyle)->addText('', ['size' => 10]);
        }

        // Signature area
        $section->addText('');
        $section->addText('');
        $section->addText('');

        // Signature table (no borders)
        $sigTable = $section->addTable([
            'width' => 100 * 50,
            'unit' => 'pct',
            'borderSize' => 0,
            'borderColor' => 'FFFFFF',
        ]);

        $rowSig = $sigTable->addRow();

        // Left column - Mengetahui, Kasubag Umum
        $leftCell = $rowSig->addCell(5000, ['borderSize' => 0, 'borderColor' => 'FFFFFF']);
        $leftCell->addText('Mengetahui,', ['size' => 11], ['alignment' => 'center']);
        $leftCell->addText('Kasubag Umum', ['bold' => true, 'size' => 11], ['alignment' => 'center']);
        $leftCell->addText('');
        $leftCell->addText('');
        $leftCell->addText('');
        $leftCell->addText('');
        $leftCell->addText('________________________', ['size' => 11], ['alignment' => 'center']);
        $leftCell->addText('(Nama Kasubag Umum)', ['size' => 10], ['alignment' => 'center']);

        // Right column - Penerima
        $rightCell = $rowSig->addCell(5000, ['borderSize' => 0, 'borderColor' => 'FFFFFF']);
        $rightCell->addText('');
        $rightCell->addText('');
        $rightCell->addText('');
        $rightCell->addText('');
        $rightCell->addText('');
        $rightCell->addText('');
        $rightCell->addText('________________________', ['size' => 11], ['alignment' => 'center']);
        $rightCell->addText('( ' . $namaPengambil . ' )', ['size' => 10], ['alignment' => 'center']);

        // Download
        $filename = 'Surat_Tanda_Terima_' . str_replace(' ', '_', $namaPengambil) . '_' . $tanggalKeluar->format('Y-m-d') . '.docx';

        $tempFile = tempnam(sys_get_temp_dir(), 'stt_') . '.docx';
        $phpWord->save($tempFile, 'Word2007');

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
