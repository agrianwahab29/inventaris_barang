@extends('layouts.app')

@section('title', 'Data Barang - Aplikasi Inventaris')
@section('page_title', 'Data Barang')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Barang</li>
@endsection

@section('styles')
<style>
    .page-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .filter-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }
    
    .data-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .card-header-custom {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 16px 20px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .stat-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .stat-badge-success {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
    }
    
    .stat-badge-warning {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
    }
    
    .stat-badge-danger {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #991b1b;
    }
    
    .bulk-toolbar {
        position: sticky;
        top: 56px;
        z-index: 100;
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border-radius: 0 0 12px 12px;
        padding: 12px 20px;
        margin: -20px -20px 16px -20px;
        box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.2);
        animation: slideDown 0.3s ease-out;
    }
    
    .bulk-toolbar form {
        margin: 0;
    }
    
    .action-btn {
        width: 32px;
        height: 32px;
        padding: 0;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }
    
    .action-btn:hover {
        transform: scale(1.1);
    }
    
    .status-badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    
    .status-available {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
    }
    
    .status-low {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
    }
    
    .status-out {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #991b1b;
    }
</style>
@endsection

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4 animate-fade-up">
    <div>
        <h4 class="mb-1 fw-bold"><i class="fas fa-box me-2" style="color: #667eea;"></i>Data Barang</h4>
        <p class="text-muted mb-0" style="font-size: 0.875rem;">Kelola inventaris barang Anda</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('barang.export', request()->all()) }}" class="btn btn-success">
            <i class="fas fa-file-excel me-2"></i>Export Excel
        </a>
        <a href="{{ route('barang.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah Barang
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card animate-fade-up animate-delay-1" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.5rem; font-weight: 700;">{{ $barangs->total() }}</div>
                        <div style="font-size: 0.75rem; opacity: 0.9;">Total Barang</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card animate-fade-up animate-delay-2" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <?php
                        $stokTersedia = $barangs->filter(function($b) {
                            return $b->stok > $b->stok_minimum;
                        })->count();
                        ?>
                        <div style="font-size: 1.5rem; font-weight: 700;">{{ $stokTersedia }}</div>
                        <div style="font-size: 0.75rem; opacity: 0.9;">Stok Tersedia</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card animate-fade-up animate-delay-3" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <?php
                        $stokRendah = $barangs->filter(function($b) {
                            return $b->stok <= $b->stok_minimum && $b->stok > 0;
                        })->count();
                        ?>
                        <div style="font-size: 1.5rem; font-weight: 700;">{{ $stokRendah }}</div>
                        <div style="font-size: 0.75rem; opacity: 0.9;">Stok Rendah</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="filter-card animate-fade-up animate-delay-4">
    <form method="GET" action="{{ route('barang.index') }}" class="row g-3">
        <div class="col-md-4">
            <label class="form-label" style="font-size: 0.75rem; font-weight: 600;">Cari Barang</label>
            <div class="input-group">
                <span class="input-group-text" style="border-radius: 10px 0 0 10px; background: #f8fafc;">
                    <i class="fas fa-search text-muted"></i>
                </span>
                <input type="text" name="search" class="form-control" placeholder="Nama barang..." value="{{ request('search') }}" style="border-radius: 0 10px 10px 0;">
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label" style="font-size: 0.75rem; font-weight: 600;">Kategori</label>
            <select name="kategori" class="form-select" style="border-radius: 10px;">
                <option value="">Semua Kategori</option>
                @foreach($kategoris as $kat)
                    <option value="{{ $kat }}" {{ request('kategori') == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label" style="font-size: 0.75rem; font-weight: 600;">Status</label>
            <select name="status" class="form-select" style="border-radius: 10px;">
                <option value="">Semua Status</option>
                <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                <option value="rendah" {{ request('status') == 'rendah' ? 'selected' : '' }}>Stok Rendah</option>
                <option value="habis" {{ request('status') == 'habis' ? 'selected' : '' }}>Stok Habis</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label" style="font-size: 0.75rem; font-weight: 600;">&nbsp;</label>
            <button type="submit" class="btn btn-primary w-100" style="border-radius: 10px;">
                <i class="fas fa-filter me-2"></i>Filter
            </button>
        </div>
    </form>
</div>

<!-- Bulk Delete Toolbar (Sticky Top) -->
@if(Auth::user()->isAdmin())
<div id="bulkToolbar" class="bulk-toolbar" style="display: none;">
    <form id="bulkDeleteForm" action="{{ route('barang.bulkDelete') }}" method="POST">
        @csrf
        @method('DELETE')
        <input type="hidden" name="ids" id="bulkIds" value="">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <div class="form-check mb-0">
                    <input type="checkbox" class="form-check-input" id="selectAllVisible">
                    <label class="form-check-label" for="selectAllVisible" style="font-size: 0.8125rem;">Pilih Semua</label>
                </div>
                <div>
                    <span class="badge bg-danger" id="selectedCount" style="font-size: 0.875rem;">0</span>
                    <span style="font-size: 0.8125rem;">barang dipilih</span>
                </div>
            </div>
            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus barang terpilih?')">
                <i class="fas fa-trash me-2"></i>Hapus Terpilih
            </button>
        </div>
    </form>
</div>
@endif

<!-- Data Table -->
<div class="data-card animate-fade-up animate-delay-5">
    <div class="card-header-custom">
        <h6 class="mb-0 fw-bold"><i class="fas fa-list me-2"></i>Daftar Barang</h6>
        <div class="d-flex gap-2">
            <span class="stat-badge stat-badge-success">
                <i class="fas fa-check"></i> Tersedia
            </span>
            <span class="stat-badge stat-badge-warning">
                <i class="fas fa-exclamation"></i> Stok Rendah
            </span>
            <span class="stat-badge stat-badge-danger">
                <i class="fas fa-times"></i> Habis
            </span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    @if(Auth::user()->isAdmin())
                    <th style="width: 50px;" class="text-center">
                        <input type="checkbox" id="selectAll" class="form-check-input">
                    </th>
                    @endif
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Satuan</th>
                    <th class="text-center">Stok</th>
                    <th class="text-center">Min. Stok</th>
                    <th>Status</th>
                    <th style="width: 120px;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($barangs as $barang)
                    <tr>
                        @if(Auth::user()->isAdmin())
                        <td class="text-center">
                            <input type="checkbox" class="bulk-checkbox form-check-input" value="{{ $barang->id }}">
                        </td>
                        @endif
                        <td>
                            <div class="fw-semibold">{{ $barang->nama_barang }}</div>
                            @if($barang->catatan)
                            <small class="text-muted">{{ Str::limit($barang->catatan, 40) }}</small>
                            @endif
                        </td>
                        <td><span class="badge bg-secondary">{{ $barang->kategori }}</span></td>
                        <td>{{ $barang->satuan }}</td>
                        <td class="text-center">
                            <div class="editable-stok-wrapper" data-barang-id="{{ $barang->id }}" data-stok-lama="{{ $barang->stok }}" data-stok-minimum="{{ $barang->stok_minimum }}" data-nama-barang="{{ $barang->nama_barang }}">
                                <span class="btn btn-link fw-bold stok-display" style="font-size: 1rem; text-decoration: none; cursor: pointer; color: inherit;" title="Klik untuk edit stok">
                                    {{ number_format($barang->stok) }}
                                    <i class="fas fa-pen-square ms-1" style="font-size: 0.7rem; opacity: 0.5;"></i>
                                </span>
                                <div class="stok-edit-form" style="display: none;">
                                    <div class="input-group" style="max-width: 160px; margin: 0 auto;">
                                        <input type="number" class="form-control form-control-sm stok-input" value="{{ $barang->stok }}" min="0" style="text-align: center;">
                                        <button class="btn btn-success btn-sm btn-simpan-stok" type="button" title="Simpan">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-secondary btn-sm btn-batal-stok" type="button" title="Batal">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="stok-loading" style="display: none;">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">{{ number_format($barang->stok_minimum) }}</td>
                        <td>
                            @if($barang->stok <= 0)
                                <span class="status-badge status-out">Stok Habis</span>
                            @elseif($barang->stok <= $barang->stok_minimum)
                                <span class="status-badge status-low">Stok Rendah</span>
                            @else
                                <span class="status-badge status-available">Tersedia</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a href="{{ route('barang.show', $barang->id) }}" class="btn btn-sm btn-info action-btn" title="Lihat">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(Auth::user()->isAdmin())
                                <a href="{{ route('barang.edit', $barang->id) }}" class="btn btn-sm btn-warning action-btn" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('barang.destroy', $barang->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus barang ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger action-btn" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ Auth::user()->isAdmin() ? '8' : '7' }}" class="text-center py-5">
                            <div style="width: 60px; height: 60px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                                <i class="fas fa-inbox text-muted" style="font-size: 1.5rem;"></i>
                            </div>
                            <h6 class="text-muted">Belum ada data barang</h6>
                            <a href="{{ route('barang.create') }}" class="btn btn-primary btn-sm mt-2">
                                <i class="fas fa-plus me-2"></i>Tambah Barang Pertama
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($barangs->hasPages())
    <div class="p-3 bg-light border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="text-muted" style="font-size: 0.8125rem;">
            Menampilkan <strong>{{ $barangs->firstItem() ?? 0 }}</strong> - <strong>{{ $barangs->lastItem() ?? 0 }}</strong> dari <strong>{{ $barangs->total() }}</strong> data
        </div>
        <nav>
            {{ $barangs->links('pagination.slim') }}
        </nav>
    </div>
    @endif
</div>

@endsection

@section('scripts')
<script>
    // Bulk delete functionality - Barang
    const selectAll = document.getElementById('selectAll');
    const selectAllVisible = document.getElementById('selectAllVisible');
    const bulkCheckboxes = document.querySelectorAll('.bulk-checkbox');
    const bulkToolbar = document.getElementById('bulkToolbar');
    const selectedCount = document.getElementById('selectedCount');
    const bulkIds = document.getElementById('bulkIds');
    const bulkDeleteForm = document.getElementById('bulkDeleteForm');
    
    function updateBulkToolbar() {
        const checked = document.querySelectorAll('.bulk-checkbox:checked');
        const count = checked.length;
        
        selectedCount.textContent = count;
        bulkToolbar.style.display = count > 0 ? 'block' : 'none';
        
        // Update select all checkbox state
        if (selectAll) {
            selectAll.checked = count === bulkCheckboxes.length && count > 0;
        }
        if (selectAllVisible) {
            selectAllVisible.checked = count === bulkCheckboxes.length && count > 0;
        }
        
        // Update hidden input with selected IDs
        const ids = Array.from(checked).map(cb => cb.value);
        bulkIds.value = ids.join(',');
    }
    
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            bulkCheckboxes.forEach(cb => cb.checked = this.checked);
            updateBulkToolbar();
        });
    }
    
    if (selectAllVisible) {
        selectAllVisible.addEventListener('change', function() {
            bulkCheckboxes.forEach(cb => cb.checked = this.checked);
            updateBulkToolbar();
        });
    }
    
    bulkCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkToolbar);
    });
    
    // Form submission
    if (bulkDeleteForm) {
        bulkDeleteForm.addEventListener('submit', function(e) {
            const checked = document.querySelectorAll('.bulk-checkbox:checked');
            if (checked.length === 0) {
                e.preventDefault();
                alert('Pilih minimal satu barang untuk dihapus');
                return false;
            }
            return confirm('Yakin hapus ' + checked.length + ' barang terpilih?');
        });
    }
</script>
@endsection

<!-- Inline Stock Edit Styles -->
<style>
.editable-stok-wrapper {
    position: relative;
}

.stok-display:hover {
    background-color: rgba(102, 126, 234, 0.1);
    border-radius: 6px;
    padding: 4px 8px;
}

.stok-display i {
    transition: opacity 0.2s ease;
}

.stok-display:hover i {
    opacity: 1 !important;
}

.stok-loading {
    padding: 8px;
}

@media (max-width: 768px) {
    .editable-stok-wrapper {
        min-width: 100px;
    }
}
</style>

<script>
    // Inline stock editing functionality
    document.addEventListener('DOMContentLoaded', function() {
        const stokWrappers = document.querySelectorAll('.editable-stok-wrapper');
        
        stokWrappers.forEach(wrapper => {
            const barangId = wrapper.dataset.barangId;
            const namaBarang = wrapper.dataset.namaBarang;
            const stokMinimum = parseInt(wrapper.dataset.stokMinimum);
            
            const displayEl = wrapper.querySelector('.stok-display');
            const editFormEl = wrapper.querySelector('.stok-edit-form');
            const loadingEl = wrapper.querySelector('.stok-loading');
            const inputEl = wrapper.querySelector('.stok-input');
            const btnSimpan = wrapper.querySelector('.btn-simpan-stok');
            const btnBatal = wrapper.querySelector('.btn-batal-stok');
            
            let stokLama = parseInt(wrapper.dataset.stokLama);
            
            // Click to edit
            displayEl.addEventListener('click', function() {
                stokLama = parseInt(wrapper.dataset.stokLama); // Refresh current value
                displayEl.style.display = 'none';
                editFormEl.style.display = 'block';
                inputEl.value = stokLama;
                inputEl.focus();
                inputEl.select();
            });
            
            // Cancel edit
            btnBatal.addEventListener('click', function() {
                editFormEl.style.display = 'none';
                displayEl.style.display = 'inline-flex';
                inputEl.value = stokLama;
            });
            
            // Save stock
            btnSimpan.addEventListener('click', function() {
                const stokBaru = parseInt(inputEl.value);
                
                if (isNaN(stokBaru) || stokBaru < 0) {
                    alert('Stok harus berupa angka positif!');
                    return;
                }
                
                if (stokBaru === stokLama) {
                    editFormEl.style.display = 'none';
                    displayEl.style.display = 'inline-flex';
                    return;
                }
                
                const selisih = stokBaru - stokLama;
                const keterangan = prompt(
                    `Edit stok untuk "${namaBarang}":\n\n` +
                    `Stok lama: ${stokLama}\n` +
                    `Stok baru: ${stokBaru}\n` +
                    `Selisih: ${selisih > 0 ? '+' : ''}${selisih}\n\n` +
                    `Masukkan keterangan perubahan (opsional):`,
                    selisih > 0 ? 'Penambahan stok manual' : 'Pengurangan stok manual'
                );
                
                if (keterangan === null) return; // User cancelled
                
                // Show loading
                editFormEl.style.display = 'none';
                loadingEl.style.display = 'block';
                
                // Send AJAX request
                const url = `/barang/${barangId}/update-stok`;
                
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        stok_baru: stokBaru,
                        keterangan: keterangan
                    })
                })
                .then(response => response.json())
                .then(data => {
                    loadingEl.style.display = 'none';
                    
                    if (data.success) {
                        // Update display
                        displayEl.innerHTML = `${stokBaru.toLocaleString('id-ID')} <i class="fas fa-pen-square ms-1" style="font-size: 0.7rem; opacity: 0.5;"></i>`;
                        displayEl.style.display = 'inline-flex';
                        
                        // Update dataset for next edit
                        wrapper.dataset.stokLama = stokBaru;
                        
                        // Update status badge
                        const row = wrapper.closest('tr');
                        let statusCell = row.querySelector('[class*="status-"]');
                        
                        if (statusCell) {
                            statusCell.className = 'status-badge';
                            if (stokBaru <= 0) {
                                statusCell.classList.add('status-out');
                                statusCell.textContent = 'Stok Habis';
                            } else if (stokBaru <= stokMinimum) {
                                statusCell.classList.add('status-low');
                                statusCell.textContent = 'Stok Rendah';
                            } else {
                                statusCell.classList.add('status-available');
                                statusCell.textContent = 'Tersedia';
                            }
                        }
                        
                        // Show success message
                        alert(`${data.message}\n\nStok berhasil diupdate dan transaksi otomatis telah dibuat.\nSilahkan cek di menu Transaksi untuk melihat riwayat perubahan.`);
                        
                    } else {
                        displayEl.style.display = 'inline-flex';
                        alert('Gagal: ' + data.message);
                    }
                })
                .catch(error => {
                    loadingEl.style.display = 'none';
                    displayEl.style.display = 'inline-flex';
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengupdate stok. Silahkan coba lagi.');
                });
            });
            
            // Handle Enter key
            inputEl.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    btnSimpan.click();
                } else if (e.key === 'Escape') {
                    btnBatal.click();
                }
            });
        });
    });
</script>
