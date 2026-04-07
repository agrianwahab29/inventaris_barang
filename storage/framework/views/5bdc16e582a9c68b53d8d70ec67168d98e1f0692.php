

<?php $__env->startSection('title', 'Manajemen User - Aplikasi Inventaris'); ?>

<?php $__env->startSection('styles'); ?>
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
    
    @keyframes  slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-users-cog me-2"></i>Manajemen User</h4>
    <a href="<?php echo e(route('users.create')); ?>" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Tambah User
    </a>
</div>

<!-- Bulk Delete Toolbar (Sticky Top) -->
<div id="bulkToolbar" class="bulk-toolbar" style="display: none;">
    <form id="bulkDeleteForm" action="<?php echo e(route('users.bulkDelete')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('DELETE'); ?>
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
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <?php if($user->id !== Auth::id()): ?>
                                    <input type="checkbox" class="form-check-input item-checkbox" value="<?php echo e($user->id); ?>">
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($users->firstItem() + $index); ?></td>
                            <td><?php echo e($user->name); ?></td>
                            <td><?php echo e($user->username); ?></td>
                            <td><?php echo e($user->email); ?></td>
                            <td>
                                <span class="badge bg-<?php echo e($user->role == 'admin' ? 'danger' : 'primary'); ?>">
                                    <?php echo e(ucfirst($user->role)); ?>

                                </span>
                            </td>
                            <td><?php echo e($user->created_at->format('d M Y')); ?></td>
                            <td>
                                <a href="<?php echo e(route('users.edit', $user)); ?>" class="btn btn-sm btn-warning btn-action me-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if($user->id !== Auth::id()): ?>
                                    <form action="<?php echo e(route('users.destroy', $user)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-danger btn-action">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-inbox fa-2x mb-2 text-muted"></i>
                                <p class="mb-0 text-muted">Tidak ada data user</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        <?php echo e($users->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\inventaris-barang2\inventaris-kantor\resources\views/users/index.blade.php ENDPATH**/ ?>