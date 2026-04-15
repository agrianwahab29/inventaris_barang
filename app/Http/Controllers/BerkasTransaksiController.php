<?php

namespace App\Http\Controllers;

use App\Models\BerkasTransaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BerkasTransaksiController extends Controller
{
    /**
     * Check if current user is admin
     */
    private function isAdmin(): bool
    {
        return Auth::user() && Auth::user()->role === 'admin';
    }

    /**
     * Check if current user owns the berkas or is admin
     */
    private function canAccess(BerkasTransaksi $berkasTransaksi): bool
    {
        return $this->isAdmin() || $berkasTransaksi->user_id === Auth::id();
    }

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
        // Use strftime for SQLite compatibility (Laravel 8 uses SQLite by default in local)
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
            'nomor_surat' => 'required_without:perihal|string|max:100|nullable',
            'perihal' => 'required_without:nomor_surat|string|max:255|nullable',
            'tanggal_surat' => 'nullable|date',
            'pengirim' => 'nullable|string|max:100',
            'penerima' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string|max:1000',
            'file' => 'required|file|mimes:pdf|max:10240', // Max 10MB
        ], [
            'nomor_surat.required_without' => 'Nomor surat atau perihal harus diisi (minimal salah satu)',
            'perihal.required_without' => 'Nomor surat atau perihal harus diisi (minimal salah satu)',
            'file.required' => 'File PDF wajib diupload',
            'file.mimes' => 'File harus berformat PDF',
            'file.max' => 'Ukuran file maksimal 10MB',
        ]);

        // Store file to disk (outside transaction)
        $file = $request->file('file');
        $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9\-\.]/', '_', $file->getClientOriginalName());
        $filePath = $file->storeAs('berkas-transaksi', $fileName, 'public');

        try {
            // Use DB::transaction() to create database record
            $berkas = DB::transaction(function () use ($validated, $file, $filePath) {
                return BerkasTransaksi::create([
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
            });

            Log::info('Berkas uploaded', ['user_id' => Auth::id(), 'file' => $file->getClientOriginalName()]);

            return redirect()->route('berkas-transaksi.index')
                ->with('success', 'Berkas berhasil diupload dan diarsipkan.');

        } catch (\Exception $e) {
            // Delete orphaned file if it exists
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
                Log::warning('Deleted orphaned file after failed database insert', ['file_path' => $filePath]);
            }

            Log::error('Failed to upload berkas', ['error' => $e->getMessage(), 'file_path' => $filePath]);
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
        $fileExists = Storage::disk('public')->exists($berkasTransaksi->file_path);
        return view('berkas-transaksi.show', compact('berkasTransaksi', 'fileExists'));
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
            'nomor_surat' => 'required_without:perihal|string|max:100|nullable',
            'perihal' => 'required_without:nomor_surat|string|max:255|nullable',
            'tanggal_surat' => 'nullable|date',
            'pengirim' => 'nullable|string|max:100',
            'penerima' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string|max:1000',
            'file' => 'nullable|file|mimes:pdf|max:10240',
        ], [
            'nomor_surat.required_without' => 'Nomor surat atau perihal harus diisi (minimal salah satu)',
            'perihal.required_without' => 'Nomor surat atau perihal harus diisi (minimal salah satu)',
        ]);

        $newFilePath = null;
        $oldFilePath = $berkasTransaksi->file_path;

        // If new file uploaded: Store new file to disk (outside transaction)
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9\-\.]/', '_', $file->getClientOriginalName());
            $newFilePath = $file->storeAs('berkas-transaksi', $fileName, 'public');
        }

        try {
            // Use DB::transaction() to update database record
            DB::transaction(function () use ($berkasTransaksi, $validated, $request, $newFilePath) {
                $data = [
                    'nomor_surat' => $validated['nomor_surat'],
                    'tanggal_surat' => $validated['tanggal_surat'],
                    'perihal' => $validated['perihal'],
                    'pengirim' => $validated['pengirim'],
                    'penerima' => $validated['penerima'],
                    'keterangan' => $validated['keterangan'],
                ];

                // If new file was uploaded, include file data
                if ($newFilePath) {
                    $file = $request->file('file');
                    $data['file_path'] = $newFilePath;
                    $data['file_name'] = $file->getClientOriginalName();
                    $data['file_size'] = $file->getSize();
                    $data['file_mime'] = $file->getMimeType();
                }

                $berkasTransaksi->update($data);
            });

            // If DB update succeeds: Delete old file (only if new file was uploaded)
            if ($newFilePath && Storage::disk('public')->exists($oldFilePath)) {
                Storage::disk('public')->delete($oldFilePath);
                Log::info('Deleted old file after successful update', ['old_file_path' => $oldFilePath]);
            }

            return redirect()->route('berkas-transaksi.index')
                ->with('success', 'Data berkas berhasil diupdate.');

        } catch (\Exception $e) {
            // Delete new file if it exists (keep old file)
            if ($newFilePath && Storage::disk('public')->exists($newFilePath)) {
                Storage::disk('public')->delete($newFilePath);
                Log::warning('Deleted new file after failed database update', ['new_file_path' => $newFilePath]);
            }

            Log::error('Failed to update berkas', [
                'error' => $e->getMessage(),
                'berkas_id' => $berkasTransaksi->id,
                'new_file_path' => $newFilePath,
            ]);
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
     * Bug #7 Fix: Add authorization check
     * Bug #5 Fix: Add path traversal protection
     */
    public function download(BerkasTransaksi $berkasTransaksi)
    {
        try {
            // Bug #7: Authorization check - user must own the file or be admin
            if (!$this->canAccess($berkasTransaksi)) {
                Log::warning('Unauthorized download attempt', [
                    'user_id' => Auth::id(),
                    'berkas_id' => $berkasTransaksi->id,
                    'owner_id' => $berkasTransaksi->user_id
                ]);
                return back()->with('error', 'Anda tidak memiliki akses untuk mengunduh file ini.');
            }

            // Bug #5: Path traversal protection - validate file_path pattern
            $expectedPrefix = 'berkas-transaksi/';
            if (!str_starts_with($berkasTransaksi->file_path, $expectedPrefix)) {
                Log::error('Path traversal attempt detected', [
                    'user_id' => Auth::id(),
                    'file_path' => $berkasTransaksi->file_path
                ]);
                return back()->with('error', 'Path file tidak valid.');
            }

            // Additional path validation - prevent directory traversal
            if (str_contains($berkasTransaksi->file_path, '..') || str_contains($berkasTransaksi->file_path, './')) {
                Log::error('Directory traversal detected', [
                    'user_id' => Auth::id(),
                    'file_path' => $berkasTransaksi->file_path
                ]);
                return back()->with('error', 'Path file tidak valid.');
            }

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
     * Bug #1 Fix: Add authorization check - users can only delete their own files, unless admin
     * Bug #8 Fix: Use whereIn() with chunking to prevent N+1 queries
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:berkas_transaksis,id',
        ]);

        try {
            $count = 0;
            $skippedCount = 0;

            // Build query with authorization filter
            $query = BerkasTransaksi::whereIn('id', $validated['ids']);

            // Non-admin users can only delete their own files
            if (!$this->isAdmin()) {
                $query->where('user_id', Auth::id());
            }

            // Use chunkById to prevent memory issues and N+1 queries
            $query->chunkById(100, function ($berkasList) use (&$count, &$skippedCount, $validated) {
                foreach ($berkasList as $berkas) {
                    // Additional authorization check for admin users
                    if ($this->isAdmin() && !$this->canAccess($berkas)) {
                        $skippedCount++;
                        Log::warning('Unauthorized bulk delete attempt', [
                            'user_id' => Auth::id(),
                            'berkas_id' => $berkas->id,
                            'owner_id' => $berkas->user_id
                        ]);
                        continue;
                    }

                    // Delete file from storage
                    if (Storage::disk('public')->exists($berkas->file_path)) {
                        Storage::disk('public')->delete($berkas->file_path);
                    }

                    $berkas->delete();
                    $count++;
                }
            });

            // Calculate how many IDs were not found or unauthorized
            $processedIds = $validated['ids'];
            $message = "{$count} berkas berhasil dihapus.";

            if (!$this->isAdmin()) {
                $unauthorizedCount = count($validated['ids']) - $count;
                if ($unauthorizedCount > 0) {
                    $message .= " {$unauthorizedCount} berkas tidak dihapus karena bukan milik Anda.";
                }
            } elseif ($skippedCount > 0) {
                $message .= " {$skippedCount} berkas dilewati karena tidak memiliki akses.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
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
     * Bug #1 Fix: Add authorization check - only admin can delete all, others can only delete their own
     * Bug #8 Fix: Use chunkById() to prevent N+1 queries and memory issues
     */
    public function deleteAll()
    {
        try {
            $query = BerkasTransaksi::query();

            // Bug #1: Non-admin users can only delete their own files
            if (!$this->isAdmin()) {
                $query->where('user_id', Auth::id());
                Log::info('Non-admin user deleting their own berkas', ['user_id' => Auth::id()]);
            } else {
                Log::info('Admin deleting all berkas', ['user_id' => Auth::id()]);
            }

            $count = 0;
            $skippedCount = 0;

            // Bug #8 Fix: Use chunkById to prevent memory issues and N+1 queries
            $query->chunkById(100, function ($berkasList) use (&$count, &$skippedCount) {
                foreach ($berkasList as $item) {
                    // Additional safety check for non-admin
                    if (!$this->isAdmin() && $item->user_id !== Auth::id()) {
                        $skippedCount++;
                        Log::warning('Unauthorized deleteAll attempt blocked', [
                            'user_id' => Auth::id(),
                            'berkas_id' => $item->id,
                            'owner_id' => $item->user_id
                        ]);
                        continue;
                    }

                    if (Storage::disk('public')->exists($item->file_path)) {
                        Storage::disk('public')->delete($item->file_path);
                    }
                    $item->delete();
                    $count++;
                }
            });

            $message = $this->isAdmin()
                ? "Semua {$count} berkas berhasil dihapus."
                : "{$count} berkas milik Anda berhasil dihapus.";

            if ($skippedCount > 0) {
                $message .= " {$skippedCount} berkas dilewati karena tidak memiliki akses.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
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
     * Bug #1 Fix: Add authorization check - only admin can delete by month for all, others only their own
     * Bug #8 Fix: Use chunkById() to prevent N+1 queries and memory issues
     */
    public function deleteByMonth(Request $request)
    {
        $validated = $request->validate([
            'tahun' => 'required|integer',
            'bulan' => 'required|integer|min:1|max:12',
        ]);

        try {
            $query = BerkasTransaksi::whereYear('tanggal_surat', $validated['tahun'])
                ->whereMonth('tanggal_surat', $validated['bulan']);

            // Bug #1: Non-admin users can only delete their own files
            if (!$this->isAdmin()) {
                $query->where('user_id', Auth::id());
                Log::info('Non-admin user deleting berkas by month', [
                    'user_id' => Auth::id(),
                    'tahun' => $validated['tahun'],
                    'bulan' => $validated['bulan']
                ]);
            }

            $count = 0;
            $skippedCount = 0;

            // Bug #8 Fix: Use chunkById to prevent memory issues and N+1 queries
            $query->chunkById(100, function ($berkasList) use (&$count, &$skippedCount) {
                foreach ($berkasList as $item) {
                    // Additional authorization check for admin users
                    if ($this->isAdmin() && !$this->canAccess($item)) {
                        $skippedCount++;
                        Log::warning('Unauthorized deleteByMonth attempt blocked', [
                            'user_id' => Auth::id(),
                            'berkas_id' => $item->id,
                            'owner_id' => $item->user_id
                        ]);
                        continue;
                    }

                    if (Storage::disk('public')->exists($item->file_path)) {
                        Storage::disk('public')->delete($item->file_path);
                    }
                    $item->delete();
                    $count++;
                }
            });

            $message = "{$count} berkas untuk " . Carbon::create($validated['tahun'], $validated['bulan'])->format('F Y') . " berhasil dihapus.";
            if ($skippedCount > 0) {
                $message .= " {$skippedCount} berkas dilewati karena tidak memiliki akses.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
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
     * Bug #1 Fix: Add authorization check - only admin can delete by range for all, others only their own
     * Bug #8 Fix: Use chunkById() to prevent N+1 queries and memory issues
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

            $query = BerkasTransaksi::whereBetween('tanggal_surat', [$startDate, $endDate]);

            // Bug #1: Non-admin users can only delete their own files
            if (!$this->isAdmin()) {
                $query->where('user_id', Auth::id());
                Log::info('Non-admin user deleting berkas by range', [
                    'user_id' => Auth::id(),
                    'range' => $startDate->format('M Y') . ' - ' . $endDate->format('M Y')
                ]);
            }

            $count = 0;
            $skippedCount = 0;

            // Bug #8 Fix: Use chunkById to prevent memory issues and N+1 queries
            $query->chunkById(100, function ($berkasList) use (&$count, &$skippedCount) {
                foreach ($berkasList as $item) {
                    // Additional authorization check for admin users
                    if ($this->isAdmin() && !$this->canAccess($item)) {
                        $skippedCount++;
                        Log::warning('Unauthorized deleteByRange attempt blocked', [
                            'user_id' => Auth::id(),
                            'berkas_id' => $item->id,
                            'owner_id' => $item->user_id
                        ]);
                        continue;
                    }

                    if (Storage::disk('public')->exists($item->file_path)) {
                        Storage::disk('public')->delete($item->file_path);
                    }
                    $item->delete();
                    $count++;
                }
            });

            $rangeLabel = $startDate->format('M Y') . ' - ' . $endDate->format('M Y');

            $message = "{$count} berkas untuk periode {$rangeLabel} berhasil dihapus.";
            if ($skippedCount > 0) {
                $message .= " {$skippedCount} berkas dilewati karena tidak memiliki akses.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
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
