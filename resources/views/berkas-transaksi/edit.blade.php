@extends('layouts.app')

@section('title', 'Edit Berkas - ' . $berkasTransaksi->file_name)

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('berkas-transaksi.index') }}">Berkas Transaksi</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-0">Edit Berkas</h1>
            <p class="text-muted small mb-0">{{ $berkasTransaksi->file_name }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('berkas-transaksi.show', $berkasTransaksi) }}" class="btn btn-info btn-sm">
                <i class="fas fa-eye me-1"></i> Detail
            </a>
            <a href="{{ route('berkas-transaksi.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-edit me-1"></i> Form Edit</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('berkas-transaksi.update', $berkasTransaksi) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nomor Surat <span class="text-muted">(Opsional)</span></label>
                                <input type="text" name="nomor_surat" class="form-control @error('nomor_surat') is-invalid @enderror" 
                                       placeholder="Contoh: 001/ST/VI/2026" 
                                       value="{{ old('nomor_surat', $berkasTransaksi->nomor_surat) }}">
                                @error('nomor_surat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Surat <span class="text-muted">(Opsional)</span></label>
                                <input type="date" name="tanggal_surat" class="form-control @error('tanggal_surat') is-invalid @enderror" 
                                       value="{{ old('tanggal_surat', $berkasTransaksi->tanggal_surat ? $berkasTransaksi->tanggal_surat->format('Y-m-d') : '') }}">
                                @error('tanggal_surat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">Perihal / Keperluan <span class="text-muted">(Opsional)</span></label>
                                <input type="text" name="perihal" class="form-control @error('perihal') is-invalid @enderror" 
                                       placeholder="Contoh: Serah terima barang ATK Q2 2026" 
                                       value="{{ old('perihal', $berkasTransaksi->perihal) }}">
                                @error('perihal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Pengirim <span class="text-muted">(Opsional)</span></label>
                                <input type="text" name="pengirim" class="form-control @error('pengirim') is-invalid @enderror" 
                                       placeholder="Nama pihak yang menyerahkan" 
                                       value="{{ old('pengirim', $berkasTransaksi->pengirim) }}">
                                @error('pengirim')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Penerima <span class="text-muted">(Opsional)</span></label>
                                <input type="text" name="penerima" class="form-control @error('penerima') is-invalid @enderror" 
                                       placeholder="Nama pihak yang menerima" 
                                       value="{{ old('penerima', $berkasTransaksi->penerima) }}">
                                @error('penerima')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">Keterangan <span class="text-muted">(Opsional)</span></label>
                                <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" 
                                          rows="3" placeholder="Keterangan tambahan...">{{ old('keterangan', $berkasTransaksi->keterangan) }}</textarea>
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">File PDF <span class="text-muted">(Kosongkan jika tidak ingin mengubah)</span></label>
                                <div class="input-group">
                                    <input type="file" name="file" id="fileInput" 
                                           class="form-control @error('file') is-invalid @enderror" 
                                           accept=".pdf" onchange="previewFile()">
                                    <span class="input-group-text"><i class="fas fa-file-pdf text-danger"></i></span>
                                </div>
                                <div class="form-text">
                                    Format: PDF | Maksimal: 10MB | Biarkan kosong jika tidak ingin mengganti file
                                </div>
                                @error('file')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                
                                <!-- Current File Info -->
                                <div class="mt-2 p-2 bg-light rounded">
                                    <small class="text-muted">File saat ini:</small>
                                    <div class="d-flex align-items-center mt-1">
                                        <i class="fas fa-file-pdf text-danger me-2"></i>
                                        <div>
                                            <div class="small fw-medium">{{ $berkasTransaksi->file_name }}</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">{{ $berkasTransaksi->file_size_human }}</div>
                                        </div>
                                        <a href="{{ route('berkas-transaksi.download', $berkasTransaksi) }}" 
                                           class="btn btn-sm btn-outline-success ms-auto">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </div>
                                
                                <!-- New File Preview -->
                                <div id="filePreview" class="mt-2 p-2 bg-light rounded d-none">
                                    <small class="text-success">File baru:</small>
                                    <div class="d-flex align-items-center mt-1">
                                        <i class="fas fa-file-pdf text-success me-2"></i>
                                        <div>
                                            <div class="small fw-medium" id="previewFileName"></div>
                                            <div class="text-muted" style="font-size: 0.75rem;" id="previewFileSize"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('berkas-transaksi.show', $berkasTransaksi) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-history me-1"></i> Riwayat</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted" width="40%">Diupload</td>
                            <td>{{ $berkasTransaksi->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Uploader</td>
                            <td>{{ $berkasTransaksi->user->name ?? 'Unknown' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Terakhir Update</td>
                            <td>{{ $berkasTransaksi->updated_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function previewFile() {
    const input = document.getElementById('fileInput');
    const preview = document.getElementById('filePreview');
    const fileName = document.getElementById('previewFileName');
    const fileSize = document.getElementById('previewFileSize');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Check file size (10MB = 10 * 1024 * 1024 bytes)
        if (file.size > 10 * 1024 * 1024) {
            alert('Ukuran file melebihi 10MB. Silakan pilih file yang lebih kecil.');
            input.value = '';
            preview.classList.add('d-none');
            return;
        }
        
        // Show preview
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);
        preview.classList.remove('d-none');
    }
}

function formatFileSize(bytes) {
    if (bytes >= 1073741824) {
        return (bytes / 1073741824).toFixed(2) + ' GB';
    } else if (bytes >= 1048576) {
        return (bytes / 1048576).toFixed(2) + ' MB';
    } else if (bytes >= 1024) {
        return (bytes / 1024).toFixed(2) + ' KB';
    } else {
        return bytes + ' bytes';
    }
}
</script>
@endsection
