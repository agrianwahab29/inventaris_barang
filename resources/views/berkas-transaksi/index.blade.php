@extends('layouts.app')

@section('title', 'Berkas Transaksi - Arsip Dokumen')

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Berkas Transaksi</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-0">Berkas Transaksi</h1>
            <p class="text-muted small mb-0">Arsip dokumen serah terima barang</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="showDeleteModal()">
                <i class="fas fa-trash-alt me-1"></i> Hapus Massal
            </button>
            <a href="{{ route('berkas-transaksi.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-upload me-1"></i> Upload Berkas
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-2">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-2 rounded me-2">
                            <i class="fas fa-file-pdf text-primary"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 small text-muted">Total Berkas</h6>
                            <h4 class="mb-0 h5">{{ $totalBerkas }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-2">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 p-2 rounded me-2">
                            <i class="fas fa-calendar text-success"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 small text-muted">Bulan Ini</h6>
                            <h4 class="mb-0 h5">{{ $totalBerkasBulanIni }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-2">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 p-2 rounded me-2">
                            <i class="fas fa-hdd text-info"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 small text-muted">Total Size</h6>
                            <h4 class="mb-0 h5">
                                @if($totalSize >= 1073741824)
                                    {{ number_format($totalSize / 1073741824, 2) }} GB
                                @elseif($totalSize >= 1048576)
                                    {{ number_format($totalSize / 1048576, 2) }} MB
                                @elseif($totalSize >= 1024)
                                    {{ number_format($totalSize / 1024, 2) }} KB
                                @else
                                    {{ $totalSize }} bytes
                                @endif
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-2">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 p-2 rounded me-2">
                            <i class="fas fa-user text-warning"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 small text-muted">Uploader</h6>
                            <h4 class="mb-0 h5">{{ $users->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white py-2">
            <h6 class="mb-0"><i class="fas fa-filter me-1"></i> Filter & Pencarian</h6>
        </div>
        <div class="card-body py-2">
            <form method="GET" action="{{ route('berkas-transaksi.index') }}" class="row g-2">
                <div class="col-md-3">
                    <label class="form-label small">Cari</label>
                    <input type="text" name="search" class="form-control form-control-sm" 
                           placeholder="Nomor surat, perihal..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Dari Tanggal</label>
                    <input type="date" name="dari" class="form-control form-control-sm" value="{{ request('dari') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Sampai Tanggal</label>
                    <input type="date" name="sampai" class="form-control form-control-sm" value="{{ request('sampai') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Tahun</label>
                    <select name="tahun_filter" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach($availableYears as $tahun)
                            <option value="{{ $tahun }}" {{ request('tahun_filter') == $tahun ? 'selected' : '' }}>
                                {{ $tahun }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Uploader</label>
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label small">&nbsp;</label>
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="{{ route('berkas-transaksi.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="card border-0 shadow-sm mb-2" id="bulkActionBar" style="display: none;">
        <div class="card-body py-2">
            <div class="d-flex justify-content-between align-items-center">
                <span class="small">
                    <span id="selectedCount">0</span> item dipilih
                </span>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                        <i class="fas fa-check-square me-1"></i> Pilih Semua
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteSelected()">
                        <i class="fas fa-trash me-1"></i> Hapus Terpilih
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="40">
                                <input type="checkbox" class="form-check-input" id="selectAllCheckbox" onclick="toggleSelectAll()">
                            </th>
                            <th>No</th>
                            <th>File</th>
                            <th>Nomor Surat</th>
                            <th>Tanggal Surat</th>
                            <th>Perihal</th>
                            <th>Pengirim / Penerima</th>
                            <th>Uploader</th>
                            <th>Size</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($berkas as $index => $item)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input item-checkbox" 
                                       value="{{ $item->id }}" onchange="updateSelectedCount()">
                            </td>
                            <td>{{ $berkas->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-file-pdf text-danger me-2"></i>
                                    <div>
                                        <div class="small fw-medium">{{ $item->file_name }}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">
                                            {{ $item->created_at->format('d M Y') }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $item->nomor_surat ?? '-' }}</td>
                            <td>{{ $item->tanggal_surat ? $item->tanggal_surat->format('d M Y') : '-' }}</td>
                            <td>{{ Str::limit($item->perihal, 30) ?? '-' }}</td>
                            <td>
                                <small>
                                    {{ $item->pengirim ?? '-' }} <i class="fas fa-arrow-right text-muted mx-1"></i> {{ $item->penerima ?? '-' }}
                                </small>
                            </td>
                            <td>{{ $item->user->name ?? 'Unknown' }}</td>
                            <td>{{ $item->file_size_human }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('berkas-transaksi.download', $item) }}" 
                                       class="btn btn-sm btn-outline-success" title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <a href="{{ route('berkas-transaksi.show', $item) }}" 
                                       class="btn btn-sm btn-outline-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('berkas-transaksi.edit', $item) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            onclick="deleteItem('{{ $item->id }}')" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p class="mb-0">Tidak ada berkas yang diarsipkan</p>
                                    <a href="{{ route('berkas-transaksi.create') }}" class="btn btn-sm btn-primary mt-2">
                                        Upload Berkas Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($berkas->hasPages())
        <div class="card-footer bg-white py-2">
            {{ $berkas->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

<!-- Delete Modal (Single) -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Konfirmasi Hapus</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Apakah Anda yakin ingin menghapus berkas ini?</p>
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Delete Modal -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fas fa-trash-alt me-1"></i> Hapus Massal</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small">Pilih Jenis Penghapusan:</label>
                    <select class="form-select form-select-sm" id="deleteType" onchange="toggleDeleteOptions()">
                        <option value="">-- Pilih --</option>
                        <option value="selected">Hapus yang Dipilih</option>
                        <option value="all">Hapus Semua Berkas</option>
                        <option value="month">Hapus per Bulan</option>
                        <option value="range">Hapus Rentang Bulan</option>
                    </select>
                </div>

                <!-- Delete by Month -->
                <div id="deleteMonthOptions" style="display: none;">
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small">Tahun</label>
                            <select class="form-select form-select-sm" id="deleteTahun">
                                @foreach($availableYears as $tahun)
                                    <option value="{{ $tahun }}">{{ $tahun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Bulan</label>
                            <select class="form-select form-select-sm" id="deleteBulan">
                                @foreach(range(1, 12) as $bulan)
                                    <option value="{{ $bulan }}">{{ DateTime::createFromFormat('!m', $bulan)->format('F') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Delete by Range -->
                <div id="deleteRangeOptions" style="display: none;">
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small">Tahun Dari</label>
                            <select class="form-select form-select-sm" id="deleteTahunDari">
                                @foreach($availableYears as $tahun)
                                    <option value="{{ $tahun }}">{{ $tahun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Bulan Dari</label>
                            <select class="form-select form-select-sm" id="deleteBulanDari">
                                @foreach(range(1, 12) as $bulan)
                                    <option value="{{ $bulan }}">{{ DateTime::createFromFormat('!m', $bulan)->format('F') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small">Tahun Sampai</label>
                            <select class="form-select form-select-sm" id="deleteTahunSampai">
                                @foreach($availableYears as $tahun)
                                    <option value="{{ $tahun }}">{{ $tahun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Bulan Sampai</label>
                            <select class="form-select form-select-sm" id="deleteBulanSampai">
                                @foreach(range(1, 12) as $bulan)
                                    <option value="{{ $bulan }}">{{ DateTime::createFromFormat('!m', $bulan)->format('F') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning mt-3 mb-0 py-2">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    <small>Data yang sudah dihapus tidak dapat dikembalikan.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-sm btn-danger" onclick="executeBulkDelete()">
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
