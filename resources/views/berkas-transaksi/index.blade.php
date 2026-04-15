@extends('layouts.app')

@section('title', 'Berkas Transaksi - Arsip Dokumen')

@section('styles')
<style>
    /* Stats Cards - Modern Gradient Design */
    .stat-card {
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        border: 1px solid rgba(0,0,0,0.08);
        border-radius: 16px;
        padding: 1.25rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--card-color) 0%, var(--card-color-light) 100%);
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    
    .stat-card.total { --card-color: #6366f1; --card-color-light: #818cf8; }
    .stat-card.month { --card-color: #10b981; --card-color-light: #34d399; }
    .stat-card.size { --card-color: #0ea5e9; --card-color-light: #38bdf8; }
    .stat-card.users { --card-color: #f59e0b; --card-color-light: #fbbf24; }
    
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        background: linear-gradient(135deg, var(--card-color) 0%, var(--card-color-light) 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }
    
    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.2;
    }
    
    .stat-label {
        font-size: 0.8125rem;
        color: #64748b;
        font-weight: 500;
    }
    
    /* Filter Card */
    .filter-card {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: 16px;
        overflow: hidden;
    }
    
    .filter-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(0,0,0,0.06);
    }
    
    .filter-header h6 {
        font-weight: 600;
        color: #334155;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .filter-header i {
        color: #6366f1;
    }
    
    /* Table Styling */
    .table-container {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: 16px;
        overflow: hidden;
    }
    
    .table-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(0,0,0,0.06);
    }
    
    .custom-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .custom-table thead th {
        background: #f8fafc;
        color: #475569;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.875rem 1rem;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }
    
    .custom-table tbody tr {
        transition: all 0.2s ease;
    }
    
    .custom-table tbody tr:hover {
        background: #f8fafc;
    }
    
    .custom-table tbody td {
        padding: 1rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    
    /* File Cell Styling */
    .file-cell {
        display: flex;
        align-items: center;
        gap: 0.875rem;
    }
    
    .file-icon {
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: #dc2626;
        flex-shrink: 0;
    }
    
    .file-info {
        min-width: 0;
        flex: 1;
    }
    
    .file-name {
        font-weight: 500;
        color: #1e293b;
        font-size: 0.875rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 250px;
    }
    
    .file-date {
        font-size: 0.75rem;
        color: #64748b;
    }
    
    /* Action Dropdown */
    .action-dropdown .btn {
        width: 36px;
        height: 36px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #64748b;
        transition: all 0.2s ease;
    }
    
    .action-dropdown .btn:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #334155;
    }
    
    .action-dropdown .dropdown-menu {
        border: 1px solid rgba(0,0,0,0.08);
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.12);
        padding: 0.5rem;
        min-width: 160px;
    }
    
    .action-dropdown .dropdown-item {
        border-radius: 8px;
        padding: 0.625rem 0.875rem;
        font-size: 0.875rem;
        color: #334155;
        display: flex;
        align-items: center;
        gap: 0.625rem;
        transition: all 0.2s ease;
    }
    
    .action-dropdown .dropdown-item:hover {
        background: #f8fafc;
    }
    
    .action-dropdown .dropdown-item i {
        font-size: 0.875rem;
        width: 16px;
        text-align: center;
    }
    
    .action-dropdown .dropdown-item.text-danger:hover {
        background: #fef2f2;
    }
    
    /* Badge Styling */
    .badge-soft {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .badge-soft-primary {
        background: #e0e7ff;
        color: #4338ca;
    }
    
    .badge-soft-success {
        background: #d1fae5;
        color: #059669;
    }
    
    /* Empty State */
    .empty-state {
        padding: 3rem 2rem;
        text-align: center;
    }
    
    .empty-state-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.25rem;
        font-size: 2rem;
        color: #94a3b8;
    }
    
    /* Bulk Action Bar */
    .bulk-action-bar {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border: 1px solid #fcd34d;
        border-radius: 12px;
        padding: 0.875rem 1.25rem;
    }
    
    /* Header Buttons */
    .btn-header {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        border-radius: 10px;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }
    
    .btn-header-primary {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: white;
        border: none;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
    }
    
    .btn-header-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
        color: white;
    }
    
    .btn-header-outline {
        background: #fff;
        color: #dc2626;
        border: 1px solid #fecaca;
    }
    
    .btn-header-outline:hover {
        background: #fef2f2;
        border-color: #fca5a5;
    }
    
    /* Pagination */
    .pagination-wrapper {
        padding: 1rem 1.25rem;
        border-top: 1px solid rgba(0,0,0,0.06);
    }
    
    /* Text Truncation */
    .text-truncate-custom {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 200px;
    }
    
    /* Sender Receiver Cell */
    .sender-receiver {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8125rem;
    }
    
    .sender-receiver .arrow {
        color: #cbd5e1;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .stat-card {
            margin-bottom: 1rem;
        }
        
        .file-name {
            max-width: 150px;
        }
        
        .custom-table thead th,
        .custom-table tbody td {
            padding: 0.75rem 0.5rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-2">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Berkas Transaksi</li>
                </ol>
            </nav>
            <h1 class="h3 mb-1 fw-bold">Berkas Transaksi</h1>
            <p class="text-muted mb-0">Arsip dokumen serah terima barang</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn-header btn-header-outline" onclick="showDeleteModal()">
                <i class="fas fa-trash-alt"></i>
                <span>Hapus Massal</span>
            </button>
            <a href="{{ route('berkas-transaksi.create') }}" class="btn-header btn-header-primary text-decoration-none">
                <i class="fas fa-cloud-upload-alt"></i>
                <span>Upload Berkas</span>
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card total h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div>
                        <div class="stat-label mb-1">Total Berkas</div>
                        <div class="stat-value">{{ $totalBerkas }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card month h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div>
                        <div class="stat-label mb-1">Bulan Ini</div>
                        <div class="stat-value">{{ $totalBerkasBulanIni }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card size h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon">
                        <i class="fas fa-database"></i>
                    </div>
                    <div>
                        <div class="stat-label mb-1">Total Size</div>
                        <div class="stat-value">
                            @if($totalSize >= 1073741824)
                                {{ number_format($totalSize / 1073741824, 2) }} <small class="fs-6">GB</small>
                            @elseif($totalSize >= 1048576)
                                {{ number_format($totalSize / 1048576, 2) }} <small class="fs-6">MB</small>
                            @elseif($totalSize >= 1024)
                                {{ number_format($totalSize / 1024, 2) }} <small class="fs-6">KB</small>
                            @else
                                {{ $totalSize }} <small class="fs-6">bytes</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card users h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <div class="stat-label mb-1">Uploader</div>
                        <div class="stat-value">{{ $users->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="filter-card mb-4">
        <div class="filter-header">
            <h6><i class="fas fa-sliders-h"></i> Filter & Pencarian</h6>
        </div>
        <div class="card-body p-3">
            <form method="GET" action="{{ route('berkas-transaksi.index') }}">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label small text-muted">Cari</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" 
                                   placeholder="Nomor surat, perihal..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label small text-muted">Dari Tanggal</label>
                        <input type="date" name="dari" class="form-control" value="{{ request('dari') }}">
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label small text-muted">Sampai Tanggal</label>
                        <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}">
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label small text-muted">Tahun</label>
                        <select name="tahun_filter" class="form-select">
                            <option value="">Semua</option>
                            @foreach($availableYears as $tahun)
                                <option value="{{ $tahun }}" {{ request('tahun_filter') == $tahun ? 'selected' : '' }}>
                                    {{ $tahun }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label small text-muted">Uploader</label>
                        <select name="user_id" class="form-select">
                            <option value="">Semua</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-1 col-md-12">
                        <label class="form-label small text-muted d-none d-lg-block">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route('berkas-transaksi.index') }}" class="btn btn-secondary" title="Reset">
                                <i class="fas fa-undo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="bulk-action-bar mb-4" id="bulkActionBar" style="display: none;">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <span class="badge-soft badge-soft-primary">
                    <i class="fas fa-check-square"></i>
                    <span id="selectedCount">0</span> item dipilih
                </span>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-light" onclick="selectAll()">
                    <i class="fas fa-check-square me-1"></i> Pilih Semua
                </button>
                <button type="button" class="btn btn-sm btn-danger" onclick="deleteSelected()">
                    <i class="fas fa-trash me-1"></i> Hapus Terpilih
                </button>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-container">
        <div class="table-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-secondary">Daftar Berkas</h6>
                @if($berkas->hasPages())
                    <small class="text-muted">{{ $berkas->firstItem() }} - {{ $berkas->lastItem() }} dari {{ $berkas->total() }}</small>
                @endif
            </div>
        </div>
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th width="50">
                            <input type="checkbox" class="form-check-input" id="selectAllCheckbox" onclick="toggleSelectAll()">
                        </th>
                        <th width="60">No</th>
                        <th>File</th>
                        <th width="150">Nomor Surat</th>
                        <th width="120">Tanggal Surat</th>
                        <th width="200">Perihal</th>
                        <th width="180">Pengirim / Penerima</th>
                        <th width="120">Uploader</th>
                        <th width="80">Size</th>
                        <th width="70">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($berkas as $index => $item)
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input item-checkbox" 
                                   value="{{ $item->id }}" onchange="updateSelectedCount()">
                        </td>
                        <td>
                            <span class="text-muted">{{ $berkas->firstItem() + $index }}</span>
                        </td>
                        <td>
                            <div class="file-cell">
                                <div class="file-icon">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <div class="file-info">
                                    <div class="file-name" title="{{ $item->file_name }}">
                                        {{ $item->file_name }}
                                    </div>
                                    <div class="file-date">
                                        <i class="far fa-clock me-1"></i>{{ $item->created_at->format('d M Y') }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge-soft badge-soft-primary">
                                {{ $item->nomor_surat ?? '-' }}
                            </span>
                        </td>
                        <td>
                            @if($item->tanggal_surat)
                                <span class="text-muted">{{ $item->tanggal_surat->format('d M Y') }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-truncate-custom d-inline-block" title="{{ $item->perihal }}">
                                {{ $item->perihal ?? '-' }}
                            </span>
                        </td>
                        <td>
                            <div class="sender-receiver">
                                <span class="text-dark">{{ $item->pengirim ?? '-' }}</span>
                                <i class="fas fa-arrow-right arrow"></i>
                                <span class="text-dark">{{ $item->penerima ?? '-' }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 28px; height: 28px; font-size: 0.75rem; font-weight: 600;">
                                    {{ substr($item->user->name ?? 'U', 0, 1) }}
                                </div>
                                <span class="small">{{ $item->user->name ?? 'Unknown' }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="text-muted small">{{ $item->file_size_human }}</span>
                        </td>
                        <td>
                            <div class="action-dropdown dropdown">
                                <button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('berkas-transaksi.show', $item) }}">
                                            <i class="fas fa-eye text-info"></i> Detail
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('berkas-transaksi.download', $item) }}">
                                            <i class="fas fa-download text-success"></i> Download
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('berkas-transaksi.edit', $item) }}">
                                            <i class="fas fa-edit text-primary"></i> Edit
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <button class="dropdown-item text-danger" onclick="deleteItem('{{ $item->id }}')">
                                            <i class="fas fa-trash-alt"></i> Hapus
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-folder-open"></i>
                                </div>
                                <h5 class="text-muted mb-2">Tidak ada berkas</h5>
                                <p class="text-muted mb-3">Belum ada dokumen yang diarsipkan</p>
                                <a href="{{ route('berkas-transaksi.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Upload Berkas Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($berkas->hasPages())
        <div class="pagination-wrapper">
            {{ $berkas->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

<!-- Delete Modal (Single) -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Konfirmasi Hapus</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-exclamation-circle text-warning" style="font-size: 3rem;"></i>
                </div>
                <p class="mb-0 text-muted">Apakah Anda yakin ingin menghapus berkas ini?</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Delete Modal -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold"><i class="fas fa-trash-alt me-2 text-danger"></i>Hapus Massal</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small text-muted">Pilih Jenis Penghapusan:</label>
                    <select class="form-select" id="deleteType" onchange="toggleDeleteOptions()">
                        <option value="">-- Pilih --</option>
                        <option value="selected">Hapus yang Dipilih</option>
                        <option value="all">Hapus Semua Berkas</option>
                        <option value="month">Hapus per Bulan</option>
                        <option value="range">Hapus Rentang Bulan</option>
                    </select>
                </div>

                <!-- Delete by Month -->
                <div id="deleteMonthOptions" style="display: none;">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label small text-muted">Tahun</label>
                            <select class="form-select" id="deleteTahun">
                                @foreach($availableYears as $tahun)
                                    <option value="{{ $tahun }}">{{ $tahun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted">Bulan</label>
                            <select class="form-select" id="deleteBulan">
                                @foreach(range(1, 12) as $bulan)
                                    <option value="{{ $bulan }}">{{ DateTime::createFromFormat('!m', $bulan)->format('F') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Delete by Range -->
                <div id="deleteRangeOptions" style="display: none;">
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small text-muted">Tahun Dari</label>
                            <select class="form-select" id="deleteTahunDari">
                                @foreach($availableYears as $tahun)
                                    <option value="{{ $tahun }}">{{ $tahun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted">Bulan Dari</label>
                            <select class="form-select" id="deleteBulanDari">
                                @foreach(range(1, 12) as $bulan)
                                    <option value="{{ $bulan }}">{{ DateTime::createFromFormat('!m', $bulan)->format('F') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label small text-muted">Tahun Sampai</label>
                            <select class="form-select" id="deleteTahunSampai">
                                @foreach($availableYears as $tahun)
                                    <option value="{{ $tahun }}">{{ $tahun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted">Bulan Sampai</label>
                            <select class="form-select" id="deleteBulanSampai">
                                @foreach(range(1, 12) as $bulan)
                                    <option value="{{ $bulan }}">{{ DateTime::createFromFormat('!m', $bulan)->format('F') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning mt-3 mb-0 d-flex align-items-center gap-2" style="border-radius: 10px;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <small>Data yang sudah dihapus tidak dapat dikembalikan.</small>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="executeBulkDelete()">
                    <i class="fas fa-trash me-1"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let selectedIds = [];

function updateSelectedCount() {
    selectedIds = [];
    document.querySelectorAll('.item-checkbox:checked').forEach(cb => {
        selectedIds.push(cb.value);
    });
    
    const count = selectedIds.length;
    document.getElementById('selectedCount').textContent = count;
    
    const bulkBar = document.getElementById('bulkActionBar');
    if (count > 0) {
        bulkBar.style.display = 'block';
    } else {
        bulkBar.style.display = 'none';
    }
}

function toggleSelectAll() {
    const masterCheckbox = document.getElementById('selectAllCheckbox');
    const checkboxes = document.querySelectorAll('.item-checkbox');
    
    checkboxes.forEach(cb => {
        cb.checked = masterCheckbox.checked;
    });
    
    updateSelectedCount();
}

function selectAll() {
    document.getElementById('selectAllCheckbox').checked = true;
    toggleSelectAll();
}

function deleteItem(id) {
    const form = document.getElementById('deleteForm');
    form.action = '{{ route("berkas-transaksi.destroy", "") }}/' + id;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function deleteSelected() {
    if (selectedIds.length === 0) {
        alert('Pilih minimal 1 item untuk dihapus');
        return;
    }
    
    if (!confirm('Yakin ingin menghapus ' + selectedIds.length + ' berkas terpilih?')) {
        return;
    }
    
    fetch('{{ route("berkas-transaksi.bulk-delete") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ ids: selectedIds })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Terjadi kesalahan: ' + error);
    });
}

function showDeleteModal() {
    const modal = new bootstrap.Modal(document.getElementById('bulkDeleteModal'));
    modal.show();
}

function toggleDeleteOptions() {
    const type = document.getElementById('deleteType').value;
    
    document.getElementById('deleteMonthOptions').style.display = 'none';
    document.getElementById('deleteRangeOptions').style.display = 'none';
    
    if (type === 'month') {
        document.getElementById('deleteMonthOptions').style.display = 'block';
    } else if (type === 'range') {
        document.getElementById('deleteRangeOptions').style.display = 'block';
    }
}

function executeBulkDelete() {
    const type = document.getElementById('deleteType').value;
    
    if (!type) {
        alert('Pilih jenis penghapusan');
        return;
    }
    
    let url = '';
    let body = {};
    
    switch(type) {
        case 'selected':
            if (selectedIds.length === 0) {
                alert('Pilih minimal 1 item terlebih dahulu');
                return;
            }
            url = '{{ route("berkas-transaksi.bulk-delete") }}';
            body = { ids: selectedIds };
            break;
            
        case 'all':
            if (!confirm('Yakin ingin menghapus SEMUA berkas? Tindakan ini tidak dapat dibatalkan.')) {
                return;
            }
            url = '{{ route("berkas-transaksi.delete-all") }}';
            break;
            
        case 'month':
            url = '{{ route("berkas-transaksi.delete-by-month") }}';
            body = {
                tahun: document.getElementById('deleteTahun').value,
                bulan: document.getElementById('deleteBulan').value
            };
            break;
            
        case 'range':
            url = '{{ route("berkas-transaksi.delete-by-range") }}';
            body = {
                tahun_dari: document.getElementById('deleteTahunDari').value,
                bulan_dari: document.getElementById('deleteBulanDari').value,
                tahun_sampai: document.getElementById('deleteTahunSampai').value,
                bulan_sampai: document.getElementById('deleteBulanSampai').value
            };
            break;
    }
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: Object.keys(body).length > 0 ? JSON.stringify(body) : null
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Terjadi kesalahan: ' + error);
    });
}
</script>
@endsection
