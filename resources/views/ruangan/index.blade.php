@extends('layouts.app')

@section('title', 'Data Ruangan - Aplikasi Inventaris')

@section('styles')
<style>
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
    
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-door-open me-2"></i>Data Ruangan</h4>
    @if(Auth::user()->isAdmin())
    <a href="{{ route('ruangan.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Tambah Ruangan
    </a>
    @endif
</div>

<!-- Bulk Delete Toolbar (Sticky Top) -->
@if(Auth::user()->isAdmin())
<div id="bulkToolbar" class="bulk-toolbar" style="display: none;">
    <form id="bulkDeleteForm" action="{{ route('ruangan.bulkDelete') }}" method="POST">
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
                    <span style="font-size: 0.8125rem;">ruangan dipilih</span>
                </div>
            </div>
            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus ruangan terpilih? Ruangan dengan riwayat transaksi akan dilewati.')">
                <i class="fas fa-trash me-2"></i>Hapus Terpilih
            </button>
        </div>
    </form>
</div>
@endif

<!-- Table -->
<div class="card table-container">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        @if(Auth::user()->isAdmin())
                            <th width="40"><input type="checkbox" class="form-check-input" id="selectAllHeader"></th>
                        @endif
                        <th>No</th>
                        <th>Nama Ruangan</th>
                        <th>Keterangan</th>
                        <th>Jumlah Transaksi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ruangans as $index => $ruangan)
                        <tr>
                            @if(Auth::user()->isAdmin())
                                <td><input type="checkbox" class="form-check-input item-checkbox" value="{{ $ruangan->id }}"></td>
                            @endif
                            <td>{{ $ruangans->firstItem() + $index }}</td>
                            <td>{{ $ruangan->nama_ruangan }}</td>
                            <td>{{ $ruangan->keterangan ?: '-' }}</td>
                            <td>
                                <span class="badge bg-info">{{ $ruangan->transaksis()->count() }} transaksi</span>
                            </td>
                            <td>
                                <a href="{{ route('ruangan.show', $ruangan) }}" class="btn btn-sm btn-info btn-action me-1">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(Auth::user()->isAdmin())
                                <a href="{{ route('ruangan.edit', $ruangan) }}" class="btn btn-sm btn-warning btn-action me-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('ruangan.destroy', $ruangan) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus ruangan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger btn-action">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ Auth::user()->isAdmin() ? 6 : 5 }}" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p class="mb-0">Tidak ada data ruangan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div class="pagination-info" style="font-size: 0.75rem;">
                Menampilkan {{ $ruangans->firstItem() }} - {{ $ruangans->lastItem() }} dari {{ $ruangans->total() }} ruangan
            </div>
            <nav>
                {{ $ruangans->appends(request()->all())->links('pagination.slim') }}
            </nav>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const selectAllHeader = document.getElementById('selectAllHeader');
    const selectAllVisible = document.getElementById('selectAllVisible');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const bulkToolbar = document.getElementById('bulkToolbar');
    const selectedCount = document.getElementById('selectedCount');
    const bulkDeleteForm = document.getElementById('bulkDeleteForm');
    const bulkIds = document.getElementById('bulkIds');

    function updateBulkToolbar() {
        const checked = document.querySelectorAll('.item-checkbox:checked');
        const count = checked.length;
        
        if (selectedCount) selectedCount.textContent = count;
        if (bulkToolbar) bulkToolbar.style.display = count > 0 ? 'block' : 'none';
        
        // Update select all checkboxes state
        if (selectAllHeader) {
            selectAllHeader.checked = count === itemCheckboxes.length && count > 0;
        }
        if (selectAllVisible) {
            selectAllVisible.checked = count === itemCheckboxes.length && count > 0;
        }
        
        // Update hidden input with selected IDs
        if (bulkIds) {
            const ids = Array.from(checked).map(cb => cb.value);
            bulkIds.value = ids.join(',');
        }
    }

    if (selectAllHeader) {
        selectAllHeader.addEventListener('change', function() {
            itemCheckboxes.forEach(cb => cb.checked = this.checked);
            updateBulkToolbar();
        });
    }
    
    if (selectAllVisible) {
        selectAllVisible.addEventListener('change', function() {
            itemCheckboxes.forEach(cb => cb.checked = this.checked);
            updateBulkToolbar();
        });
    }

    itemCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkToolbar);
    });
    
    // Form submission
    if (bulkDeleteForm) {
        bulkDeleteForm.addEventListener('submit', function(e) {
            const checked = document.querySelectorAll('.item-checkbox:checked');
            if (checked.length === 0) {
                e.preventDefault();
                alert('Pilih minimal satu ruangan untuk dihapus');
                return false;
            }
            return confirm('Yakin hapus ' + checked.length + ' ruangan terpilih? Ruangan dengan riwayat transaksi akan dilewati.');
        });
    }
</script>
@endsection
