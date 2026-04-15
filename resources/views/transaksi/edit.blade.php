@extends('layouts.app')

@section('title', 'Edit Transaksi')

@section('content')
<style>
    @media (max-width: 767.98px) {
        .card-body { padding: 12px; }
        .card-header h5 { font-size: 1rem; }
    }
    @media (max-width: 575.98px) {
        .card-body { padding: 10px; }
        .form-label { font-size: 0.8125rem; }
    }
</style>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap" style="gap: 8px;">
        <h1 class="h3 mb-0">Edit Transaksi</h1>
        <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Form Edit Transaksi #{{ $transaksi->id }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('transaksi.update', $transaksi) }}" method="POST" id="formEditTransaksi">
                @csrf
                @method('PUT')

                <div class="row mb-4">
                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <label for="barang_id" class="form-label">Barang <span class="text-danger">*</span></label>
                            <select class="form-select @error('barang_id') is-invalid @enderror" id="barang_id" name="barang_id" required>
                                <option value="">Pilih Barang</option>
                                @foreach($barangs as $barang)
                                    <option value="{{ $barang->id }}" data-satuan="{{ $barang->satuan }}" data-stok="{{ $barang->stok }}"
                                        {{ old('barang_id', $transaksi->barang_id) == $barang->id ? 'selected' : '' }}>
                                        {{ $barang->nama_barang }} (Stok: {{ $barang->stok }} {{ $barang->satuan }})
                                    </option>
                                @endforeach
                            </select>
                            @error('barang_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info" id="stokInfo">
                            <strong>Stok Saat Ini:</strong> <span id="stokSaatIni">{{ $transaksi->barang->stok ?? 0 }}</span> <span id="satuanBarang">{{ $transaksi->barang->satuan ?? '' }}</span>
                        </div>

                        <div class="mb-3">
                            <label for="jumlah_masuk" class="form-label">Jumlah Masuk</label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('jumlah_masuk') is-invalid @enderror" 
                                    id="jumlah_masuk" name="jumlah_masuk" min="0" 
                                    value="{{ old('jumlah_masuk', $transaksi->jumlah_masuk) }}">
                                <span class="input-group-text" id="satuanMasuk">{{ $transaksi->barang->satuan ?? 'Buah' }}</span>
                            </div>
                            @error('jumlah_masuk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal') is-invalid @enderror" 
                                id="tanggal" name="tanggal" required
                                value="{{ old('tanggal', $transaksi->tanggal->format('Y-m-d')) }}">
                            @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $transaksi->keterangan) }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <label for="jumlah_keluar" class="form-label">Jumlah Keluar</label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('jumlah_keluar') is-invalid @enderror" 
                                    id="jumlah_keluar" name="jumlah_keluar" min="0"
                                    value="{{ old('jumlah_keluar', $transaksi->jumlah_keluar) }}">
                                <span class="input-group-text" id="satuanKeluar">{{ $transaksi->barang->satuan ?? 'Buah' }}</span>
                            </div>
                            @error('jumlah_keluar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tanggal Keluar - Selalu tampil dan bisa diedit -->
                        <div class="mb-3" id="tanggalKeluarDiv">
                            <label for="tanggal_keluar" class="form-label">Tanggal Keluar</label>
                            <input type="date" class="form-control @error('tanggal_keluar') is-invalid @enderror" 
                                id="tanggal_keluar" name="tanggal_keluar"
                                value="{{ old('tanggal_keluar', $transaksi->tanggal_keluar ? $transaksi->tanggal_keluar->format('Y-m-d') : '') }}"
                                placeholder="Kosongkan jika tidak ada">
                            <small class="text-muted">Kosongkan jika barang tidak keluar</small>
                            @error('tanggal_keluar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <input type="hidden" name="tipe_pengambil" value="nama_ruangan">

                        <div class="mb-3" id="ruanganDiv">
                            <label for="ruangan_id" class="form-label">Ruangan Tujuan <span class="text-danger">*</span></label>
                            <select class="form-select @error('ruangan_id') is-invalid @enderror" id="ruangan_id" name="ruangan_id">
                                <option value="">Pilih Ruangan...</option>
                                @foreach($ruangans as $ruangan)
                                    <option value="{{ $ruangan->id }}" 
                                        {{ old('ruangan_id', $transaksi->ruangan_id) == $ruangan->id ? 'selected' : '' }}>
                                        {{ $ruangan->nama_ruangan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('ruangan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="namaPengambilDiv">
                            <label for="nama_pengambil" class="form-label">Nama Pengambil <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_pengambil') is-invalid @enderror" 
                                id="nama_pengambil" name="nama_pengambil" placeholder="Masukkan nama pengambil"
                                value="{{ old('nama_pengambil', $transaksi->nama_pengambil) }}">
                            @error('nama_pengambil')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-between flex-wrap" style="gap: 8px;">
                    <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const barangSelect = document.getElementById('barang_id');
    
    function updateSatuan() {
        if (barangSelect && barangSelect.selectedOptions.length > 0 && barangSelect.value) {
            const selectedOption = barangSelect.selectedOptions[0];
            const satuan = selectedOption.getAttribute('data-satuan') || 'Buah';
            const stok = selectedOption.getAttribute('data-stok') || 0;
            
            const elMasuk = document.getElementById('satuanMasuk');
            const elKeluar = document.getElementById('satuanKeluar');
            const elBarang = document.getElementById('satuanBarang');
            const elStok = document.getElementById('stokSaatIni');
            
            if (elMasuk) elMasuk.textContent = satuan;
            if (elKeluar) elKeluar.textContent = satuan;
            if (elBarang) elBarang.textContent = satuan;
            if (elStok) elStok.textContent = stok;
        }
    }
    
    // Event listeners
    if (barangSelect) barangSelect.addEventListener('change', updateSatuan);
    
    // Initial check
    updateSatuan();
});
</script>
@endsection
