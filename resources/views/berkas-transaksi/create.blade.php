@extends('layouts.app')

@section('title', 'Upload Berkas - Arsip Dokumen')

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('berkas-transaksi.index') }}">Berkas Transaksi</a></li>
            <li class="breadcrumb-item active">Upload</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-0">Upload Berkas</h1>
            <p class="text-muted small mb-0">Upload dokumen serah terima barang (PDF)</p>
        </div>
        <a href="{{ route('berkas-transaksi.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-upload me-1"></i> Form Upload</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('berkas-transaksi.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nomor Surat <span class="text-muted">(Opsional)</span></label>
                                <input type="text" name="nomor_surat" class="form-control @error('nomor_surat') is-invalid @enderror" 
                                       placeholder="Contoh: 001/ST/VI/2026" value="{{ old('nomor_surat') }}">
                                @error('nomor_surat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Surat <span class="text-muted">(Opsional)</span></label>
                                <input type="date" name="tanggal_surat" class="form-control @error('tanggal_surat') is-invalid @enderror" 
                                       value="{{ old('tanggal_surat') }}">
                                @error('tanggal_surat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">Perihal / Keperluan <span class="text-muted">(Opsional)</span></label>
                                <input type="text" name="perihal" class="form-control @error('perihal') is-invalid @enderror" 
                                       placeholder="Contoh: Serah terima barang ATK Q2 2026" value="{{ old('perihal') }}">
                                @error('perihal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Pengirim <span class="text-muted">(Opsional)</span></label>
                                <input type="text" name="pengirim" class="form-control @error('pengirim') is-invalid @enderror" 
                                       placeholder="Nama pihak yang menyerahkan" value="{{ old('pengirim') }}">
                                @error('pengirim')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Penerima <span class="text-muted">(Opsional)</span></label>
                                <input type="text" name="penerima" class="form-control @error('penerima') is-invalid @enderror" 
                                       placeholder="Nama pihak yang menerima" value="{{ old('penerima') }}">
                                @error('penerima')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">Keterangan <span class="text-muted">(Opsional)</span></label>
                                <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" 
                                          rows="3" placeholder="Keterangan tambahan...">{{ old('keterangan') }}</textarea>
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">File PDF <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="file" name="file" id="fileInput" 
                                           class="form-control @error('file') is-invalid @enderror" 
                                           accept=".pdf" required onchange="previewFile()">
                                    <span class="input-group-text"><i class="fas fa-file-pdf text-danger"></i></span>
                                </div>
                                <div class="form-text">
                                    Format: PDF | Maksimal: 10MB
                                </div>
                                @error('file')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                
                                <!-- File Preview -->
                                <div id="filePreview" class="mt-3 p-3 bg-light rounded d-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-pdf fa-2x text-danger me-3"></i>
                                        <div>
                                            <div class="fw-medium" id="previewFileName"></div>
                                            <div class="text-muted small" id="previewFileSize"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('berkas-transaksi.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload me-1"></i> Upload Berkas
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-1"></i> Informasi</h6>
                </div>
                <div class="card-body">
                    <h6 class="fw-medium">Ketentuan Upload:</h6>
                    <ul class="list-unstyled small text-muted mb-3">
                        <li><i class="fas fa-check text-success me-1"></i> Format file: PDF</li>
                        <li><i class="fas fa-check text-success me-1"></i> Maksimal ukuran: 10MB</li>
                        <li><i class="fas fa-check text-success me-1"></i> Dokumen harus sudah dilegalisir</li>
                        <li><i class="fas fa-check text-success me-1"></i> Scan dengan resolusi minimal 300dpi</li>
                    </ul>
                    
                    <hr>
                    
                    <h6 class="fw-medium">Data yang Disimpan:</h6>
                    <ul class="list-unstyled small text-muted mb-0">
                        <li><i class="fas fa-file me-1"></i> File PDF</li>
                        <li><i class="fas fa-calendar me-1"></i> Tanggal upload</li>
                        <li><i class="fas fa-user me-1"></i> User yang mengupload</li>
                        <li><i class="fas fa-info-circle me-1"></i> Informasi surat</li>
                    </ul>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-warning">
                    <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-1"></i> Perhatian</h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-0">
                        Dokumen yang diupload akan menjadi arsip digital. 
                        Pastikan dokumen asli tetap disimpan dengan baik.
                    </p>
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
