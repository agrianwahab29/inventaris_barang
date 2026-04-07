@extends('layouts.app')

@section('title', 'Manajemen User - Aplikasi Inventaris')

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
<!-- Single-Admin System Notice -->
<div class="alert alert-info d-flex align-items-center mb-4" role="alert">
    <i class="fas fa-info-circle me-2 fs-5"></i>
    <div>
        <strong>Single-Admin System:</strong> Sistem ini dirancang untuk satu administrator yang menginput data. 
        Pegawai dapat mengakses laporan tanpa login. 
        <span class="text-muted">Status user digunakan untuk mengontrol akses login admin.</span>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-users-cog me-2"></i>Manajemen User</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('users.index', ['status' => 'nonaktif']) }}" class="btn btn-outline-secondary {{ request('status') == 'nonaktif' ? 'active' : '' }}">
            <i class="fas fa-eye-slash me-2"></i>Lihat Nonaktif
        </a>
        <a href="{{ route('users.index') }}" class="btn btn-outline-success {{ !request('status') ? 'active' : '' }}">
            <i class="fas fa-user-check me-2"></i>User Aktif
        </a>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah User
        </a>
    </div>
</div>

<!-- Bulk Delete Toolbar (Sticky Top) -->
<div id="bulkToolbar" class="bulk-toolbar" style="display: none;">
    <form id="bulkDeleteForm" action="{{ route('users.bulkDelete') }}" method="POST">
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
                    <span style="font-size: 0.8125rem;">user dipilih</span>
                </div>
            </div>
            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus user terpilih? Akun sendiri tidak bisa dihapus.')">
                <i class="fas fa-trash me-2"></i>Hapus Terpilih
            </button>
        </div>
    </form>
</div>

<div class="card table-container">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40"><input type="checkbox" class="form-check-input" id="selectAllHeader"></th>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                        <tr>
                            <td>
                                @if($user->id !== Auth::id())
                                    <input type="checkbox" class="form-check-input item-checkbox" value="{{ $user->id }}">
                                @endif
                            </td>
                            <td>{{ $users->firstItem() + $index }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : 'primary' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $user->status == 'aktif' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning btn-action me-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($user->id !== Auth::id())
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
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
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-inbox fa-2x mb-2 text-muted"></i>
                                <p class="mb-0 text-muted">Tidak ada data user</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        {{ $users->links() }}
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
                alert('Pilih minimal satu user untuk dihapus');
                return false;
            }
            return confirm('Yakin hapus ' + checked.length + ' user terpilih? Akun sendiri tidak bisa dihapus.');
        });
    }
</script>
@endsection
