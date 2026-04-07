<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransaksiExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::with(['barang', 'ruangan', 'user']);

        // Filter berdasarkan user (untuk admin)
        if ($request->filled('user_id') && Auth::user()->isAdmin()) {
            $query->where('user_id', $request->user_id);
        }

        // Filter berdasarkan tipe transaksi
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        // Filter berdasarkan barang
        if ($request->filled('barang_id')) {
            $query->where('barang_id', $request->barang_id);
        }

        // Filter tanggal dari
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }

        // Filter tanggal sampai
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }

        // Filter tanggal keluar (khusus barang keluar)
        if ($request->filled('tanggal_keluar_dari')) {
            $query->whereDate('tanggal_keluar', '>=', $request->tanggal_keluar_dari);
        }
        if ($request->filled('tanggal_keluar_sampai')) {
            $query->whereDate('tanggal_keluar', '<=', $request->tanggal_keluar_sampai);
        }

        // Filter multiple tanggal (untuk export)
        if ($request->filled('tanggal_list')) {
            $tanggalList = explode(',', $request->tanggal_list);
            $query->whereIn(DB::raw('DATE(tanggal)'), $tanggalList);
        }

        // Filter tahun
        if ($request->filled('tahun')) {
            $query->whereRaw("strftime('%Y', tanggal) = ?", [$request->tahun]);
        }

        // Filter bulan (jika tahun juga dipilih, else gunakan tahun sekarang)
        if ($request->filled('bulan')) {
            $tahun = $request->filled('tahun') ? $request->tahun : date('Y');
            $query->whereRaw("strftime('%Y', tanggal) = ?", [$tahun])
                  ->whereRaw("strftime('%m', tanggal) = ?", [str_pad($request->bulan, 2, '0', STR_PAD_LEFT)]);
        }

        $transaksis = $query->orderBy('created_at', 'desc')->paginate(25);
        $barangs = Barang::orderBy('nama_barang')->get();
        // Only show active users in filters
        $users = User::aktif()->orderBy('name')->get();
        $availableDates = Transaksi::selectRaw('DATE(tanggal) as tgl')
            ->distinct()
            ->orderBy('tgl', 'desc')
            ->pluck('tgl');
        
        $availableYears = Transaksi::selectRaw("strftime('%Y', tanggal) as tahun")
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');
        
        $availableMonths = Transaksi::selectRaw("strftime('%m', tanggal) as bulan")
            ->distinct()
            ->orderBy('bulan', 'asc')
            ->pluck('bulan');
        
        // Group months by year for the export modal
        $monthsByYear = [];
        if ($availableYears->isNotEmpty()) {
            foreach ($availableYears as $year) {
                $months = Transaksi::selectRaw("strftime('%m', tanggal) as bulan")
                    ->whereRaw("strftime('%Y', tanggal) = ?", [$year])
                    ->distinct()
                    ->orderBy('bulan', 'asc')
                    ->pluck('bulan')
                    ->map(function($month) {
                        return (int) $month; // Convert to integer
                    })
                    ->toArray(); // Convert to plain array
                
                $monthsByYear[$year] = $months;
            }
        }

        // Get latest timestamp for polling
        $latestTimestamp = Transaksi::latest('created_at')->value('created_at');

        return view('transaksi.index', compact('transaksis', 'barangs', 'users', 'availableDates', 'availableYears', 'availableMonths', 'monthsByYear', 'latestTimestamp'));
    }

    // Form input barang (gabungan masuk & keluar dalam satu form)
    public function create()
    {
        $barangs = Barang::orderBy('nama_barang')->get();
        $ruangans = Ruangan::orderBy('nama_ruangan')->get();
        return view('transaksi.create', compact('barangs', 'ruangans'));
    }

    // Store transaksi (satu record untuk masuk dan keluar)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'jumlah_masuk' => 'nullable|integer|min:0',
            'jumlah_keluar' => 'nullable|integer|min:0',
            'tanggal_masuk' => 'nullable|date',
            'tanggal_keluar' => 'nullable|date',
            'ruangan_id' => 'nullable|exists:ruangans,id',
            'nama_pengambil' => 'nullable|string|max:255',
            'tipe_pengambil' => 'nullable|in:nama_ruangan,ruangan_saja',
            'keterangan' => 'nullable|string',
        ]);

        $jumlahMasukInput = (int)($validated['jumlah_masuk'] ?? 0);
        $jumlahKeluar = (int)($validated['jumlah_keluar'] ?? 0);

        if ($jumlahMasukInput == 0 && $jumlahKeluar == 0) {
            return back()->withErrors(['jumlah_masuk' => 'Jumlah masuk atau jumlah keluar harus diisi minimal 1'])
                ->withInput();
        }

        $barang = Barang::findOrFail($validated['barang_id']);
        $stokSebelum = $barang->stok;
        
        // FIX: Gunakan input user langsung tanpa menambahkan stok sebelum
        $jumlahMasuk = $jumlahMasukInput;
        
        $stokSetelahMasuk = $stokSebelum + $jumlahMasukInput;
        
        if ($stokSetelahMasuk < $jumlahKeluar) {
            return back()->withErrors(['jumlah_keluar' => 'Stok tidak mencukupi. Stok setelah masuk: ' . $stokSetelahMasuk . ', diminta keluar: ' . $jumlahKeluar])
                ->withInput();
        }

        $sisaStok = $stokSetelahMasuk - $jumlahKeluar;
        
        $tipeTransaksi = 'masuk';
        $jumlah = $jumlahMasuk;
        
        if ($jumlahKeluar > 0 && $jumlahMasukInput > 0) {
            $tipeTransaksi = 'masuk_keluar';
        } elseif ($jumlahKeluar > 0 && $jumlahMasukInput == 0) {
            $tipeTransaksi = 'keluar';
            $jumlah = $jumlahKeluar;
        } elseif ($jumlahMasukInput > 0 && $jumlahKeluar == 0) {
            $tipeTransaksi = 'masuk';
        }

        $namaPengambil = null;
        if ($jumlahKeluar > 0 && ($validated['tipe_pengambil'] ?? 'ruangan_saja') === 'nama_ruangan') {
            $namaPengambil = $validated['nama_pengambil'] ?? null;
        }

        DB::beginTransaction();
        try {
            $barang->update(['stok' => $sisaStok]);

            Transaksi::create([
                'barang_id' => $validated['barang_id'],
                'tipe' => $tipeTransaksi,
                'jumlah_masuk' => $jumlahMasuk,
                'jumlah_keluar' => $jumlahKeluar,
                'jumlah' => $jumlah,
                'stok_sebelum' => $stokSebelum,
                'stok_setelah_masuk' => $stokSetelahMasuk,
                'sisa_stok' => $sisaStok,
                'tanggal' => $validated['tanggal_masuk'] ?? now(),
                'tanggal_keluar' => $validated['tanggal_keluar'] ?? null,
                'ruangan_id' => $jumlahKeluar > 0 ? ($validated['ruangan_id'] ?? null) : null,
                'nama_pengambil' => $namaPengambil,
                'tipe_pengambil' => $jumlahKeluar > 0 ? ($validated['tipe_pengambil'] ?? null) : null,
                'user_id' => Auth::id(),
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            DB::commit();
            Cache::flush();

            $messages = [];
            if ($jumlahMasuk > 0) {
                $messages[] = 'Barang masuk ' . $jumlahMasuk . ' ' . $barang->satuan;
            }
            if ($jumlahKeluar > 0) {
                $messages[] = 'Barang keluar ' . $jumlahKeluar . ' ' . $barang->satuan;
            }
            
            // BUG FIX #7: Ensure proper integer comparison for stock minimum warning
            $sisaStokInt = (int) $sisaStok;
            $stokMinimumInt = (int) $barang->stok_minimum;
            
            if ($sisaStokInt === 0) {
                $messages[] = 'Stok habis!';
            } elseif ($sisaStokInt <= $stokMinimumInt && $sisaStokInt > 0) {
                $messages[] = 'Stok minimum!';
            }

            return redirect()->route('transaksi.index')->with('success', implode(' | ', $messages));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Transaksi $transaksi)
    {
        $transaksi->load(['barang', 'ruangan', 'user']);
        return view('transaksi.show', compact('transaksi'));
    }

    // Edit transaksi - Form edit
    public function edit(Transaksi $transaksi)
    {
        $transaksi->load(['barang', 'ruangan', 'user']);
        $barangs = Barang::orderBy('nama_barang')->get();
        $ruangans = Ruangan::orderBy('nama_ruangan')->get();
        return view('transaksi.edit', compact('transaksi', 'barangs', 'ruangans'));
    }

    // Update transaksi
    public function update(Request $request, Transaksi $transaksi)
    {
        // Validasi input
        $validated = $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'jumlah_masuk' => 'nullable|integer|min:0',
            'jumlah_keluar' => 'nullable|integer|min:0',
            'tanggal' => 'required|date',
            'tanggal_keluar' => 'nullable|date',
            'ruangan_id' => 'nullable|exists:ruangans,id',
            'nama_pengambil' => 'nullable|string|max:255',
            'tipe_pengambil' => 'nullable|in:nama_ruangan,ruangan_saja',
            'keterangan' => 'nullable|string',
        ]);

        $jumlahMasukInput = (int)($validated['jumlah_masuk'] ?? 0);
        $jumlahKeluar = (int)($validated['jumlah_keluar'] ?? 0);

        if ($jumlahMasukInput == 0 && $jumlahKeluar == 0) {
            return back()->withErrors(['jumlah_masuk' => 'Jumlah masuk atau jumlah keluar harus diisi minimal 1'])
                ->withInput();
        }

        $barang = Barang::findOrFail($validated['barang_id']);
        
        // Hitung selisih untuk update stok barang
        $oldJumlahMasuk = $transaksi->jumlah_masuk;
        $oldJumlahKeluar = $transaksi->jumlah_keluar;
        
        $stokSebelum = $barang->stok;
        
        // Stok sebelum transaksi ini (rollback stok lama)
        $stokTanpaTransaksiIni = $stokSebelum - ($oldJumlahMasuk - $oldJumlahKeluar);
        
        // Terapkan transaksi baru
        $stokSetelahMasuk = $stokTanpaTransaksiIni + $jumlahMasukInput;
        $sisaStok = $stokSetelahMasuk - $jumlahKeluar;
        
        if ($sisaStok < 0) {
            return back()->withErrors(['jumlah_keluar' => 'Stok tidak mencukupi. Stok tersedia: ' . $stokSetelahMasuk . ', diminta keluar: ' . $jumlahKeluar])
                ->withInput();
        }

        // Tentukan tipe transaksi
        $tipeTransaksi = 'masuk';
        $jumlahTotal = $jumlahMasukInput;
        
        if ($jumlahKeluar > 0 && $jumlahMasukInput > 0) {
            $tipeTransaksi = 'masuk_keluar';
        } elseif ($jumlahKeluar > 0 && $jumlahMasukInput == 0) {
            $tipeTransaksi = 'keluar';
            $jumlahTotal = $jumlahKeluar;
        }

        $namaPengambil = null;
        // Simpan nama_pengambil jika diisi, tidak tergantung jumlah_keluar
        if (!empty($validated['nama_pengambil'])) {
            $namaPengambil = $validated['nama_pengambil'];
        }

        DB::beginTransaction();
        try {
            // Update stok barang
            $barang->update(['stok' => $sisaStok]);

            // Update transaksi
            $transaksi->update([
                'barang_id' => $validated['barang_id'],
                'tipe' => $tipeTransaksi,
                'jumlah_masuk' => $jumlahMasukInput,
                'jumlah_keluar' => $jumlahKeluar,
                'jumlah' => $jumlahTotal,
                'stok_sebelum' => $stokTanpaTransaksiIni,
                'stok_setelah_masuk' => $stokSetelahMasuk,
                'sisa_stok' => $sisaStok,
                'tanggal' => $validated['tanggal'],
                'tanggal_keluar' => $validated['tanggal_keluar'] ?? null,
                'ruangan_id' => $validated['ruangan_id'] ?? null,
                'nama_pengambil' => $namaPengambil,
                'tipe_pengambil' => $validated['tipe_pengambil'] ?? null,
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            DB::commit();
            Cache::flush();

            $messages = ['Transaksi berhasil diupdate'];
            if ($jumlahMasukInput > 0) {
                $messages[] = 'Barang masuk ' . $jumlahMasukInput . ' ' . $barang->satuan;
            }
            if ($jumlahKeluar > 0) {
                $messages[] = 'Barang keluar ' . $jumlahKeluar . ' ' . $barang->satuan;
            }
            
            // BUG FIX #7: Ensure proper integer comparison for stock minimum warning
            $sisaStokInt = (int) $sisaStok;
            $stokMinimumInt = (int) $barang->stok_minimum;
            
            if ($sisaStokInt === 0) {
                $messages[] = 'Stok habis!';
            } elseif ($sisaStokInt <= $stokMinimumInt && $sisaStokInt > 0) {
                $messages[] = 'Stok minimum!';
            }

            return redirect()->route('transaksi.index')->with('success', implode(' | ', $messages));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Transaksi $transaksi)
    {
        // Cek apakah user adalah admin atau pemilik transaksi
        $user = Auth::user();
        
        if (!$user->isAdmin() && $transaksi->user_id !== $user->id) {
            return back()->with('error', 'Anda hanya dapat menghapus transaksi yang Anda buat sendiri');
        }

        // Kembalikan stok barang
        $barang = $transaksi->barang;
        
        // Hitung ulang stok: stok saat ini dikurangi dampak transaksi ini
        // Jika transaksi masuk: kurangi stok
        // Jika transaksi keluar: tambahi stok
        $stokSaatIni = $barang->stok;
        
        if ($transaksi->tipe === 'masuk') {
            $stokBaru = $stokSaatIni - $transaksi->jumlah_masuk;
        } elseif ($transaksi->tipe === 'keluar') {
            $stokBaru = $stokSaatIni + $transaksi->jumlah_keluar;
        } else { // masuk_keluar
            $stokBaru = $stokSaatIni - $transaksi->jumlah_masuk + $transaksi->jumlah_keluar;
        }
        
        $barang->update(['stok' => max(0, $stokBaru)]);

        $transaksi->delete();
        Cache::flush();
        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dihapus');
    }

    // Bulk delete transaksi
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', '');
        
        // Convert string to array (comma-separated from JavaScript)
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }
        
        // Filter empty values and ensure integers
        $ids = array_filter(array_map('intval', $ids));
        
        if (empty($ids)) {
            return back()->with('error', 'Pilih minimal satu transaksi untuk dihapus');
        }

        $user = Auth::user();
        
        DB::beginTransaction();
        try {
            $query = Transaksi::with('barang')->whereIn('id', $ids);
            
            // Jika bukan admin, hanya bisa hapus transaksi sendiri
            if (!$user->isAdmin()) {
                $query->where('user_id', $user->id);
            }
            
            $transaksis = $query->get();
            $deletedCount = 0;
            $skippedCount = 0;

            foreach ($transaksis as $transaksi) {
                // Cek lagi untuk memastikan (security)
                if (!$user->isAdmin() && $transaksi->user_id !== $user->id) {
                    $skippedCount++;
                    continue;
                }
                
                $barang = $transaksi->barang;
                $transaksi->delete();
                $deletedCount++;
                
                // Recalculate stock for the item after deletion to guarantee accuracy
                $totalMasuk = Transaksi::where('barang_id', $barang->id)->sum('jumlah_masuk');
                $totalKeluar = Transaksi::where('barang_id', $barang->id)->sum('jumlah_keluar');
                $barang->update(['stok' => $totalMasuk - $totalKeluar]);
            }

            DB::commit();
            Cache::flush();
            
            $message = $deletedCount . ' transaksi berhasil dihapus';
            if ($skippedCount > 0) {
                $message .= ' (' . $skippedCount . ' transaksi dilewati karena bukan milik Anda)';
            }
            
            return redirect()->route('transaksi.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        Log::debug('=== Export START ===', ['type' => $request->export_type, 'all_input' => $request->all()]);
        
        // Check if there are any transactions in the database first
        $transactionCount = Transaksi::count();
        if ($transactionCount === 0) {
            Log::warning('Export attempted with no transactions in database');
            return back()->with('error', 'Tidak ada data transaksi untuk diexport. Silakan tambahkan transaksi terlebih dahulu.')->withInput();
        }
        
        // Convert empty strings to null BEFORE validation
        $input = $request->all();
        $allFields = ['tahun', 'tahun_dari', 'tahun_sampai', 'bulan', 'bulan_dari', 'bulan_sampai', 'tahun_bulan', 'tanggal_dari', 'tanggal_sampai', 'tanggal_list', 'user_id'];
        foreach ($allFields as $field) {
            if (!isset($input[$field]) || $input[$field] === '' || $input[$field] === '""' || $input[$field] === null) {
                $input[$field] = null;
            }
        }
        $request->merge($input);

        Log::debug('After merge', ['merged' => $request->all()]);
        
        // Convert tahun_bulan to int
        if ($request->tahun_bulan) $request->request->set('tahun_bulan', (int)$request->tahun_bulan);
        
        // Convert bulan values to int (remove leading zeros like "01" -> 1)
        if ($request->bulan) $request->request->set('bulan', (int)$request->bulan);
        if ($request->bulan_dari) $request->request->set('bulan_dari', (int)$request->bulan_dari);
        if ($request->bulan_sampai) $request->request->set('bulan_sampai', (int)$request->bulan_sampai);
        
        // Convert tahun values to int
        if ($request->tahun) $request->request->set('tahun', (int)$request->tahun);
        if ($request->tahun_dari) $request->request->set('tahun_dari', (int)$request->tahun_dari);
        if ($request->tahun_sampai) $request->request->set('tahun_sampai', (int)$request->tahun_sampai);

        Log::debug('After integer conversion', ['bulan' => $request->bulan, 'bulan_dari' => $request->bulan_dari, 'bulan_sampai' => $request->bulan_sampai]);

        try {
            $request->validate([
                'export_type' => 'required|in:all,range,dates,year,year_range,month,month_range',
                'tanggal_dari' => 'nullable|date',
                'tanggal_sampai' => 'nullable|date',
                'tanggal_list' => 'nullable|string',
                'tahun' => 'nullable|integer|min:2000|max:2100',
                'tahun_dari' => 'nullable|integer|min:2000|max:2100',
                'tahun_sampai' => 'nullable|integer|min:2000|max:2100',
                'bulan' => 'nullable|integer|min:1|max:12',
                'bulan_dari' => 'nullable|integer|min:1|max:12',
                'bulan_sampai' => 'nullable|integer|min:1|max:12',
                'tahun_bulan' => 'nullable|integer|min:2000|max:2100',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Export validation failed', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        }
        
        Log::debug('Validation passed');
        
        // Validate date range
        if ($request->export_type === 'range') {
            if ($request->tanggal_dari && $request->tanggal_sampai) {
                if ($request->tanggal_dari > $request->tanggal_sampai) {
                    Log::error('Date range invalid');
                    return back()->with('error', 'Tanggal dari harus lebih kecil atau sama dengan tanggal sampai')->withInput();
                }
            }
        }
        
        // Validate month export has required fields
        if ($request->export_type === 'month') {
            Log::debug('Month validation', ['tahun_bulan' => $request->tahun_bulan, 'bulan' => $request->bulan]);
            if (!$request->tahun_bulan || !$request->bulan) {
                Log::error('Month validation failed');
                return back()->with('error', 'Pilih tahun dan bulan untuk export')->withInput();
            }
        }
        
        // Validate month_range has all required fields
        if ($request->export_type === 'month_range') {
            if (!$request->tahun_dari || !$request->bulan_dari || !$request->tahun_sampai || !$request->bulan_sampai) {
                Log::error('Month range validation failed');
                return back()->with('error', 'Pilih semua field untuk rentang bulan')->withInput();
            }
        }

        $filename = 'Data_Transaksi_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        Log::info('Export request', ['type' => $request->export_type, 'params' => $request->only(['tahun', 'tahun_dari', 'tahun_sampai', 'bulan', 'bulan_dari', 'bulan_sampai', 'tahun_bulan', 'tanggal_dari', 'tanggal_sampai'])]);
        
        // Create export instance and pre-check if there's data
        $export = new TransaksiExport(
            $request->export_type,
            $request->tanggal_dari,
            $request->tanggal_sampai,
            $request->tanggal_list,
            $request->user_id,
            $request->tahun,
            $request->tahun_dari,
            $request->tahun_sampai,
            $request->bulan,
            $request->bulan_dari,
            $request->bulan_sampai,
            $request->tahun_bulan
        );
        
        // Check if the collection has data before proceeding
        try {
            $collection = $export->collection();
            if ($collection->isEmpty()) {
                Log::warning('Export has no data for selected criteria', ['type' => $request->export_type]);
                return back()->with('error', 'Tidak ada data transaksi untuk kriteria yang dipilih. Silakan pilih kriteria lain.')->withInput();
            }
        } catch (\Exception $e) {
            Log::error('Export collection check failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal mengambil data transaksi: ' . $e->getMessage())->withInput();
        }
        
        // Use Excel::store and then download from storage
        $tempFile = 'exports/' . $filename;
        Log::debug('Storing file', ['path' => $tempFile]);
        
        // Ensure exports directory exists
        $exportsDir = storage_path('app/exports');
        if (!is_dir($exportsDir)) {
            if (!mkdir($exportsDir, 0755, true)) {
                Log::error('Failed to create exports directory', ['path' => $exportsDir]);
                return back()->with('error', 'Gagal membuat direktori export. Hubungi administrator.')->withInput();
            }
        }
        
        // Check if directory is writable
        if (!is_writable($exportsDir)) {
            Log::error('Exports directory not writable', ['path' => $exportsDir]);
            return back()->with('error', 'Direktori export tidak dapat ditulis. Hubungi administrator.')->withInput();
        }
        
        try {
            Excel::store($export, $tempFile, 'local');
            Log::info('Export file created SUCCESS', ['file' => $tempFile, 'full_path' => storage_path('app/' . $tempFile)]);
            
            // Check if file exists
            $fullPath = storage_path('app/' . $tempFile);
            if (!file_exists($fullPath)) {
                Log::error('File NOT created', ['path' => $fullPath]);
                return back()->with('error', 'File export tidak dapat dibuat. Silakan coba lagi.')->withInput();
            }
            
            // Check file size
            $fileSize = filesize($fullPath);
            if ($fileSize === 0) {
                Log::error('File created but is empty', ['path' => $fullPath]);
                unlink($fullPath); // Clean up empty file
                return back()->with('error', 'File export kosong. Silakan coba lagi.')->withInput();
            }
            
            Log::debug('File exists and has content, preparing download', ['size' => $fileSize]);
            
            return response()->download($fullPath, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);
            
        } catch (\Maatwebsite\Excel\Exceptions\NoTypeDetectedException $e) {
            Log::error('Export Excel type detection failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Format file tidak dikenali. Pastikan PHP extension untuk Excel terinstal.')->withInput();
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            Log::error('Export PhpSpreadsheet error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Gagal membuat file Excel: ' . $e->getMessage())->withInput();
        } catch (\Exception $e) {
            Log::error('Export FAILED', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Export gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function getBarangInfo($id)
    {
        $barang = Barang::findOrFail($id);
        return response()->json([
            'stok' => $barang->stok,
            'satuan' => $barang->satuan,
            'stok_minimum' => $barang->stok_minimum,
        ]);
    }

    /**
     * Check for new transactions since given timestamp (API endpoint for polling)
     */
    public function checkUpdates(Request $request)
    {
        try {
            $since = $request->query('since');
            
            if (!$since) {
                return response()->json([
                    'has_new' => false,
                    'timestamp' => now()->toIso8601String()
                ]);
            }
            
            $sinceDate = \Carbon\Carbon::parse($since);
            
            // Check for new transactions since timestamp
            $newTransactions = Transaksi::where('created_at', '>', $sinceDate)
                ->orWhere('updated_at', '>', $sinceDate)
                ->count();
            
            $latestTransaction = Transaksi::latest('created_at')->first();
            
            return response()->json([
                'has_new' => $newTransactions > 0,
                'count' => $newTransactions,
                'timestamp' => $latestTransaction ? $latestTransaction->created_at->toIso8601String() : now()->toIso8601String()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Check updates error: ' . $e->getMessage());
            return response()->json([
                'has_new' => false,
                'error' => 'Invalid timestamp format',
                'timestamp' => now()->toIso8601String()
            ], 400);
        }
    }
}
