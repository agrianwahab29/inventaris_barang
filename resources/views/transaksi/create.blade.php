@extends('layouts.app')

@section('title', 'Barang Masuk/Keluar - Aplikasi Inventaris')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('transaksi.index') }}">Transaksi</a></li>
    <li class="breadcrumb-item active">Barang Masuk/Keluar</li>
@endsection

@section('page_title', 'Barang Masuk/Keluar')

@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .form-section {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    
    .section-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px dashed #e2e8f0;
    }
    
    .section-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }
    
    .section-masuk { background: #d1fae5; color: #059669; }
    .section-keluar { background: #fef3c7; color: #d97706; }
    .section-info { background: #e0e7ff; color: #4f46e5; }
    
    .info-card {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 10px;
        padding: 16px;
        border: 1px solid #e2e8f0;
    }
    
    .info-item {
        text-align: center;
    }
    
    .info-value {
        font-size: 1.5rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 4px;
    }
    
    .info-label {
        font-size: 0.625rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.625rem;
    }
    
    .input-group-custom {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s;
    }
    
    .input-group-custom:focus-within {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.1);
    }
    
    .input-group-custom input,
    .input-group-custom select {
        border: none;
        box-shadow: none;
    }
    
    .input-group-custom .input-group-text {
        background: #f8fafc;
        border: none;
        color: #64748b;
        font-weight: 500;
        font-size: 0.75rem;
    }
    
    .calculation-box {
        background: #f8fafc;
        border-radius: 8px;
        padding: 10px;
        text-align: center;
    }
    
    .calculation-value {
        font-size: 1.125rem;
        font-weight: 700;
        margin-bottom: 2px;
    }
    
    .calculation-label {
        font-size: 0.625rem;
        color: #64748b;
        text-transform: uppercase;
    }
    
    .btn-submit {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
        border: none;
        padding: 10px 28px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
        color: white;
    }
    
    .btn-cancel {
        background: #f1f5f9;
        color: #64748b;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.3s;
    }
    
    .btn-cancel:hover {
        background: #e2e8f0;
        color: #475569;
    }
    
    @media (max-width: 767.98px) {
        .section-icon {
            width: 32px;
            height: 32px;
            font-size: 14px;
        }
        
        .section-header {
            gap: 8px;
            margin-bottom: 12px;
            padding-bottom: 10px;
        }
        
        .section-header h5 {
            font-size: 1rem;
        }
    }
    
    @media (max-width: 575.98px) {
        .form-section {
            padding: 14px;
            border-radius: 10px;
        }
        
        .info-card {
            padding: 10px;
        }
        
        .info-value {
            font-size: 1.25rem;
        }
        
        .calculation-box {
            padding: 8px;
        }
        
        .calculation-value {
            font-size: 1rem;
        }
        
        .btn-submit, .btn-cancel {
            padding: 8px 16px;
            font-size: 0.8125rem;
        }
        
        .section-icon {
            width: 30px;
            height: 30px;
            font-size: 14px;
        }
        
        .d-flex.justify-content-between {
            flex-wrap: wrap;
            gap: 8px;
        }
    }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <form method="POST" action="{{ route('transaksi.store') }}" id="formTransaksi">
            @csrf
            
            <!-- Section: Pilih Barang -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon section-info">
                        <i class="fas fa-box"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 fw-bold">Pilih Barang</h5>
                        <p class="mb-0 text-muted small">Pilih barang untuk melihat informasi stok</p>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-medium">Nama Barang <span class="text-danger">*</span></label>
                    <div class="input-group-custom">
                        <select name="barang_id" id="barang_id" class="form-select form-select-lg" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach($barangs as $barang)
                                <option value="{{ $barang->id }}" 
                                        data-stok="{{ $barang->stok }}" 
                                        data-satuan="{{ $barang->satuan }}" 
                                        data-min="{{ $barang->stok_minimum }}"
                                        data-nama="{{ $barang->nama_barang }}"
                                        data-kategori="{{ $barang->kategori }}"
                                        {{ old('barang_id') == $barang->id ? 'selected' : '' }}>
                                    {{ $barang->nama_barang }} (Stok: {{ $barang->stok }} {{ $barang->satuan }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('barang_id')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Hidden field for staleness check -->
                <input type="hidden" name="latest_timestamp" id="latest_timestamp" value="{{ $latestTimestamp ?? '' }}">

                <!-- Info Card -->
                <div id="infoBoxBarang" class="info-card" style="display: none;">
                    <div class="row g-4">
                        <div class="col-md-3 col-6">
                            <div class="info-item">
                                <div class="info-value" id="infoStok">-</div>
                                <div class="info-label">Stok Saat Ini</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="info-item">
                                <div class="info-value" id="infoSatuan">-</div>
                                <div class="info-label">Satuan</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="info-item">
                                <div class="mt-2">
                                    <span class="status-badge" id="infoStatus">
                                        <i class="fas fa-circle fa-xs"></i> <span>-</span>
                                    </span>
                                </div>
                                <div class="info-label mt-2">Status Stok</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6" style="display: none;">
                            <div class="info-item">
                                <div class="info-value text-secondary" id="infoStokMin">-</div>
                                <div class="info-label">Stok Minimum</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section: Paraf (tersembunyi di tampilan, input tetap aktif) -->
            <div class="form-section d-none">
                <input type="text" name="keterangan" class="form-control" value="" readonly placeholder="Kolom paraf akan dicetak kosong">
            </div>

            <!-- Section: Barang Masuk -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon section-masuk">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 fw-bold text-success">Barang Masuk</h5>
                        <p class="mb-0 text-muted small">Isi jika ada barang yang masuk ke gudang</p>
                    </div>
                </div>
                
                <div class="row g-4">
                <div class="col-md-4 col-12 col-sm-4">
                    <label class="form-label fw-medium" for="jumlah_masuk">Jumlah Masuk</label>
                    <div class="input-group-custom">
                        <input type="number" name="jumlah_masuk" id="jumlah_masuk" class="form-control" 
                               value="{{ old('jumlah_masuk', 0) }}" min="0" placeholder="0" aria-describedby="help_masuk">
                        <span class="input-group-text satuan-label" id="satuan_masuk">-</span>
                    </div>
                    <small id="help_masuk" class="text-muted">Isi 0 jika tidak ada barang masuk</small>
                </div>

                    <div class="col-md-4 col-12 col-sm-4">
                        <label class="form-label fw-medium">Stok Setelah Masuk</label>
                        <div class="calculation-box">
                            <div class="calculation-value text-success" id="stok_setelah_masuk">-</div>
                            <div class="calculation-label">Stok Akhir</div>
                        </div>
                    </div>

                <div class="col-md-4 col-12 col-sm-4">
                    <label class="form-label fw-medium" for="tanggal_masuk">Tanggal Masuk</label>
                    <input type="date" id="tanggal_masuk" name="tanggal_masuk" class="form-control" 
                           value="{{ old('tanggal_masuk', date('Y-m-d')) }}" aria-label="Tanggal barang masuk">
                </div>
                </div>
            </div>

            <!-- Section: Barang Keluar -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon section-keluar">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 fw-bold text-warning">Barang Keluar</h5>
                        <p class="mb-0 text-muted small">Isi jika ada barang yang keluar dari gudang</p>
                    </div>
                </div>
                
                <div class="row g-4">
                <div class="col-md-3 col-6 col-sm-3">
                    <label class="form-label fw-medium" for="jumlah_keluar">Jumlah Keluar</label>
                    <div class="input-group-custom">
                        <input type="number" name="jumlah_keluar" id="jumlah_keluar" class="form-control" 
                               value="{{ old('jumlah_keluar', 0) }}" min="0" placeholder="0" aria-describedby="help_keluar">
                        <span class="input-group-text satuan-label" id="satuan_keluar">-</span>
                    </div>
                    <small id="help_keluar" class="text-muted">Isi 0 jika tidak ada barang keluar</small>
                </div>

                    <div class="col-md-3 col-6 col-sm-3">
                        <label class="form-label fw-medium">Sisa Setelah Keluar</label>
                        <div class="calculation-box">
                            <div class="calculation-value" id="sisa_setelah_keluar">-</div>
                            <div class="calculation-label">Sisa Stok</div>
                        </div>
                    </div>

                <div class="col-md-3 col-6 col-sm-3">
                    <label class="form-label fw-medium" for="tanggal_keluar">Tanggal Keluar</label>
                    {{-- BUG FIX: tanggal_keluar should default to empty, NOT today's date.
                         Only set a date when jumlah_keluar > 0 (actual barang keluar).
                         Previous value "{{ old('tanggal_keluar', date('Y-m-d')) }}" caused
                         masuk-only transactions to have a tanggal_keluar, showing wrong data
                         in the index table. Also note: existing records with tanggal_keluar
                         set but jumlah_keluar = 0/null are a data issue that may need manual cleanup. --}}
                    <input type="date" id="tanggal_keluar" name="tanggal_keluar" class="form-control" 
                           value="{{ old('tanggal_keluar', '') }}" aria-label="Tanggal barang keluar">
                </div>

                    <input type="hidden" name="tipe_pengambil" value="nama_ruangan">
                </div>

                <div class="row g-4 mt-2">
                <div class="col-md-6 col-12 col-sm-6" id="field_nama">
                    <label class="form-label fw-medium" for="nama_pengambil">Nama Pengambil</label>
                    <div class="input-group-custom">
                        <span class="input-group-text" id="icon_user"><i class="fas fa-user"></i></span>
                        <input type="text" name="nama_pengambil" id="nama_pengambil" class="form-control" 
                               value="{{ old('nama_pengambil') }}" placeholder="Contoh: Wahab" aria-describedby="icon_user">
                    </div>
                </div>

                <div class="col-md-6 col-12 col-sm-6">
                    <label class="form-label fw-medium" for="ruangan_id">Ruangan Tujuan</label>
                    <div class="input-group-custom">
                        <span class="input-group-text" id="icon_ruangan"><i class="fas fa-door-open"></i></span>
                        <select name="ruangan_id" id="ruangan_id" class="form-select" aria-describedby="icon_ruangan">
                            <option value="">-- Pilih Ruangan --</option>
                            @foreach($ruangans as $ruangan)
                                <option value="{{ $ruangan->id }}" {{ old('ruangan_id') == $ruangan->id ? 'selected' : '' }}>
                                    {{ $ruangan->nama_ruangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 8px;">
                <a href="{{ route('transaksi.index') }}" class="btn-cancel">
                    <i class="fas fa-arrow-left me-2"></i>Batal
                </a>
                <button type="submit" class="btn-submit" id="btnSimpanTransaksi" data-loading-text="Menyimpan...">
                    <i class="fas fa-save"></i>
                    Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // BUG FIX #3: Reset form after successful submission
    // Check if page was loaded with success message (redirected after submit)
    @if(session('success'))
        // Clear URL parameters to prevent form resubmission on back button
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    @endif
    
    // Prevent form data persistence on back button
    if (window.performance && window.performance.navigation.type === window.performance.navigation.TYPE_BACK_FORWARD) {
        // Page loaded from back/forward button - reset the form
        document.getElementById('formTransaksi').reset();
        document.getElementById('infoBoxBarang').style.display = 'none';
    }
    
    const barangSelect = document.getElementById('barang_id');
    const jumlahMasukInput = document.getElementById('jumlah_masuk');
    const jumlahKeluarInput = document.getElementById('jumlah_keluar');
    const tanggalKeluarInput = document.getElementById('tanggal_keluar');
    const stokSetelahMasukInput = document.getElementById('stok_setelah_masuk');
    const sisaKeluarInput = document.getElementById('sisa_setelah_keluar');
    const satuanLabels = document.querySelectorAll('.satuan-label');
    const fieldNama = document.getElementById('field_nama');
    const infoBoxBarang = document.getElementById('infoBoxBarang');
    const infoStok = document.getElementById('infoStok');
    const infoSatuan = document.getElementById('infoSatuan');
    const infoStatus = document.getElementById('infoStatus');
    const infoStokMin = document.getElementById('infoStokMin');

    let stokAwal = 0;
    let satuan = '-';
    let stokMinimum = 0;

    function updateCalculations() {
        const selected = barangSelect.options[barangSelect.selectedIndex];
        
        if (barangSelect.value === '') {
            infoBoxBarang.style.display = 'none';
            return;
        }
        
        stokAwal = parseInt(selected.getAttribute('data-stok')) || 0;
        satuan = selected.getAttribute('data-satuan') || '-';
        stokMinimum = parseInt(selected.getAttribute('data-min')) || 0;
        
        // Tampilkan info box
        infoBoxBarang.style.display = 'block';
        
        // Update info box
        infoStok.textContent = stokAwal;
        infoSatuan.textContent = satuan;
        infoStokMin.textContent = stokMinimum;
        
        // Update status stok dengan warna
        if (stokAwal <= 0) {
            infoStatus.className = 'status-badge bg-danger text-white';
            infoStatus.innerHTML = '<i class="fas fa-times-circle"></i> <span>STOK HABIS</span>';
            infoStok.className = 'info-value text-danger';
        } else if (stokAwal <= stokMinimum) {
            infoStatus.className = 'status-badge bg-warning text-dark';
            infoStatus.innerHTML = '<i class="fas fa-exclamation-circle"></i> <span>STOK RENDAH</span>';
            infoStok.className = 'info-value text-warning';
        } else {
            infoStatus.className = 'status-badge bg-success text-white';
            infoStatus.innerHTML = '<i class="fas fa-check-circle"></i> <span>STOK AMAN</span>';
            infoStok.className = 'info-value text-success';
        }
        
        // Update satuan labels
        satuanLabels.forEach(el => el.textContent = satuan);
        
        // Hitung stok setelah masuk
        const jumlahMasuk = parseInt(jumlahMasukInput.value) || 0;
        const stokSetelahMasuk = stokAwal + jumlahMasuk;
        stokSetelahMasukInput.textContent = stokSetelahMasuk;
        
        // Hitung sisa setelah keluar (dari stok setelah masuk)
        const jumlahKeluar = parseInt(jumlahKeluarInput.value) || 0;
        const sisaSetelahKeluar = stokSetelahMasuk - jumlahKeluar;
        
        if (sisaSetelahKeluar < 0) {
            sisaKeluarInput.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Tidak cukup!</span>';
            sisaKeluarInput.className = 'calculation-value';
        } else if (sisaSetelahKeluar === 0) {
            sisaKeluarInput.innerHTML = '<span class="text-warning">0 <small>(Habis)</small></span>';
            sisaKeluarInput.className = 'calculation-value';
        } else {
            sisaKeluarInput.textContent = sisaSetelahKeluar;
            sisaKeluarInput.className = 'calculation-value text-success';
        }
    }

    // BUG FIX: Auto-set/clear tanggal_keluar based on jumlah_keluar.
    // When jumlah_keluar > 0, auto-fill tanggal_keluar with today's date.
    // When jumlah_keluar is 0 or empty, clear tanggal_keluar so it submits as null.
    // Only auto-set if the user hasn't manually entered a date (field is empty).
    function updateTanggalKeluar() {
        const jumlahKeluar = parseInt(jumlahKeluarInput.value) || 0;
        if (jumlahKeluar > 0) {
            // Only auto-set if the user hasn't manually picked a date
            if (tanggalKeluarInput.value === '') {
                const today = new Date().toISOString().split('T')[0]; // YYYY-MM-DD
                tanggalKeluarInput.value = today;
            }
        } else {
            // No barang keluar — clear the date to prevent stale data
            tanggalKeluarInput.value = '';
        }
    }

    // Event listeners
    barangSelect.addEventListener('change', updateCalculations);
    jumlahMasukInput.addEventListener('input', updateCalculations);
    jumlahKeluarInput.addEventListener('input', function() {
        updateCalculations();
        updateTanggalKeluar();
    });

    // Form validation and submit handling (BUG #4 fix)
    const formTransaksi = document.getElementById('formTransaksi');
    const btnSimpan = document.getElementById('btnSimpanTransaksi');
    let isSubmitting = false;
    
    formTransaksi.addEventListener('submit', function(e) {
        // Prevent double submit
        if (isSubmitting) {
            e.preventDefault();
            return false;
        }
        
        // Validate form
        const barangId = barangSelect.value;
        const jumlahMasuk = parseInt(jumlahMasukInput.value) || 0;
        const jumlahKeluar = parseInt(jumlahKeluarInput.value) || 0;
        const stokSetelahMasuk = stokAwal + jumlahMasuk;
        const sisaSetelahKeluar = stokSetelahMasuk - jumlahKeluar;
        
        if (!barangId) {
            e.preventDefault();
            alert('Pilih barang terlebih dahulu');
            barangSelect.focus();
            return false;
        }
        
        if (jumlahMasuk === 0 && jumlahKeluar === 0) {
            e.preventDefault();
            alert('Isi jumlah masuk atau jumlah keluar minimal 1');
            return false;
        }
        
        if (sisaSetelahKeluar < 0) {
            e.preventDefault();
            alert('Stok tidak mencukupi untuk jumlah keluar yang diminta');
            jumlahKeluarInput.focus();
            return false;
        }
        
        // Disable button and show loading state
        isSubmitting = true;
        btnSimpan.disabled = true;
        btnSimpan.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
        
        // Form will submit normally, button will be re-enabled on page reload
        return true;
    });

    // Initialize
    @if(old('barang_id'))
        updateCalculations();
    @endif
</script>
@endsection
