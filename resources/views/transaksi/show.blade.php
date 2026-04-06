@extends('layouts.app')

@section('title', 'Detail Transaksi - Aplikasi Inventaris')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-info-circle me-2"></i>Detail Transaksi</h4>
    <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card table-container">
            <div class="card-header bg-{{ $transaksi->jumlah_masuk > 0 && $transaksi->jumlah_keluar > 0 ? 'info' : ($transaksi->jumlah_masuk > 0 ? 'success' : 'warning') }} text-white py-3">
                <h5 class="mb-0">
                    <i class="fas fa-{{ $transaksi->jumlah_masuk > 0 && $transaksi->jumlah_keluar > 0 ? 'exchange-alt' : ($transaksi->jumlah_masuk > 0 ? 'arrow-down' : 'arrow-up') }} me-2"></i>
                    @if($transaksi->jumlah_masuk > 0 && $transaksi->jumlah_keluar > 0)
                        Transaksi Masuk & Keluar
                    @elseif($transaksi->jumlah_masuk > 0)
                        Barang Masuk
                    @else
                        Barang Keluar
                    @endif
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="250"><strong>Tanggal Transaksi</strong></td>
                        <td>: {{ $transaksi->tanggal->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Nama Barang</strong></td>
                        <td>: {{ $transaksi->barang->nama_barang }}</td>
                    </tr>
                    @if($transaksi->stok_sebelum !== null)
                    <tr>
                        <td><strong>Stok Sebelum</strong></td>
                        <td>: {{ $transaksi->stok_sebelum }} {{ $transaksi->barang->satuan }}</td>
                    </tr>
                    @endif
                    @if($transaksi->jumlah_masuk > 0)
                    <tr>
                        <td><strong>Jumlah Masuk</strong></td>
                        <td>: 
                            <span class="badge bg-success">{{ $transaksi->jumlah_masuk }} {{ $transaksi->barang->satuan }}</span>
                        </td>
                    </tr>
                    @if($transaksi->stok_setelah_masuk !== null)
                    <tr>
                        <td><strong>Stok Setelah Masuk</strong></td>
                        <td>: {{ $transaksi->stok_setelah_masuk }} {{ $transaksi->barang->satuan }}</td>
                    </tr>
                    @endif
                    @endif
                    @if($transaksi->jumlah_keluar > 0)
                    <tr>
                        <td><strong>Jumlah Keluar</strong></td>
                        <td>: 
                            <span class="badge bg-warning text-dark">{{ $transaksi->jumlah_keluar }} {{ $transaksi->barang->satuan }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Keluar</strong></td>
                        <td>: {{ $transaksi->tanggal_keluar ? $transaksi->tanggal_keluar->format('d M Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Nama/Bagian/Ruang yang Mengambil</strong></td>
                        <td>: {{ $transaksi->pengambil_formatted }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td><strong>Sisa Stok Akhir</strong></td>
                        <td>: 
                            <span class="badge badge-{{ ($transaksi->sisa_stok ?? 0) <= 0 ? 'stock-habis' : (($transaksi->sisa_stok ?? 0) <= $transaksi->barang->stok_minimum ? 'stock-rendah' : 'stock-aman') }}">
                                {{ $transaksi->sisa_stok ?? 0 }} {{ $transaksi->barang->satuan }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>User Input</strong></td>
                        <td>: {{ $transaksi->user->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Waktu Input</strong></td>
                        <td>: {{ $transaksi->created_at->format('d M Y H:i:s') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Paraf</strong></td>
                        <td>: </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
