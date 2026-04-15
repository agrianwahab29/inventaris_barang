@extends('layouts.app')

@section('title', 'Detail Barang - Aplikasi Inventaris')

@section('content')
<div class="row">
    <div class="col-md-4 col-12 mb-3">
        <div class="card table-container">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-box me-2"></i>Detail Barang</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td class="text-muted" width="40%">Nama</td>
                        <td class="fw-semibold">{{ $barang->nama_barang }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kategori</td>
                        <td><span class="badge bg-info">{{ $barang->kategori }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Satuan</td>
                        <td>{{ $barang->satuan }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Stok</td>
                        <td>
                            <span class="badge badge-{{ $barang->isStokHabis() ? 'stock-habis' : ($barang->isStokRendah() ? 'stock-rendah' : 'stock-aman') }} fs-6">
                                {{ $barang->stok }} {{ $barang->satuan }}
                            </span>
                        </td>
                    </tr>
                    <!-- Baris Stok Minimum disembunyikan - tetap tersimpan di database -->
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            @if($barang->isStokHabis())
                                <span class="badge bg-danger">Stok Habis</span>
                            @elseif($barang->isStokRendah())
                                <span class="badge bg-warning">Stok Rendah</span>
                            @else
                                <span class="badge bg-success">Stok Aman</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Dibuat</td>
                        <td>{{ $barang->created_at->format('d M Y H:i') }}</td>
                    </tr>
                </table>
                
                @if($barang->catatan)
                    <div class="alert alert-light border mt-3">
                        <small class="text-muted">Catatan:</small>
                        <p class="mb-0">{{ $barang->catatan }}</p>
                    </div>
                @endif
                
                <div class="d-grid gap-2 mt-4">
                    <a href="{{ route('barang.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('barang.edit', $barang) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit Barang
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8 col-12">
        <div class="card table-container">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Transaksi</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Jumlah</th>
                                <th>Sisa Stok</th>
                                <th>Ruangan</th>
                                <th>User</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transaksis as $transaksi)
                                <tr>
                                    <td>{{ $transaksi->tanggal->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $transaksi->tipe == 'masuk' ? 'success' : 'warning' }}">
                                            <i class="fas fa-arrow-{{ $transaksi->tipe == 'masuk' ? 'down' : 'up' }} me-1"></i>
                                            {{ ucfirst($transaksi->tipe) }}
                                        </span>
                                    </td>
                                    <td>{{ $transaksi->jumlah }} {{ $barang->satuan }}</td>
                                    <td>{{ $transaksi->sisa_stok }}</td>
                                    <td>{{ $transaksi->ruangan ? $transaksi->ruangan->nama_ruangan : '-' }}</td>
                                    <td>{{ $transaksi->user->name }}</td>
                                    <td>{{ $transaksi->keterangan ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2"></i>
                                        <p class="mb-0">Belum ada transaksi</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white">
                {{ $transaksis->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
