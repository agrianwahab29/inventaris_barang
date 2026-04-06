@extends('layouts.app')

@section('title', 'Detail Ruangan - Aplikasi Inventaris')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-door-open me-2"></i>Detail Ruangan</h4>
    <div>
        <a href="{{ route('ruangan.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
        @if(Auth::user()->isAdmin())
        <a href="{{ route('ruangan.edit', $ruangan) }}" class="btn btn-warning ms-2">
            <i class="fas fa-edit me-2"></i>Edit
        </a>
        @endif
    </div>
</div>

<!-- Detail Ruangan -->
<div class="card table-container mb-4">
    <div class="card-header bg-primary text-white py-3">
        <h5 class="mb-0">Informasi Ruangan</h5>
    </div>
    <div class="card-body">
        <table class="table table-borderless">
            <tr>
                <td width="200"><strong>Nama Ruangan</strong></td>
                <td>: {{ $ruangan->nama_ruangan }}</td>
            </tr>
            <tr>
                <td><strong>Keterangan</strong></td>
                <td>: {{ $ruangan->keterangan ?: '-' }}</td>
            </tr>
            <tr>
                <td><strong>Dibuat Pada</strong></td>
                <td>: {{ $ruangan->created_at->format('d M Y H:i') }}</td>
            </tr>
            <tr>
                <td><strong>Diupdate Pada</strong></td>
                <td>: {{ $ruangan->updated_at->format('d M Y H:i') }}</td>
            </tr>
        </table>
    </div>
</div>

<!-- Riwayat Transaksi -->
<div class="card table-container">
    <div class="card-header bg-info text-white py-3">
        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Transaksi Barang Keluar ke Ruangan Ini</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Nama Pengambil</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $transaksis = $ruangan->transaksis()->with('barang')->orderBy('created_at', 'desc')->get();
                    @endphp
                    @forelse($transaksis as $index => $transaksi)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $transaksi->tanggal_keluar ? $transaksi->tanggal_keluar->format('d M Y') : $transaksi->tanggal->format('d M Y') }}</td>
                            <td>{{ $transaksi->barang->nama_barang }}</td>
                            <td>{{ $transaksi->jumlah }} {{ $transaksi->barang->satuan }}</td>
                            <td>{{ $transaksi->pengambil_formatted }}</td>
                            <td>{{ $transaksi->keterangan ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p class="mb-0">Tidak ada transaksi ke ruangan ini</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
