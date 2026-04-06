@extends('layouts.app')

@section('title', 'Edit Ruangan - Aplikasi Inventaris')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card table-container">
            <div class="card-header bg-warning text-dark py-3">
                <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Ruangan</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('ruangan.update', $ruangan) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Ruangan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_ruangan" class="form-control @error('nama_ruangan') is-invalid @enderror" 
                               value="{{ old('nama_ruangan', $ruangan->nama_ruangan) }}" required>
                        @error('nama_ruangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" 
                                  rows="3" placeholder="Catatan tambahan (opsional)">{{ old('keterangan', $ruangan->keterangan) }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('ruangan.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-2"></i>Update Ruangan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
