<?php $__env->startSection('title', 'Manajemen User - Aplikasi Inventaris'); ?>
<?php $__env->startSection('page_title', 'Manajemen User'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active">Manajemen User</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<style>
    /* Stats Cards */
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
    .stat-card.active { --card-color: #10b981; --card-color-light: #34d399; }
    .stat-card.admin { --card-color: #ef4444; --card-color-light: #f87171; }
    .stat-card.user { --card-color: #3b82f6; --card-color-light: #60a5fa; }
    
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

    /* Filter Section */
    .filter-section {
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: 16px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
    }

    /* User Cards */
    .users-container {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .user-card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.25rem;
        padding: 1.25rem;
    }

    .user-grid-card {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.08);
        border-radius: 16px;
        padding: 1.25rem;
        transition: all 0.3s ease;
        position: relative;
        cursor: pointer;
    }

    .user-grid-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.12);
        border-color: rgba(99, 102, 241, 0.3);
    }

    .user-grid-card.selected {
        border: 2px solid #6366f1;
        background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
    }

    .user-grid-card-header {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .user-avatar {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 700;
        color: white;
        flex-shrink: 0;
    }

    .user-avatar.admin {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .user-avatar.pengguna {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }

    .user-info {
        flex: 1;
        min-width: 0;
    }

    .user-name {
        font-size: 1rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.25rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .user-email {
        font-size: 0.8125rem;
        color: #64748b;
        margin-bottom: 0.5rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .user-meta {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .user-badge {
        font-size: 0.6875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        padding: 0.25rem 0.5rem;
        border-radius: 20px;
    }

    .user-badge.admin {
        background: #fee2e2;
        color: #dc2626;
    }

    .user-badge.pengguna {
        background: #dbeafe;
        color: #2563eb;
    }

    .user-badge.aktif {
        background: #d1fae5;
        color: #059669;
    }

    .user-badge.nonaktif {
        background: #e5e7eb;
        color: #6b7280;
    }

    .user-grid-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(0,0,0,0.06);
    }

    .user-date {
        font-size: 0.75rem;
        color: #94a3b8;
    }

    .user-actions {
        display: flex;
        gap: 0.5rem;
    }

    .btn-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }

    .btn-icon.edit {
        background: #fef3c7;
        color: #d97706;
    }

    .btn-icon.edit:hover {
        background: #fcd34d;
    }

    .btn-icon.delete {
        background: #fee2e2;
        color: #dc2626;
    }

    .btn-icon.delete:hover {
        background: #fecaca;
    }

    /* List View */
    .list-view .user-card-grid {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        padding: 0;
    }

    .list-view .user-grid-card {
        display: flex;
        align-items: center;
        padding: 0.875rem 1rem;
        gap: 1rem;
    }

    .list-view .user-grid-card-header {
        display: flex;
        align-items: center;
        margin-bottom: 0;
        flex: 1;
    }

    .list-view .user-grid-card {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .list-view .user-name {
        width: 200px;
        margin-bottom: 0;
    }

    .list-view .user-email {
        width: 200px;
        margin-bottom: 0;
    }

    .list-view .user-grid-card-footer {
        margin-top: 0;
        padding-top: 0;
        border-top: none;
        flex-shrink: 0;
    }

    /* View Toggle */
    .view-toggle {
        display: flex;
        gap: 0.25rem;
        background: #f1f5f9;
        padding: 0.25rem;
        border-radius: 10px;
    }

    .view-toggle button {
        padding: 0.5rem 0.875rem;
        border-radius: 8px;
        border: none;
        background: transparent;
        color: #64748b;
        font-size: 0.8125rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }

    .view-toggle button:hover {
        color: #334155;
    }

    .view-toggle button.active {
        background: #fff;
        color: #6366f1;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    /* Checkbox */
    .user-checkbox {
        position: absolute;
        top: 1rem;
        right: 1rem;
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-state i {
        font-size: 4rem;
        color: #e2e8f0;
        margin-bottom: 1rem;
    }

    /* Bulk Toolbar */
    .bulk-toolbar {
        position: sticky;
        top: 0;
        z-index: 100;
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border-bottom: 1px solid rgba(239, 68, 68, 0.2);
        padding: 1rem 1.25rem;
        margin: 0;
        animation: slideDown 0.3s ease-out;
    }

    @keyframes  slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Responsive - Tablet */
    @media (max-width: 767.98px) {
        .stat-card {
            padding: 1rem;
        }

        .stat-value {
            font-size: 1.5rem;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            font-size: 1.25rem;
        }

        .filter-section {
            padding: 0.75rem;
        }

        .user-card-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
            padding: 1rem;
        }

        .list-view .user-grid-card {
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .list-view .user-grid-card-header {
            flex: 1 1 100%;
        }

        .list-view .user-name,
        .list-view .user-email {
            width: auto;
        }

        .list-view .user-grid-card-footer {
            flex: 0 0 auto;
        }
    }

    /* Responsive - Small Mobile */
    @media (max-width: 575.98px) {
        .stat-card {
            padding: 0.75rem;
        }

        .stat-value {
            font-size: 1.25rem;
        }

        .stat-icon {
            width: 36px;
            height: 36px;
            font-size: 1.125rem;
            border-radius: 10px;
        }

        .stat-label {
            font-size: 0.75rem;
        }

        .filter-section {
            padding: 0.625rem;
            border-radius: 12px;
        }

        .user-card-grid {
            grid-template-columns: 1fr;
            gap: 0.75rem;
            padding: 0.75rem;
        }

        .users-container {
            border-radius: 12px;
        }

        .user-grid-card {
            padding: 1rem;
        }

        .user-avatar {
            width: 44px;
            height: 44px;
            font-size: 1.25rem;
        }

        .btn-icon {
            width: 30px;
            height: 30px;
            font-size: 0.8125rem;
        }

        .empty-state {
            padding: 3rem 1.5rem;
        }

        .empty-state i {
            font-size: 3rem;
        }

        .bulk-toolbar {
            padding: 0.75rem;
        }
    }

    /* Responsive - Very Small Mobile */
    @media (max-width: 374.98px) {
        .user-card-grid {
            grid-template-columns: 1fr;
            gap: 0.5rem;
            padding: 0.5rem;
        }

        .stat-card {
            padding: 0.625rem;
        }

        .stat-value {
            font-size: 1.125rem;
        }

        .stat-icon {
            width: 32px;
            height: 32px;
            font-size: 1rem;
        }

        .user-grid-card {
            padding: 0.875rem;
        }

        .user-grid-card-header {
            gap: 0.75rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            font-size: 1.125rem;
        }

        .view-toggle button {
            padding: 0.375rem 0.625rem;
            font-size: 0.75rem;
        }
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3 col-md-6">
        <div class="stat-card total">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-value"><?php echo e($stats['total']); ?></div>
                    <div class="stat-label">Total User</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3 col-md-6">
        <div class="stat-card active">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-value"><?php echo e($stats['aktif']); ?></div>
                    <div class="stat-label">User Aktif</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3 col-md-6">
        <div class="stat-card admin">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-value"><?php echo e($stats['admin']); ?></div>
                    <div class="stat-label">Administrator</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3 col-md-6">
        <div class="stat-card user">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-value"><?php echo e($stats['pengguna']); ?></div>
                    <div class="stat-label">User Biasa</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-user"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter & Actions -->
<div class="filter-section">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="view-toggle">
                <button type="button" id="viewCard" class="active">
                    <i class="fas fa-th-large"></i>
                    Kartu
                </button>
                <button type="button" id="viewList">
                    <i class="fas fa-list"></i>
                    Daftar
                </button>
            </div>
            <div class="vr d-none d-md-block" style="height: 32px; background: #e2e8f0;"></div>
            <div class="d-flex gap-2">
                <a href="<?php echo e(route('users.index', ['status' => 'all'])); ?>" class="btn btn-sm <?php echo e(request('status') === 'all' ? 'btn-primary' : 'btn-outline-primary'); ?>">
                    <i class="fas fa-users me-1"></i>Semua
                </a>
                <a href="<?php echo e(route('users.index')); ?>" class="btn btn-sm <?php echo e(!request('status') || request('status') === 'aktif' ? 'btn-success' : 'btn-outline-success'); ?>">
                    <i class="fas fa-check-circle me-1"></i>Aktif
                </a>
                <a href="<?php echo e(route('users.index', ['status' => 'nonaktif'])); ?>" class="btn btn-sm <?php echo e(request('status') == 'nonaktif' ? 'btn-secondary' : 'btn-outline-secondary'); ?>">
                    <i class="fas fa-ban me-1"></i>Nonaktif
                </a>
            </div>
        </div>
        <a href="<?php echo e(route('users.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah User
        </a>
    </div>
</div>

<!-- Bulk Delete Toolbar (Sticky Top) -->
<div id="bulkToolbar" class="bulk-toolbar" style="display: none;">
    <form id="bulkDeleteForm" action="<?php echo e(route('users.bulkDelete')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('DELETE'); ?>
        <input type="hidden" name="ids" id="bulkIds" value="">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="form-check mb-0">
                    <input type="checkbox" class="form-check-input" id="selectAllVisible">
                    <label class="form-check-label" for="selectAllVisible" style="font-size: 0.8125rem; color: #7f1d1d;">Pilih Semua di Halaman Ini</label>
                </div>
                <div>
                    <span class="badge bg-white text-danger" id="selectedCount" style="font-size: 0.875rem; border: 1px solid #fecaca;">0</span>
                    <span style="font-size: 0.8125rem; color: #7f1d1d;">user dipilih</span>
                </div>
            </div>
            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus user terpilih? Akun sendiri tidak bisa dihapus.')">
                <i class="fas fa-trash me-2"></i>Hapus Terpilih
            </button>
        </div>
    </form>
</div>

<!-- User Cards Container -->
<div class="users-container" id="usersContainer">
    <?php if($users->count() > 0): ?>
        <div class="user-card-grid" id="userCardGrid">
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="user-grid-card" data-user-id="<?php echo e($user->id); ?>">
                    <?php if($user->id !== Auth::id()): ?>
                        <input type="checkbox" class="form-check-input user-checkbox item-checkbox" value="<?php echo e($user->id); ?>" data-checkbox="user-<?php echo e($user->id); ?>">
                    <?php endif; ?>
                    
                    <div class="user-grid-card-header">
                        <div class="user-avatar <?php echo e($user->role); ?>">
                            <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                        </div>
                        <div class="user-info">
                            <div class="user-name"><?php echo e($user->name); ?></div>
                            <div class="user-email"><?php echo e($user->email); ?></div>
                            <div class="user-meta">
                                <span class="user-badge <?php echo e($user->role); ?>"><?php echo e(ucfirst($user->role)); ?></span>
                                <span class="user-badge <?php echo e($user->status); ?>"><?php echo e(ucfirst($user->status)); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="user-grid-card-footer">
                        <div class="user-date">
                            <i class="far fa-calendar-alt me-1"></i><?php echo e($user->created_at->format('d M Y')); ?>

                        </div>
                        <div class="user-actions">
                            <a href="<?php echo e(route('users.edit', $user)); ?>" class="btn-icon edit" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if($user->id !== Auth::id()): ?>
                                <form action="<?php echo e(route('users.destroy', $user)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn-icon delete" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-users-slash"></i>
            <h5 class="text-muted mb-2">Tidak ada data user</h5>
            <p class="text-muted mb-3">Belum ada user yang terdaftar dalam sistem.</p>
            <a href="<?php echo e(route('users.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah User Pertama
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Pagination -->
<?php if($users->hasPages()): ?>
    <div class="d-flex justify-content-center mt-4">
        <?php echo e($users->links()); ?>

    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    // View Toggle
    const viewCard = document.getElementById('viewCard');
    const viewList = document.getElementById('viewList');
    const usersContainer = document.getElementById('usersContainer');
    
    viewCard.addEventListener('click', function() {
        usersContainer.classList.remove('list-view');
        viewCard.classList.add('active');
        viewList.classList.remove('active');
        localStorage.setItem('usersView', 'card');
    });
    
    viewList.addEventListener('click', function() {
        usersContainer.classList.add('list-view');
        viewList.classList.add('active');
        viewCard.classList.remove('active');
        localStorage.setItem('usersView', 'list');
    });
    
    // Restore view preference
    const savedView = localStorage.getItem('usersView') || 'card';
    if (savedView === 'list') {
        viewList.click();
    }
    
    // Bulk Selection
    const selectAllVisible = document.getElementById('selectAllVisible');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const bulkToolbar = document.getElementById('bulkToolbar');
    const selectedCount = document.getElementById('selectedCount');
    const bulkDeleteForm = document.getElementById('bulkDeleteForm');
    const bulkIds = document.getElementById('bulkIds');
    const userCards = document.querySelectorAll('.user-grid-card');

    function updateBulkToolbar() {
        const checked = document.querySelectorAll('.item-checkbox:checked');
        const count = checked.length;
        
        if (selectedCount) selectedCount.textContent = count;
        if (bulkToolbar) bulkToolbar.style.display = count > 0 ? 'block' : 'none';
        
        // Update select all checkbox
        if (selectAllVisible) {
            const totalCheckable = itemCheckboxes.length;
            selectAllVisible.checked = count === totalCheckable && count > 0;
            selectAllVisible.indeterminate = count > 0 && count < totalCheckable;
        }
        
        // Update hidden input
        if (bulkIds) {
            const ids = Array.from(checked).map(cb => cb.value);
            bulkIds.value = ids.join(',');
        }
        
        // Update card selection styling
        userCards.forEach(card => {
            const checkbox = card.querySelector('.item-checkbox');
            if (checkbox && checkbox.checked) {
                card.classList.add('selected');
            } else {
                card.classList.remove('selected');
            }
        });
    }

    // Click card to toggle checkbox
    userCards.forEach(card => {
        card.addEventListener('click', function(e) {
            // Don't toggle if clicking on buttons or links
            if (e.target.closest('.btn-icon') || e.target.closest('form') || e.target.closest('.user-checkbox')) {
                return;
            }
            
            const checkbox = this.querySelector('.item-checkbox');
            if (checkbox) {
                checkbox.checked = !checkbox.checked;
                updateBulkToolbar();
            }
        });
    });

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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\inventaris-barang2\inventaris-kantor\resources\views/users/index.blade.php ENDPATH**/ ?>