@extends('layouts.app')

@section('title', 'Detail Berkas - ' . $berkasTransaksi->file_name)

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('berkas-transaksi.index') }}">Berkas Transaksi</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-0">Detail Berkas</h1>
            <p class="text-muted small mb-0">Informasi lengkap dokumen arsip</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('berkas-transaksi.download', $berkasTransaksi) }}" class="btn btn-success btn-sm">
                <i class="fas fa-download me-1"></i> Download
            </a>
            <a href="{{ route('berkas-transaksi.edit', $berkasTransaksi) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="{{ route('berkas-transaksi.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- PDF Preview -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-file-pdf me-1 text-danger"></i> Preview PDF</h6>
                    <span class="badge bg-light text-dark">{{ $berkasTransaksi->file_name }}</span>
                </div>
                <div class="card-body p-0">
                    <div style="height: 600px; background: #f8f9fa;">
                        <iframe src="{{ asset('storage/' . $berkasTransaksi->file_path) }}" 
                                width="100%" height="100%" style="border: none;"></iframe>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- File Info -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-1"></i> Informasi File</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="text-muted" width="35%">Nama File</td>
                            <td class="fw-medium">{{ $berkasTransaksi->file_name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Ukuran</td>
                            <td>{{ $berkasTransaksi->file_size_human }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tipe</td>
                            <td><span class="badge bg-danger">PDF</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Diupload</td>
                            <td>{{ $berkasTransaksi->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Uploader</td>
                            <td>{{ $berkasTransaksi->user->name ?? 'Unknown' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Document Info -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-file-alt me-1"></i> Informasi Dokumen</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="text-muted" width="35%">Nomor Surat</td>
                            <td class="fw-medium">{{ $berkasTransaksi->nomor_surat ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Surat</td>
                            <td>{{ $berkasTransaksi->tanggal_surat ? $berkasTransaksi->tanggal_surat->format('d F Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Perihal</td>
                            <td>{{ $berkasTransaksi->perihal ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Pengirim</td>
                            <td>{{ $berkasTransaksi->pengirim ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Penerima</td>
                            <td>{{ $berkasTransaksi->penerima ?? '-' }}</td>
                        </tr>
                    </table>
                    
                    @if($berkasTransaksi->keterangan)
                    <hr>
                    <h6 class="small fw-medium">Keterangan:</h6>
                    <p class="small text-muted mb-0">{{ $berkasTransaksi->keterangan }}</p>
                    @endif
                </div>
            </div>
            
            <!-- Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('berkas-transaksi.download', $berkasTransaksi) }}" class="btn btn-success">
                            <i class="fas fa-download me-1"></i> Download File
                        </a>
                        <a href="{{ route('berkas-transaksi.edit', $berkasTransaksi) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> Edit Informasi
                        </a>
                        <button type="button" class="btn btn-outline-danger" 
                                onclick="if(confirm('Yakin ingin menghapus berkas ini?')) { document.getElementById('deleteForm').submit(); }">
                            <i class="fas fa-trash me-1"></i> Hapus Berkas
                        </button>
                    </div>
                    
                    <form id="deleteForm" action="{{ route('berkas-transaksi.destroy', $berkasTransaksi) }}" method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
