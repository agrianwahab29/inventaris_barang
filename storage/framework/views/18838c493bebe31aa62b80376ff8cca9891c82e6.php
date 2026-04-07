<?php $__env->startSection('title', 'Surat Tanda Terima Barang - Aplikasi Inventaris'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
    <li class="breadcrumb-item active">Surat Tanda Terima</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_title', 'Surat Tanda Terima Barang'); ?>

<?php $__env->startSection('styles'); ?>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<style>
    .filter-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        color: white;
    }
    .group-card {
        border-radius: 12px;
        box-shadow: 0 2px 6px -1px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
    }
    .group-card:hover {
        box-shadow: 0 8px 25px -5px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    .group-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-bottom: 1px solid #e2e8f0;
        border-radius: 12px 12px 0 0;
        padding: 14px 18px;
    }
    .item-row {
        padding: 8px 18px;
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.15s ease;
    }
    .item-row:last-child {
        border-bottom: none;
    }
    .item-row:hover {
        background: #f8fafc;
    }
    .btn-generate {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
        color: white;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 6px 16px;
        border-radius: 8px;
        transition: all 0.3s;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
    }
    .btn-generate:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        color: white;
    }
    .badge-item {
        font-size: 0.625rem;
        padding: 3px 8px;
        border-radius: 20px;
        font-weight: 600;
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #94a3b8;
    }
    .empty-state i {
        font-size: 3rem;
        opacity: 0.3;
        margin-bottom: 12px;
    }
    .stat-box {
        border-radius: 10px;
        padding: 14px;
        color: white;
        position: relative;
        overflow: hidden;
    }
    .stat-box::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 100%;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }
    .stat-total { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .stat-items { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .stat-qty { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- Header Section -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-0 fw-bold">Surat Tanda Terima Barang</h5>
        <p class="text-muted mb-0 small">Kelola dan cetak surat tanda terima barang (DOCX)</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('transaksi.create')); ?>" class="btn btn-primary rounded-pill px-3" style="font-size: 0.75rem;">
            <i class="fas fa-plus me-1"></i>Input Barang Keluar
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-2 mb-3">
    <div class="col-md-4">
        <div class="stat-box stat-total">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-0 opacity-75" style="font-size: 0.6875rem;">Total Grup</p>
                    <h4 class="mb-0 fw-bold" style="font-size: 1.25rem;"><?php echo e($grouped->count()); ?></h4>
                </div>
                <i class="fas fa-layer-group opacity-50" style="font-size: 1.25rem;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-box stat-items">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-0 opacity-75" style="font-size: 0.6875rem;">Jenis Barang</p>
                    <h4 class="mb-0 fw-bold" style="font-size: 1.25rem;"><?php echo e($grouped->sum('total_items')); ?></h4>
                </div>
                <i class="fas fa-boxes opacity-50" style="font-size: 1.25rem;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-box stat-qty">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-0 opacity-75" style="font-size: 0.6875rem;">Total Qty Keluar</p>
                    <h4 class="mb-0 fw-bold" style="font-size: 1.25rem;"><?php echo e($grouped->sum('total_qty')); ?></h4>
                </div>
                <i class="fas fa-arrow-up opacity-50" style="font-size: 1.25rem;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card filter-card mb-3 border-0 shadow">
    <div class="card-body py-2 px-3">
        <form method="GET" action="<?php echo e(route('surat-tanda-terima.index')); ?>">
            <div class="row g-2 align-items-end">
                <div class="col-lg-4 col-md-4">
                    <label class="form-label text-white-50" style="font-size: 0.625rem;">Pengambil</label>
                    <select name="pengambil" class="form-select" style="font-size: 0.75rem; padding: 4px 8px;">
                        <option value="">-- Semua Pengambil --</option>
                        <?php $__currentLoopData = $daftarPengambil; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pengambil): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($pengambil); ?>" <?php echo e(request('pengambil') == $pengambil ? 'selected' : ''); ?>>
                                <?php echo e($pengambil); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-lg-4 col-md-4">
                    <label class="form-label text-white-50" style="font-size: 0.625rem;">Tanggal Keluar</label>
                    <select name="tanggal" class="form-select" style="font-size: 0.75rem; padding: 4px 8px;">
                        <option value="">-- Pilih Tanggal --</option>
                        <?php $__currentLoopData = $daftarTanggal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tgl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($tgl['value']); ?>" <?php echo e(request('tanggal') == $tgl['value'] ? 'selected' : ''); ?>>
                                <?php echo e($tgl['label']); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-light flex-fill" style="font-size: 0.75rem; padding: 4px 8px;">
                            <i class="fas fa-filter me-1"></i>Filter
                        </button>
                        <a href="<?php echo e(route('surat-tanda-terima.index')); ?>" class="btn btn-outline-light" style="font-size: 0.75rem; padding: 4px 8px;" title="Reset">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Data Section -->
<?php $__empty_1 = true; $__currentLoopData = $grouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<div class="group-card mb-3 animate-fade-up" style="animation-delay: <?php echo e($index * 0.05); ?>s;">
    <div class="group-header d-flex justify-content-between align-items-center">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="fw-bold" style="font-size: 0.9375rem;"><?php echo e($group['nama_pengambil']); ?></span>
                <?php if($group['ruangan']): ?>
                <span class="badge-item bg-primary text-white"><?php echo e($group['ruangan']->nama_ruangan); ?></span>
                <?php endif; ?>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted" style="font-size: 0.6875rem;">
                    <i class="fas fa-calendar me-1"></i>
                    <?php if($group['tanggal_keluar']): ?>
                        <?php echo e(\Carbon\Carbon::parse($group['tanggal_keluar'])->translatedFormat('d F Y')); ?>

                    <?php else: ?>
                        -
                    <?php endif; ?>
                </span>
                <span class="text-muted" style="font-size: 0.6875rem;">
                    <i class="fas fa-box me-1"></i><?php echo e($group['total_items']); ?> jenis barang
                </span>
                <span class="text-muted" style="font-size: 0.6875rem;">
                    <i class="fas fa-cubes me-1"></i>Total: <?php echo e($group['total_qty']); ?> unit
                </span>
            </div>
        </div>
        <div>
            <form method="GET" action="<?php echo e(route('surat-tanda-terima.generate')); ?>" class="d-inline">
                <input type="hidden" name="nama_pengambil" value="<?php echo e($group['nama_pengambil']); ?>">
                <input type="hidden" name="tanggal_keluar" value="<?php echo e($group['tanggal_keluar'] ? \Carbon\Carbon::parse($group['tanggal_keluar'])->format('Y-m-d') : ''); ?>">
                <button type="submit" class="btn-generate">
                    <i class="fas fa-file-word me-1"></i>Cetak DOCX
                </button>
            </form>
        </div>
    </div>

    <div class="px-2 py-1">
        <?php $__currentLoopData = $group['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="item-row d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted" style="font-size: 0.6875rem; width: 24px;"><?php echo e($loop->iteration); ?></span>
                <span class="fw-medium" style="font-size: 0.8125rem;"><?php echo e($item->barang->nama_barang ?? '-'); ?></span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="fw-bold text-warning" style="font-size: 0.8125rem;"><?php echo e($item->jumlah_keluar); ?></span>
                <span class="text-muted" style="font-size: 0.6875rem;"><?php echo e($item->barang->satuan ?? '-'); ?></span>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<div class="card border-0 shadow">
    <div class="card-body">
        <div class="empty-state">
            <i class="fas fa-file-alt d-block"></i>
            <p class="mb-1 fw-bold" style="font-size: 0.875rem;">Belum Ada Data</p>
            <p class="mb-2" style="font-size: 0.75rem;">Tidak ada transaksi barang keluar yang bisa dicetak menjadi surat.</p>
            <a href="<?php echo e(route('transaksi.create')); ?>" class="btn btn-primary rounded-pill px-3" style="font-size: 0.75rem;">
                <i class="fas fa-plus me-1"></i>Input Barang Keluar
            </a>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\inventaris-barang2\inventaris-kantor\resources\views/surat-tanda-terima/index.blade.php ENDPATH**/ ?>