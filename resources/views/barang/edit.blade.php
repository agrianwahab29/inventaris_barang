@extends('layouts.app')

@section('title', 'Edit Barang - Aplikasi Inventaris')

@section('content')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectEl = document.getElementById('satuanSelect');
    const inputEl = document.getElementById('satuanInput');
    const currentSatuan = '{{ $barang->satuan }}';
    const predefinedSatuan = @json($satuans);
    
    if (selectEl && inputEl) {
        // Check if current unit is custom
        if (!predefinedSatuan.includes(currentSatuan)) {
            // Add current custom unit to options
            const customOption = document.createElement('option');
            customOption.value = currentSatuan;
            customOption.textContent = currentSatuan + ' (Custom)';
            customOption.selected = true;
            selectEl.insertBefore(customOption, selectEl.querySelector('option[value="__custom__"]'));
            inputEl.style.display = 'block';
            inputEl.value = currentSatuan;
            inputEl.required = true;
            selectEl.removeAttribute('name');
            inputEl.setAttribute('name', 'satuan');
        }
        
        selectEl.addEventListener('change', function() {
            if (this.value === '__custom__') {
                inputEl.style.display = 'block';
                inputEl.required = true;
                selectEl.required = false;
                selectEl.removeAttribute('name');
                inputEl.setAttribute('name', 'satuan');
            } else if (this.value) {
                inputEl.style.display = 'none';
                inputEl.required = false;
                selectEl.setAttribute('name', 'satuan');
                inputEl.removeAttribute('name');
                selectEl.value = this.value;
            }
        });
    }
});
</script>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card table-container">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-edit me-2 text-warning"></i>Edit Barang</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('barang.update', $barang) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" name="nama_barang" class="form-control @error('nama_barang') is-invalid @enderror" value="{{ old('nama_barang', $barang->nama_barang) }}" required>
                        @error('nama_barang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori" class="form-select @error('kategori') is-invalid @enderror" required>
                                @foreach($kategoris as $kat)
                                    <option value="{{ $kat }}" {{ old('kategori', $barang->kategori) == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                                @endforeach
                            </select>
                            @error('kategori')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Satuan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <select name="satuan_select" class="form-select" id="satuanSelect">
                                    <option value="">Pilih Satuan</option>
                                    @foreach($satuans as $sat)
                                        <option value="{{ $sat }}" {{ old('satuan', $barang->satuan) == $sat ? 'selected' : '' }}>{{ $sat }}</option>
                                    @endforeach
                                    <option value="__custom__">+ Tambah Satuan Baru</option>
                                </select>
                                <input type="text" name="satuan" class="form-control" id="satuanInput" value="{{ old('satuan', $barang->satuan) }}" placeholder="Atau ketik satuan">
                            </div>
                            @error('satuan')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok Saat Ini</label>
                            <input type="text" class="form-control" value="{{ $barang->stok }} {{ $barang->satuan }}" disabled>
                            <small class="text-muted">Untuk mengubah stok, gunakan menu transaksi</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok Minimum <span class="text-danger">*</span></label>
                            <input type="number" name="stok_minimum" class="form-control @error('stok_minimum') is-invalid @enderror" value="{{ old('stok_minimum', $barang->stok_minimum) }}" min="1" required>
                            @error('stok_minimum')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" rows="3">{{ old('catatan', $barang->catatan) }}</textarea>
                        @error('catatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('barang.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-2"></i>Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
