<?php

namespace App\Http\Controllers;

use App\Models\BerkasTransaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BerkasTransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = BerkasTransaksi::with('user');

        // Filter by search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_surat', 'like', '%' . $search . '%')
                  ->orWhere('perihal', 'like', '%' . $search . '%')
                  ->orWhere('pengirim', 'like', '%' . $search . '%')
                  ->orWhere('penerima', 'like', '%' . $search . '%');
            });
        }

        // Filter by date range
        if ($request->has('dari') && $request->dari) {
            $query->whereDate('tanggal_surat', '>=', $request->dari);
        }
        if ($request->has('sampai') && $request->sampai) {
            $query->whereDate('tanggal_surat', '<=', $request->sampai);
        }

        // Filter by month/year
        if ($request->has('bulan') && $request->bulan && $request->has('tahun') && $request->tahun) {
            $query->whereYear('tanggal_surat', $request->tahun)
                  ->whereMonth('tanggal_surat', $request->bulan);
        }

        // Filter by year only
        if ($request->has('tahun_filter') && $request->tahun_filter) {
            $query->whereYear('tanggal_surat', $request->tahun_filter);
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $berkas = $query->orderBy('created_at', 'desc')
                       ->paginate(15)
                       ->withQueryString();

        // Get filter data
        $users = User::select('id', 'name')->orderBy('name')->get();
        $availableYears = BerkasTransaksi::selectRaw("strftime('%Y', tanggal_surat) as tahun")
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        // Stats
        $totalBerkas = BerkasTransaksi::count();
        $totalBerkasBulanIni = BerkasTransaksi::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $totalSize = BerkasTransaksi::sum('file_size');

        return view('berkas-transaksi.index', compact(
            'berkas',
            'users',
            'availableYears',
            'totalBerkas',
            'totalBerkasBulanIni',
            'totalSize'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('berkas-transaksi.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_surat' => 'nullable|string|max:100',
            'tanggal_surat' => 'nullable|date',
            'perihal' => 'nullable|string|max:255',
            'pengirim' => 'nullable|string|max:100',
            'penerima' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string|max:1000',
            'file' => 'required|file|mimes:pdf|max:10240', // Max 10MB
        ], [
            'file.required' => 'File PDF wajib diupload',
            'file.mimes' => 'File harus berformat PDF',
            'file.max' => 'Ukuran file maksimal 10MB',
        ]);

        try {
            $file = $request->file('file');
            $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9\-\.]/', '_', $file->getClientOriginalName());
            $filePath = $file->storeAs('berkas-transaksi', $fileName, 'public');

            BerkasTransaksi::create([
                'nomor_surat' => $validated['nomor_surat'],
                'tanggal_surat' => $validated['tanggal_surat'],
                'perihal' => $validated['perihal'],
                'pengirim' => $validated['pengirim'],
                'penerima' => $validated['penerima'],
                'keterangan' => $validated['keterangan'],
                'user_id' => Auth::id(),
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'file_mime' => $file->getMimeType(),
            ]);

            Log::info('Berkas uploaded', ['user_id' => Auth::id(), 'file' => $file->getClientOriginalName()]);

            return redirect()->route('berkas-transaksi.index')
                ->with('success', 'Berkas berhasil diupload dan diarsipkan.');

        } catch (\Exception $e) {
            Log::error('Failed to upload berkas', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal mengupload berkas: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BerkasTransaksi  $berkasTransaksi
     * @return \Illuminate\Http\Response
     */
    public function show(BerkasTransaksi $berkasTransaksi)
    {
        return view('berkas-transaksi.show', compact('berkasTransaksi'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BerkasTransaksi  $berkasTransaksi
     * @return \Illuminate\Http\Response
     */
    public function edit(BerkasTransaksi $berkasTransaksi)
    {
        return view('berkas-transaksi.edit', compact('berkasTransaksi'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BerkasTransaksi  $berkasTransaksi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BerkasTransaksi $berkasTransaksi)
    {
        $validated = $request->validate([
            'nomor_surat' => 'nullable|string|max:100',
            'tanggal_surat' => 'nullable|date',
            'perihal' => 'nullable|string|max:255',
            'pengirim' => 'nullable|string|max:100',
            'penerima' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string|max:1000',
            'file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        try {
            $data = [
                'nomor_surat' => $validated['nomor_surat'],
                'tanggal_surat' => $validated['tanggal_surat'],
                'perihal' => $validated['perihal'],
                'pengirim' => $validated['pengirim'],
                'penerima' => $validated['penerima'],
                'keterangan' => $validated['keterangan'],
            ];

            // If new file uploaded
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                
                // Delete old file
                if (Storage::disk('public')->exists($berkasTransaksi->file_path)) {
                    Storage::disk('public')->delete($berkasTransaksi->file_path);
                }
                
                // Store new file
                $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9\-\.]/', '_', $file->getClientOriginalName());
                $filePath = $file->storeAs('berkas-transaksi', $fileName, 'public');
                
                $data['file_path'] = $filePath;
                $data['file_name'] = $file->getClientOriginalName();
                $data['file_size'] = $file->getSize();
                $data['file_mime'] = $file->getMimeType();
            }

            $berkasTransaksi->update($data);

            return redirect()->route('berkas-transaksi.index')
                ->with('success', 'Data berkas berhasil diupdate.');

        } catch (\Exception $e) {
            Log::error('Failed to update berkas', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal mengupdate berkas: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BerkasTransaksi  $berkasTransaksi
     * @return \Illuminate\Http\Response
     */
    public function destroy(BerkasTransaksi $berkasTransaksi)
    {
        try {
            // Delete file from storage
            if (Storage::disk('public')->exists($berkasTransaksi->file_path)) {
                Storage::disk('public')->delete($berkasTransaksi->file_path);
            }

            $berkasTransaksi->delete();

            return redirect()->route('berkas-transaksi.index')
                ->with('success', 'Berkas berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Failed to delete berkas', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal menghapus berkas: ' . $e->getMessage());
        }
    }

    /**
     * Download file
     */
    public function download(BerkasTransaksi $berkasTransaksi)
    {
        try {
            if (!Storage::disk('public')->exists($berkasTransaksi->file_path)) {
                return back()->with('error', 'File tidak ditemukan di server.');
            }

            Log::info('Berkas downloaded', [
                'user_id' => Auth::id(),
                'berkas_id' => $berkasTransaksi->id,
                'file' => $berkasTransaksi->file_name
            ]);

            return Storage::disk('public')->download(
                $berkasTransaksi->file_path,
                $berkasTransaksi->file_name
            );

        } catch (\Exception $e) {
            Log::error('Failed to download berkas', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal mengunduh file: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete - delete multiple selected items
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:berkas_transaksis,id',
        ]);

        try {
            $count = 0;
            foreach ($validated['ids'] as $id) {
                $berkas = BerkasTransaksi::find($id);
                if ($berkas) {
                    // Delete file
                    if (Storage::disk('public')->exists($berkas->file_path)) {
                        Storage::disk('public')->delete($berkas->file_path);
                    }
                    $berkas->delete();
                    $count++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "{$count} berkas berhasil dihapus.",
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk delete failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus berkas: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete all berkas
     */
    public function deleteAll()
    {
        try {
            $berkas = BerkasTransaksi::all();
            $count = 0;

            foreach ($berkas as $item) {
                if (Storage::disk('public')->exists($item->file_path)) {
                    Storage::disk('public')->delete($item->file_path);
                }
                $item->delete();
                $count++;
            }

            return response()->json([
                'success' => true,
                'message' => "Semua {$count} berkas berhasil dihapus.",
            ]);

        } catch (\Exception $e) {
            Log::error('Delete all failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus semua berkas: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete berkas by month
     */
    public function deleteByMonth(Request $request)
    {
        $validated = $request->validate([
            'tahun' => 'required|integer',
            'bulan' => 'required|integer|min:1|max:12',
        ]);

        try {
            $berkas = BerkasTransaksi::whereYear('tanggal_surat', $validated['tahun'])
                ->whereMonth('tanggal_surat', $validated['bulan'])
                ->get();
            
            $count = 0;
            foreach ($berkas as $item) {
                if (Storage::disk('public')->exists($item->file_path)) {
                    Storage::disk('public')->delete($item->file_path);
                }
                $item->delete();
                $count++;
            }

            return response()->json([
                'success' => true,
                'message' => "{$count} berkas untuk " . Carbon::create($validated['tahun'], $validated['bulan'])->format('F Y') . " berhasil dihapus.",
            ]);

        } catch (\Exception $e) {
            Log::error('Delete by month failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus berkas: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete berkas by month range
     */
    public function deleteByRange(Request $request)
    {
        $validated = $request->validate([
            'tahun_dari' => 'required|integer',
            'bulan_dari' => 'required|integer|min:1|max:12',
            'tahun_sampai' => 'required|integer',
            'bulan_sampai' => 'required|integer|min:1|max:12',
        ]);

        try {
            // Build date range
            $startDate = Carbon::create($validated['tahun_dari'], $validated['bulan_dari'], 1);
            $endDate = Carbon::create($validated['tahun_sampai'], $validated['bulan_sampai'], 1)->endOfMonth();

            $berkas = BerkasTransaksi::whereBetween('tanggal_surat', [$startDate, $endDate])->get();
            
            $count = 0;
            foreach ($berkas as $item) {
                if (Storage::disk('public')->exists($item->file_path)) {
                    Storage::disk('public')->delete($item->file_path);
                }
                $item->delete();
                $count++;
            }

            $rangeLabel = $startDate->format('M Y') . ' - ' . $endDate->format('M Y');

            return response()->json([
                'success' => true,
                'message' => "{$count} berkas untuk periode {$rangeLabel} berhasil dihapus.",
            ]);

        } catch (\Exception $e) {
            Log::error('Delete by range failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus berkas: ' . $e->getMessage(),
            ], 500);
        }
    }
}
