<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BarangExport;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $query = Barang::query();

        if ($request->filled('search')) {
            $query->where('nama_barang', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status')) {
            if ($request->status == 'habis') {
                $query->where('stok', '<=', 0);
            } elseif ($request->status == 'rendah') {
                $query->whereColumn('stok', '<=', 'stok_minimum')
                    ->where('stok', '>', 0);
            } elseif ($request->status == 'tersedia') {
                $query->whereColumn('stok', '>', 'stok_minimum');
            }
        }

        $barangs = $query->orderBy('nama_barang')->paginate(25);
        $kategoris = ['ATK', 'Kebersihan', 'Konsumsi', 'Perlengkapan', 'Lainnya'];

        return view('barang.index', compact('barangs', 'kategoris'));
    }

    public function create()
    {
        $kategoris = ['ATK', 'Kebersihan', 'Konsumsi', 'Perlengkapan', 'Lainnya'];
        $satuans = ['Buah', 'Rim', 'Dos', 'Lusin', 'Pak', 'Box', 'Galon', 'Botol', 'Bungkus', 'Kilo', 'Pasang', 'Warna', 'Jenis', 'Kotak', 'Gantung', 'Lembar'];
        return view('barang.create', compact('kategoris', 'satuans'));
    }

    public function store(Request $request)
    {
        $satuans = ['Buah', 'Rim', 'Dos', 'Lusin', 'Pak', 'Box', 'Galon', 'Botol', 'Bungkus', 'Kilo', 'Pasang', 'Warna', 'Jenis', 'Kotak', 'Gantung', 'Lembar'];
        
        $rules = [
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'required|in:ATK,Kebersihan,Konsumsi,Perlengkapan,Lainnya',
            'stok' => 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:1',
            'catatan' => 'nullable|string',
        ];
        
        // Allow both predefined and custom units
        $satuanRule = 'required|string|max:50';
        if (!in_array($request->satuan, $satuans)) {
            // It's a custom unit, validate as string
            $rules['satuan'] = $satuanRule;
        } else {
            $rules['satuan'] = 'required|in:' . implode(',', $satuans);
        }
        
        $validated = $request->validate($rules);

        $existingBarang = Barang::where('nama_barang', $validated['nama_barang'])->first();
        
        if ($existingBarang) {
            $stokSebelum = $existingBarang->stok;
            $stokSetelahMasuk = $stokSebelum + $validated['stok'];
            
            $existingBarang->update([
                'stok' => $stokSetelahMasuk,
                'kategori' => $validated['kategori'],
                'satuan' => $validated['satuan'],
                'stok_minimum' => $validated['stok_minimum'],
                'catatan' => $validated['catatan'],
            ]);

            Transaksi::create([
                'barang_id' => $existingBarang->id,
                'tipe' => 'masuk',
                'jumlah_masuk' => $validated['stok'],
                'jumlah_keluar' => 0,
                'jumlah' => $validated['stok'],
                'stok_sebelum' => $stokSebelum,
                'stok_setelah_masuk' => $stokSetelahMasuk,
                'sisa_stok' => $stokSetelahMasuk,
                'tanggal' => now(),
                'ruangan_id' => null,
                'user_id' => Auth::id(),
                'keterangan' => 'Penambahan stok barang existing',
            ]);

            $message = 'Stok barang berhasil ditambahkan';
        } else {
            $barang = Barang::create($validated);

            Transaksi::create([
                'barang_id' => $barang->id,
                'tipe' => 'masuk',
                'jumlah_masuk' => $validated['stok'],
                'jumlah_keluar' => 0,
                'jumlah' => $validated['stok'],
                'stok_sebelum' => 0,
                'stok_setelah_masuk' => $validated['stok'],
                'sisa_stok' => $validated['stok'],
                'tanggal' => now(),
                'ruangan_id' => null,
                'user_id' => Auth::id(),
                'keterangan' => 'Barang masuk pertama kali',
            ]);

            $message = 'Barang berhasil ditambahkan';
        }

        Cache::flush();
        return redirect()->route('barang.index')->with('success', $message);
    }

    public function show(Barang $barang)
    {
        $transaksis = Transaksi::where('barang_id', $barang->id)
            ->with(['ruangan', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('barang.show', compact('barang', 'transaksis'));
    }

    public function edit(Barang $barang)
    {
        $kategoris = ['ATK', 'Kebersihan', 'Konsumsi', 'Perlengkapan', 'Lainnya'];
        $satuans = ['Buah', 'Rim', 'Dos', 'Lusin', 'Pak', 'Box', 'Galon', 'Botol', 'Bungkus', 'Kilo', 'Pasang', 'Warna', 'Jenis', 'Kotak', 'Gantung', 'Lembar'];
        return view('barang.edit', compact('barang', 'kategoris', 'satuans'));
    }

    public function update(Request $request, Barang $barang)
    {
        $satuans = ['Buah', 'Rim', 'Dos', 'Lusin', 'Pak', 'Box', 'Galon', 'Botol', 'Bungkus', 'Kilo', 'Pasang', 'Warna', 'Jenis', 'Kotak', 'Gantung', 'Lembar'];
        
        $rules = [
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'required|in:ATK,Kebersihan,Konsumsi,Perlengkapan,Lainnya',
            'stok_minimum' => 'required|integer|min:1',
            'catatan' => 'nullable|string',
        ];
        
        // Allow both predefined and custom units
        $satuanRule = 'required|string|max:50';
        if (!in_array($request->satuan, $satuans)) {
            $rules['satuan'] = $satuanRule;
        } else {
            $rules['satuan'] = 'required|in:' . implode(',', $satuans);
        }
        
        $validated = $request->validate($rules);

        $barang->update($validated);
        Cache::flush();
        return redirect()->route('barang.index')->with('success', 'Barang berhasil diupdate');
    }

    public function destroy(Barang $barang)
    {
        // Cek apakah ada transaksi terkait
        $transaksiCount = Transaksi::where('barang_id', $barang->id)->count();
        
        if ($transaksiCount > 0) {
            return back()->with('error', 'Barang tidak dapat dihapus karena memiliki riwayat transaksi');
        }

        $barang->delete();
        Cache::flush();
        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus');
    }

    public function export(Request $request)
    {
        $request->validate([
            'kategori' => 'nullable|in:ATK,Kebersihan,Konsumsi,Perlengkapan,Lainnya',
            'status' => 'nullable|in:habis,rendah,tersedia',
        ]);
        
        $filename = 'Data_Barang_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new BarangExport($request->kategori, $request->status), $filename);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        
        // Handle case where ids is a comma-separated string (from AJAX/form data)
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }
        
        // Ensure all IDs are integers and filter out empty values
        $ids = array_filter(array_map('intval', $ids));
        
        if (empty($ids)) {
            return back()->with('error', 'Pilih minimal satu barang untuk dihapus');
        }

        DB::beginTransaction();
        try {
            $barangs = Barang::whereIn('id', $ids)->get();
            $deletedCount = 0;
            $skippedCount = 0;

            foreach ($barangs as $barang) {
                if (Transaksi::where('barang_id', $barang->id)->count() > 0) {
                    $skippedCount++;
                    continue;
                }
                
                $barang->delete();
                $deletedCount++;
            }

            DB::commit();
            Cache::flush();
            
            $message = $deletedCount . ' barang berhasil dihapus.';
            if ($skippedCount > 0) {
                $message .= ' (' . $skippedCount . ' barang dilewati karena memiliki riwayat transaksi).';
            }
            
            return redirect()->route('barang.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update stok langsung dari daftar barang dengan auto-sinkronisasi transaksi
     */
    public function updateStok(Request $request, Barang $barang)
    {
        $validated = $request->validate([
            'stok_baru' => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $stokBaru = (int) $validated['stok_baru'];
        $stokLama = $barang->stok;
        $selisih = $stokBaru - $stokLama;

        // Jika tidak ada perubahan
        if ($selisih === 0) {
            return response()->json([
                'success' => true,
                'message' => 'Tidak ada perubahan stok',
                'stok' => $stokBaru,
            ]);
        }

        DB::beginTransaction();
        try {
            // Update stok barang
            $barang->update(['stok' => $stokBaru]);

            // Buat transaksi otomatis untuk tracking
            if ($selisih > 0) {
                // Stok bertambah - buat transaksi masuk
                Transaksi::create([
                    'barang_id' => $barang->id,
                    'tipe' => 'masuk',
                    'jumlah_masuk' => $selisih,
                    'jumlah_keluar' => 0,
                    'jumlah' => $selisih,
                    'stok_sebelum' => $stokLama,
                    'stok_setelah_masuk' => $stokBaru,
                    'sisa_stok' => $stokBaru,
                    'tanggal' => now(),
                    'ruangan_id' => null,
                    'user_id' => Auth::id(),
                    'keterangan' => $validated['keterangan'] ?? 'Penyesuaian stok via edit cepat (+)',
                ]);
            } else {
                // Stok berkurang - buat transaksi keluar
                Transaksi::create([
                    'barang_id' => $barang->id,
                    'tipe' => 'keluar',
                    'jumlah_masuk' => 0,
                    'jumlah_keluar' => abs($selisih),
                    'jumlah' => abs($selisih),
                    'stok_sebelum' => $stokLama,
                    'stok_setelah_masuk' => 0,
                    'sisa_stok' => $stokBaru,
                    'tanggal' => now(),
                    'ruangan_id' => null,
                    'user_id' => Auth::id(),
                    'keterangan' => $validated['keterangan'] ?? 'Penyesuaian stok via edit cepat (-)',
                ]);
            }

            DB::commit();
            Cache::flush();

            // Tentukan status untuk response
            $status = 'tersedia';
            if ($stokBaru <= 0) {
                $status = 'habis';
            } elseif ($stokBaru <= $barang->stok_minimum) {
                $status = 'rendah';
            }

            return response()->json([
                'success' => true,
                'message' => 'Stok berhasil diupdate dari ' . $stokLama . ' menjadi ' . $stokBaru,
                'stok' => $stokBaru,
                'selisih' => $selisih,
                'status' => $status,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal update stok: ' . $e->getMessage(),
            ], 500);
        }
    }
}
