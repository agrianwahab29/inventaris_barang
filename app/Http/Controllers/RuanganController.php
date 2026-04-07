<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ruangan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RuanganController extends Controller
{
    public function index()
    {
        $ruangans = Ruangan::orderBy('nama_ruangan')->paginate(20);
        return view('ruangan.index', compact('ruangans'));
    }

    public function create()
    {
        return view('ruangan.create');
    }

    public function store(Request $request)
    {
        // Hanya admin yang bisa menambah ruangan
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('ruangan.index')
                ->with('error', 'Hanya admin yang dapat menambah data ruangan');
        }

        $validated = $request->validate([
            'nama_ruangan' => 'required|string|max:255|unique:ruangans,nama_ruangan',
            'keterangan' => 'nullable|string',
        ]);

        Ruangan::create($validated);

        return redirect()->route('ruangan.index')
            ->with('success', 'Data ruangan berhasil ditambahkan');
    }

    public function show(Ruangan $ruangan)
    {
        $ruangan->load('transaksis.barang');
        return view('ruangan.show', compact('ruangan'));
    }

    public function edit(Ruangan $ruangan)
    {
        return view('ruangan.edit', compact('ruangan'));
    }

    public function update(Request $request, Ruangan $ruangan)
    {
        // Hanya admin yang bisa mengupdate ruangan
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('ruangan.index')
                ->with('error', 'Hanya admin yang dapat mengubah data ruangan');
        }

        $validated = $request->validate([
            'nama_ruangan' => 'required|string|max:255|unique:ruangans,nama_ruangan,' . $ruangan->id,
            'keterangan' => 'nullable|string',
        ]);

        $ruangan->update($validated);

        return redirect()->route('ruangan.index')
            ->with('success', 'Data ruangan berhasil diperbarui');
    }

    public function destroy(Ruangan $ruangan)
    {
        // Hanya admin yang bisa menghapus ruangan
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('ruangan.index')
                ->with('error', 'Hanya admin yang dapat menghapus data ruangan');
        }

        // Cek apakah ruangan masih digunakan di transaksi
        if ($ruangan->transaksis()->count() > 0) {
            return redirect()->route('ruangan.index')
                ->with('error', 'Ruangan tidak dapat dihapus karena masih digunakan dalam transaksi');
        }

        $ruangan->delete();

        return redirect()->route('ruangan.index')
            ->with('success', 'Data ruangan berhasil dihapus');
    }

    public function bulkDelete(Request $request)
    {
        // Hanya admin yang bisa menghapus ruangan
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('ruangan.index')
                ->with('error', 'Hanya admin yang dapat menghapus data ruangan');
        }

        $ids = $request->input('ids', '');
        
        // Convert string to array (comma-separated from JavaScript)
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }
        
        // Filter empty values and ensure integers
        $ids = array_filter(array_map('intval', $ids));
        
        if (empty($ids)) {
            return back()->with('error', 'Pilih minimal satu ruangan untuk dihapus');
        }

        DB::beginTransaction();
        try {
            $ruangans = Ruangan::whereIn('id', $ids)->get();
            $deletedCount = 0;
            $skippedCount = 0;

            foreach ($ruangans as $ruangan) {
                if ($ruangan->transaksis()->count() > 0) {
                    $skippedCount++;
                    continue;
                }
                
                $ruangan->delete();
                $deletedCount++;
            }

            DB::commit();
            
            $message = $deletedCount . ' ruangan berhasil dihapus.';
            if ($skippedCount > 0) {
                $message .= ' (' . $skippedCount . ' ruangan dilewati karena masih digunakan dalam transaksi).';
            }
            
            return redirect()->route('ruangan.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
