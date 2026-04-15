

<?php $__env->startSection('title', 'Riwayat Transaksi - Aplikasi Inventaris'); ?>
<?php $__env->startSection('page_title', 'Riwayat Transaksi'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active">Riwayat Transaksi</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<style>
    .transaction-card { transition: all 0.3s ease; border-radius: 12px; overflow: hidden; }
    .filter-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; color: white; }
    .stat-box { border-radius: 10px; padding: 12px; color: white; position: relative; overflow: hidden; }
    .stat-box::before { content: ''; position: absolute; top: -50%; right: -50%; width: 100%; height: 100%; background: rgba(255,255,255,0.1); border-radius: 50%; }
    .stat-masuk { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .stat-keluar { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .stat-total { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
    .table-row-hover:hover { background-color: #f8fafc !important; transform: scale(1.005); transition: all 0.2s ease; }
    .btn-action { width: 28px; height: 28px; padding: 0; font-size: 0.6875rem; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s ease; }
    .btn-action:hover { transform: scale(1.1); box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
    .user-avatar { width: 24px; height: 24px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: inline-flex; align-items: center; justify-content: center; font-weight: 600; font-size: 10px; margin-right: 6px; }
    .badge-transaction { padding: 4px 8px; border-radius: 12px; font-size: 0.625rem; font-weight: 600; }
    .bulk-toolbar { position: sticky; top: 56px; z-index: 100; background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border-radius: 0 0 12px 12px; padding: 12px 20px; margin: -16px -16px 16px -16px; box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.2); animation: slideDown 0.3s ease; }
    .bulk-toolbar form { margin: 0; }
    @keyframes  slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    .export-modal .modal-content { border-radius: 12px; border: none; }
    .export-modal .modal-header { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border-radius: 12px 12px 0 0; padding: 12px 16px; }
    .date-chip { display: inline-block; background: #e0e7ff; color: #4338ca; padding: 3px 8px; border-radius: 12px; font-size: 0.6875rem; margin: 2px; cursor: pointer; }
    .date-chip:hover { background: #c7d2fe; }
    /* Export type cards */
    .export-type-card { border: 2px solid #e5e7eb; border-radius: 10px; padding: 10px 6px; cursor: pointer; transition: all 0.2s ease; text-align: center; }
    .export-type-card:hover { border-color: #10b981; background: #f0fdf4; }
    .export-type-card.active { border-color: #10b981; background: #f0fdf4; box-shadow: 0 0 0 3px rgba(16,185,129,0.15); }
    .export-icon { width: 36px; height: 36px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 1rem; margin-bottom: 4px; }
    .export-section { animation: fadeIn 0.3s ease; }
    @keyframes  fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
    
    /* Fix for dropdown transparency in modal - COMPREHENSIVE */
    .export-modal .form-select,
    .export-modal select,
    #exportModal .form-select,
    #exportModal select {
        background-color: #fff !important;
        background: #fff !important;
        opacity: 1 !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        filter: none !important;
        border: 1px solid #ced4da !important;
        color: #212529 !important;
    }
    
    /* Force solid background on dropdown options - AGGRESSIVE FIX */
    .export-modal .form-select option,
    .export-modal select option,
    #exportModal .form-select option,
    #exportModal select option,
    select[name="bulan"] option,
    select[name="bulan_dari"] option,
    select[name="bulan_sampai"] option,
    #monthBulan option,
    #monthRangeBulanDari option,
    #monthRangeBulanSampai option,
    #yearRangeDari option,
    #yearRangeSampai option,
    #monthTahun option,
    #monthRangeTahunDari option,
    #monthRangeTahunSampai option {
        background-color: #ffffff !important;
        background: #ffffff !important;
        color: #000000 !important;
        color: #212529 !important;
        opacity: 1 !important;
        -webkit-text-fill-color: #000000 !important;
        font-weight: 500 !important;
    }
    
    /* Windows/Chrome specific fix */
    @media  screen and (-webkit-min-device-pixel-ratio: 0) {
        .export-modal select option,
        #exportModal select option {
            background-color: #ffffff !important;
            color: #000000 !important;
            text-shadow: none !important;
        }
    }
    
    /* Firefox specific fix */
    @-moz-document url-prefix() {
        .export-modal select option,
        #exportModal select option {
            background-color: #ffffff !important;
            color: #000000 !important;
        }
    }
    
    /* Ensure modal content area has solid background */
    .export-modal .modal-body,
    #exportModal .modal-body {
        background-color: #fff !important;
    }
    
    /* Fix for select elements in month section */
    #monthSection select,
    #monthRangeSection select,
    #yearRangeSection select {
        background-color: #fff !important;
        opacity: 1 !important;
        color: #212529 !important;
    }
    
    #monthSection select option,
    #monthRangeSection select option,
    #yearRangeSection select option {
        background-color: #fff !important;
        color: #000 !important;
        color: #212529 !important;
    }
    
    /* Fix dropdown when opened */
    .export-modal select:focus,
    #exportModal select:focus {
        background-color: #fff !important;
        color: #212529 !important;
    }
    
    /* Ensure optgroup and options are visible */
    .export-modal select optgroup,
    #exportModal select optgroup {
        background-color: #f8f9fa !important;
        color: #000 !important;
    }
    /* Fix for pagination on mobile */
    @media (max-width: 768px) {
        .pagination {
            flex-wrap: wrap;
            justify-content: center;
            font-size: 0.7rem;
        }
        .pagination .page-item {
            margin: 1px;
        }
        .pagination .page-link {
            padding: 4px 8px;
            min-width: 28px;
            text-align: center;
        }
        .pagination-info {
            font-size: 0.7rem;
            text-align: center;
            width: 100%;
            margin-bottom: 8px;
        }
    }
    
    /* Ensure pagination-info is styled */
    .pagination-info {
        font-size: 0.75rem;
        color: #6c757d;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- Header Section -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-0 fw-bold">Riwayat Transaksi</h5>
        <p class="text-muted mb-0 small">Kelola dan pantau semua transaksi</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" id="btnExportModal" class="btn btn-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#exportModal" style="font-size: 0.75rem;">
            <i class="fas fa-file-excel me-1"></i>Export
        </button>
        <a href="<?php echo e(route('transaksi.create')); ?>" class="btn btn-primary rounded-pill px-3" style="font-size: 0.75rem;">
            <i class="fas fa-plus me-1"></i>Input
        </a>
    </div>
</div>

<!-- Error Message Display -->
<?php if(session('error')): ?>
<div class="alert alert-danger alert-dismissible fade show mb-3" role="alert" style="font-size: 0.75rem;">
    <i class="fas fa-exclamation-triangle me-2"></i><?php echo e(session('error')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert" style="font-size: 0.6rem;"></button>
</div>
<?php endif; ?>

<?php if(session('success')): ?>
<div class="alert alert-success alert-dismissible fade show mb-3 auto-dismiss" role="alert" style="font-size: 0.75rem;" data-auto-dismiss="5000">
    <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert" style="font-size: 0.6rem;"></button>
</div>
<?php endif; ?>

<!-- Stats Cards -->
<div class="row g-2 mb-3">
    <div class="col-md-4">
        <div class="stat-box stat-masuk">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-0 opacity-75" style="font-size: 0.6875rem;">Barang Masuk</p>
                    <h4 class="mb-0 fw-bold" style="font-size: 1.25rem;"><?php echo e($transaksis->sum('jumlah_masuk')); ?></h4>
                </div>
                <i class="fas fa-arrow-down opacity-50" style="font-size: 1.25rem;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-box stat-keluar">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-0 opacity-75" style="font-size: 0.6875rem;">Barang Keluar</p>
                    <h4 class="mb-0 fw-bold" style="font-size: 1.25rem;"><?php echo e($transaksis->sum('jumlah_keluar')); ?></h4>
                </div>
                <i class="fas fa-arrow-up opacity-50" style="font-size: 1.25rem;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-box stat-total">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-0 opacity-75" style="font-size: 0.6875rem;">Total Transaksi</p>
                    <h4 class="mb-0 fw-bold" style="font-size: 1.25rem;"><?php echo e($transaksis->total()); ?></h4>
                </div>
                <i class="fas fa-exchange-alt opacity-50" style="font-size: 1.25rem;"></i>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 11">
    <div id="newTransactionToast" class="toast align-items-center text-white bg-warning border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="newTransactionToastBody">⚠️ Ada transaksi baru ditambahkan</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
<input type="hidden" id="lastTransactionTimestamp" value="<?php echo e($latestTimestamp ?? ''); ?>">

<!-- Filter Section -->
<div class="card filter-card mb-3 border-0 shadow">
    <div class="card-body py-2 px-3">
        <form method="GET" action="<?php echo e(route('transaksi.index')); ?>">
            <div class="row g-2 align-items-end">
                <?php if(Auth::user()->isAdmin()): ?>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label text-white-50" style="font-size: 0.625rem;">User</label>
                    <select name="user_id" class="form-select" style="font-size: 0.75rem; padding: 4px 8px;">
                        <option value="">Semua</option>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($user->id); ?>" <?php echo e(request('user_id') == $user->id ? 'selected' : ''); ?>><?php echo e($user->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <?php endif; ?>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label text-white-50" style="font-size: 0.625rem;">Tipe</label>
                    <select name="tipe" class="form-select" style="font-size: 0.75rem; padding: 4px 8px;">
                        <option value="">Semua</option>
                        <option value="masuk" <?php echo e(request('tipe') == 'masuk' ? 'selected' : ''); ?>>Masuk</option>
                        <option value="keluar" <?php echo e(request('tipe') == 'keluar' ? 'selected' : ''); ?>>Keluar</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label text-white-50" style="font-size: 0.625rem;">Barang</label>
                    <select name="barang_id" class="form-select" style="font-size: 0.75rem; padding: 4px 8px;">
                        <option value="">Semua</option>
                        <?php $__currentLoopData = $barangs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $barang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($barang->id); ?>" <?php echo e(request('barang_id') == $barang->id ? 'selected' : ''); ?>><?php echo e($barang->nama_barang); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label text-white-50" style="font-size: 0.625rem;">Dari</label>
                    <select id="filterDariDropdown" class="form-select mb-1" style="font-size: 0.75rem; padding: 4px 8px;" onchange="syncFilterDari()">
                        <option value="">-- Pilih --</option>
                        <?php $__currentLoopData = $availableDates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($date); ?>" <?php echo e(request('tanggal_dari') == $date ? 'selected' : ''); ?>><?php echo e(\Carbon\Carbon::parse($date)->translatedFormat('d M Y')); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <input type="date" name="tanggal_dari" id="filterDariManual" class="form-control" style="font-size: 0.75rem; padding: 4px 8px;" value="<?php echo e(request('tanggal_dari')); ?>" onchange="syncFilterDariDropdown()">
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label text-white-50" style="font-size: 0.625rem;">Sampai</label>
                    <select id="filterSampaiDropdown" class="form-select mb-1" style="font-size: 0.75rem; padding: 4px 8px;" onchange="syncFilterSampai()">
                        <option value="">-- Pilih --</option>
                        <?php $__currentLoopData = $availableDates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($date); ?>" <?php echo e(request('tanggal_sampai') == $date ? 'selected' : ''); ?>><?php echo e(\Carbon\Carbon::parse($date)->translatedFormat('d M Y')); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <input type="date" name="tanggal_sampai" id="filterSampaiManual" class="form-control" style="font-size: 0.75rem; padding: 4px 8px;" value="<?php echo e(request('tanggal_sampai')); ?>" onchange="syncFilterSampaiDropdown()">
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label text-white-50" style="font-size: 0.625rem;">Tahun</label>
                    <select name="tahun" class="form-select" style="font-size: 0.75rem; padding: 4px 8px;">
                        <option value="">Semua</option>
                        <?php $__empty_1 = true; $__currentLoopData = $availableYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tahun): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <option value="<?php echo e($tahun); ?>" <?php echo e(request('tahun') == $tahun ? 'selected' : ''); ?>><?php echo e($tahun); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <option value=""><?php echo e(date('Y')); ?></option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label text-white-50" style="font-size: 0.625rem;">Bulan</label>
                    <select name="bulan" class="form-select" style="font-size: 0.75rem; padding: 4px 8px;">
                        <option value="">Semua</option>
                        <?php for($b = 1; $b <= 12; $b++): ?>
                            <option value="<?php echo e($b); ?>" <?php echo e(request('bulan') == $b ? 'selected' : ''); ?>><?php echo e(\Carbon\Carbon::create(null, $b)->translatedFormat('F')); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-light flex-fill" style="font-size: 0.75rem; padding: 4px 8px;"><i class="fas fa-filter me-1"></i>Filter</button>
                        <a href="<?php echo e(route('transaksi.index')); ?>" class="btn btn-outline-light" style="font-size: 0.75rem; padding: 4px 8px;" title="Reset"><i class="fas fa-undo"></i></a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Delete Form -->
<form method="POST" action="<?php echo e(route('transaksi.bulkDelete')); ?>" id="bulkDeleteForm">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
    <div class="card bulk-toolbar mb-2 border-0 shadow-sm" id="bulkToolbar" style="display: none;">
        <div class="card-body py-2">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <span class="badge bg-danger rounded-pill me-2" id="selectedCount" style="font-size: 0.75rem;">0</span>
                    <span class="fw-medium" style="font-size: 0.75rem;">item dipilih</span>
                    <div class="form-check ms-3 mb-0">
                        <input class="form-check-input" type="checkbox" id="selectAll" style="font-size: 0.75rem;">
                        <label class="form-check-label" for="selectAll" style="font-size: 0.75rem;">Pilih Semua</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-danger rounded-pill" style="font-size: 0.75rem; padding: 4px 12px;" onclick="return confirm('Yakin ingin menghapus transaksi terpilih?')">
                    <i class="fas fa-trash-alt me-1"></i>Hapus
                </button>
            </div>
        </div>
    </div>
    <div class="card transaction-card border-0 shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle" id="transaksiTable" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-2 px-3" width="40"><input type="checkbox" class="form-check-input" id="selectAllHeader" style="font-size: 0.75rem;"></th>
                            <th class="py-2">No</th>
                            <th class="py-2">Tanggal</th>
                            <th class="py-2">Tanggal Keluar</th>
                            <th class="py-2">Barang</th>
                            <th class="py-2 text-center">Masuk</th>
                            <th class="py-2 text-center">Keluar</th>
                            <th class="py-2 text-center">Sisa</th>
                            <th class="py-2">User</th>
                            <th class="py-2">Pengambil</th>
                            <th class="py-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $transaksis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $transaksi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $canDelete = Auth::user()->isAdmin() || Auth::id() === $transaksi->user_id; ?>
                        <tr class="table-row-hover">
                            <td class="py-2 px-3"><?php if($canDelete): ?><input type="checkbox" class="form-check-input item-checkbox" name="ids[]" value="<?php echo e($transaksi->id); ?>" style="font-size: 0.75rem;"><?php endif; ?></td>
                            <td class="py-2 text-muted"><?php echo e($transaksis->firstItem() + $index); ?></td>
                            <td class="py-2"><div class="fw-medium"><?php echo e($transaksi->tanggal->format('d M Y')); ?></div></td>
                            <td class="py-2"><?php if($transaksi->tanggal_keluar): ?><div class="fw-medium text-warning"><?php echo e($transaksi->tanggal_keluar->format('d M Y')); ?></div><?php else: ?><span class="text-muted" style="font-size: 0.625rem;">-</span><?php endif; ?></td>
                            <td class="py-2">
                                <span class="badge-transaction bg-<?php echo e($transaksi->jumlah_masuk > 0 && $transaksi->jumlah_keluar > 0 ? 'info' : ($transaksi->jumlah_masuk > 0 ? 'success' : 'warning')); ?> text-white me-1">
                                    <i class="fas fa-<?php echo e($transaksi->jumlah_masuk > 0 && $transaksi->jumlah_keluar > 0 ? 'exchange-alt' : ($transaksi->jumlah_masuk > 0 ? 'arrow-down' : 'arrow-up')); ?>"></i>
                                </span>
                                <span class="fw-medium"><?php echo e($transaksi->barang->nama_barang); ?></span>
                            </td>
                            <td class="py-2 text-center"><?php if($transaksi->jumlah_masuk > 0): ?><span class="text-success fw-bold"><?php echo e($transaksi->jumlah_masuk); ?></span> <small class="text-muted"><?php echo e($transaksi->barang->satuan); ?></small><?php else: ?><span class="text-muted">-</span><?php endif; ?></td>
                            <td class="py-2 text-center"><?php if($transaksi->jumlah_keluar > 0): ?><span class="text-warning fw-bold"><?php echo e($transaksi->jumlah_keluar); ?></span> <small class="text-muted"><?php echo e($transaksi->barang->satuan); ?></small><?php else: ?><span class="text-muted">-</span><?php endif; ?></td>
                            <td class="py-2 text-center"><span class="badge rounded-pill bg-<?php echo e(($transaksi->sisa_stok ?? 0) <= 0 ? 'danger' : (($transaksi->sisa_stok ?? 0) <= $transaksi->barang->stok_minimum ? 'warning text-dark' : 'success')); ?>" style="font-size: 0.625rem; padding: 2px 8px;"><?php echo e($transaksi->sisa_stok ?? 0); ?></span></td>
                            <td class="py-2">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar"><?php echo e(strtoupper(substr($transaksi->user->name, 0, 1))); ?></div>
                                    <div>
                                        <div class="fw-medium" style="font-size: 0.75rem;"><?php echo e($transaksi->user->name); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-2">
                                <?php if($transaksi->jumlah_keluar > 0): ?>
                                    <?php if($transaksi->nama_pengambil): ?>
                                        <div class="fw-medium" style="font-size: 0.75rem;"><?php echo e($transaksi->nama_pengambil); ?></div>
                                    <?php endif; ?>
                                    <?php if($transaksi->ruangan): ?>
                                        <small class="text-muted" style="font-size: 0.625rem;"><i class="fas fa-door-open me-1"></i><?php echo e($transaksi->ruangan->nama_ruangan); ?></small>
                                    <?php endif; ?>
                                    <?php if(!$transaksi->nama_pengambil && !$transaksi->ruangan): ?>
                                        <span class="text-muted" style="font-size: 0.625rem;">-</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted" style="font-size: 0.625rem;">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-2 text-center">
                                <div class="d-flex justify-content-center align-items-center gap-1">
                                    <a href="<?php echo e(route('transaksi.show', $transaksi)); ?>" class="btn btn-info btn-action" title="Detail"><i class="fas fa-eye"></i></a>
                                    <a href="<?php echo e(route('transaksi.edit', $transaksi)); ?>" class="btn btn-warning btn-action" title="Edit"><i class="fas fa-edit"></i></a>
                                    <?php if($canDelete): ?>
                                    <form action="<?php echo e(route('transaksi.destroy', $transaksi)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button type="submit" class="btn btn-danger btn-action" title="Hapus"><i class="fas fa-trash"></i></button></form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="10" class="text-center py-4"><div class="text-muted"><i class="fas fa-inbox fa-2x mb-2 opacity-25"></i><p class="mb-0" style="font-size: 0.75rem;">Tidak ada data transaksi</p><a href="<?php echo e(route('transaksi.create')); ?>" class="btn btn-primary btn-sm mt-2 rounded-pill" style="font-size: 0.6875rem;"><i class="fas fa-plus me-1"></i>Buat Transaksi</a></div></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="pagination-info">Menampilkan <?php echo e($transaksis->firstItem()); ?> - <?php echo e($transaksis->lastItem()); ?> dari <?php echo e($transaksis->total()); ?> transaksi</div>
                <nav><?php echo e($transaksis->appends(request()->all())->links('pagination::bootstrap-4')); ?></nav>
            </div>
        </div>
    </div>
</form>

<!-- Export Modal -->
<div class="modal fade export-modal" id="exportModal" tabindex="-1">
    <div class="modal-dialog modal-lg" style="font-size: 0.8125rem;">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title fw-bold"><i class="fas fa-file-excel me-2"></i>Export Data Transaksi</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="font-size: 0.75rem;"></button>
            </div>
            <form action="<?php echo e(route('transaksi.export')); ?>" method="GET" id="exportForm">
                <div class="modal-body py-3">
                    <!-- Step 1: Pilih Jenis -->
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size: 0.75rem;"><span class="badge bg-primary rounded-pill me-1">1</span> Pilih Jenis Export</label>
                        <div class="row g-2">
                            <?php
                            $types = [
                                ['id' => 'all', 'icon' => 'database', 'color' => 'primary', 'label' => 'Semua Data', 'desc' => 'Seluruh riwayat'],
                                ['id' => 'range', 'icon' => 'calendar-alt', 'color' => 'success', 'label' => 'Rentang Tanggal', 'desc' => 'Dari tanggal A ke B'],
                                ['id' => 'dates', 'icon' => 'calendar-check', 'color' => 'warning', 'label' => 'Pilih Tanggal', 'desc' => 'Beberapa tanggal'],
                                ['id' => 'year', 'icon' => 'calendar', 'color' => 'info', 'label' => 'Per Tahun', 'desc' => 'Satu tahun tertentu'],
                            ];
                            $types2 = [
                                ['id' => 'year_range', 'icon' => 'arrows-alt-h', 'color' => 'primary', 'label' => 'Rentang Tahun', 'desc' => 'Dari tahun X ke Y'],
                                ['id' => 'month', 'icon' => 'calendar-day', 'color' => 'success', 'label' => 'Per Bulan', 'desc' => 'Satu bulan tertentu'],
                                ['id' => 'month_range', 'icon' => 'calendar-week', 'color' => 'warning', 'label' => 'Rentang Bulan', 'desc' => 'Dari bulan A ke B'],
                            ];
                            ?>
                            <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-6 col-md-3">
                                <div class="export-type-card <?php echo e($t['id'] === 'all' ? 'active' : ''); ?>" onclick="selectExportType('<?php echo e($t['id']); ?>')">
                                    <input class="form-check-input d-none" type="radio" name="export_type" id="export_<?php echo e($t['id']); ?>" value="<?php echo e($t['id']); ?>" <?php echo e($t['id'] === 'all' ? 'checked' : ''); ?>>
                                    <label class="d-block text-center w-100" for="export_<?php echo e($t['id']); ?>" style="cursor:pointer;">
                                        <div class="export-icon bg-<?php echo e($t['color']); ?>-subtle text-<?php echo e($t['color']); ?>"><i class="fas fa-<?php echo e($t['icon']); ?>"></i></div>
                                        <div class="fw-bold" style="font-size: 0.7rem;"><?php echo e($t['label']); ?></div>
                                        <div class="text-muted" style="font-size: 0.55rem;"><?php echo e($t['desc']); ?></div>
                                    </label>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <div class="row g-2 mt-1">
                            <?php $__currentLoopData = $types2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-6 col-md-3">
                                <div class="export-type-card" onclick="selectExportType('<?php echo e($t['id']); ?>')">
                                    <input class="form-check-input d-none" type="radio" name="export_type" id="export_<?php echo e($t['id']); ?>" value="<?php echo e($t['id']); ?>">
                                    <label class="d-block text-center w-100" for="export_<?php echo e($t['id']); ?>" style="cursor:pointer;">
                                        <div class="export-icon bg-<?php echo e($t['color']); ?>-subtle text-<?php echo e($t['color']); ?>"><i class="fas fa-<?php echo e($t['icon']); ?>"></i></div>
                                        <div class="fw-bold" style="font-size: 0.7rem;"><?php echo e($t['label']); ?></div>
                                        <div class="text-muted" style="font-size: 0.55rem;"><?php echo e($t['desc']); ?></div>
                                    </label>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    <!-- Panduan -->
                    <div id="exportGuide" class="alert alert-light border mb-3 py-2" style="font-size: 0.7rem;">
                        <i class="fas fa-lightbulb text-warning me-1"></i>
                        <span id="exportGuideText">Klik <strong>Export</strong> untuk mengunduh seluruh data transaksi dalam format Excel.</span>
                    </div>

                    <!-- Filter User -->
                    <?php if(Auth::user()->isAdmin()): ?>
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 0.75rem;"><span class="badge bg-secondary rounded-pill me-1">2</span> Filter User <small class="text-muted">(opsional)</small></label>
                        <select name="user_id" class="form-select" style="font-size: 0.75rem; padding: 4px 8px;">
                            <option value="">Semua User</option>
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <!-- Range Section - Fleksibel Datepicker -->
                    <div id="rangeSection" class="export-section" style="display: none;">
                        <!-- Panduan Export Rentang Tanggal -->
                        <div class="card border-info mb-2" style="font-size: 0.625rem;">
                            <div class="card-header bg-info text-white py-1 px-2">
                                <i class="fas fa-info-circle me-1"></i><strong>Panduan Export Rentang Tanggal</strong>
                            </div>
                            <div class="card-body py-2 px-2" style="font-size: 0.625rem;">
                                <div class="mb-2">
                                    <div class="d-flex align-items-start mb-1">
                                        <span class="badge bg-primary me-2" style="font-size: 0.5rem;">1</span>
                                        <span><i class="fas fa-calendar-alt text-primary me-1"></i>Pilih tanggal awal (dari) dengan mengklik icon kalender</span>
                                    </div>
                                    <div class="d-flex align-items-start mb-1">
                                        <span class="badge bg-primary me-2" style="font-size: 0.5rem;">2</span>
                                        <span><i class="fas fa-calendar-alt text-primary me-1"></i>Pilih tanggal akhir (sampai) dengan mengklik icon kalender</span>
                                    </div>
                                    <div class="d-flex align-items-start">
                                        <span class="badge bg-primary me-2" style="font-size: 0.5rem;">3</span>
                                        <span><i class="fas fa-file-export text-primary me-1"></i>Klik tombol Export untuk generate file</span>
                                    </div>
                                </div>
                                <hr class="my-1" style="opacity: 0.2;">
                                <div class="alert alert-warning py-1 px-2 mb-1" style="font-size: 0.625rem;">
                                    <i class="fas fa-exclamation-circle me-1 text-warning"></i>
                                    <strong>Catatan Penting:</strong>
                                    <ul class="mb-0 ps-3 mt-1">
                                        <li>Anda bisa memilih tanggal bebas, tidak terbatas pada tanggal transaksi yang ada</li>
                                        <li>Jika tidak ada transaksi di rentang tanggal yang dipilih, hasil export akan kosong</li>
                                    </ul>
                                </div>
                                <div class="alert alert-light border py-1 px-2 mb-0" style="font-size: 0.625rem;">
                                    <i class="fas fa-lightbulb text-warning me-1"></i>
                                    <strong>Tips:</strong> Gunakan filter "Semua Tanggal" terlebih dahulu untuk melihat tanggal apa saja yang memiliki transaksi
                                </div>
                            </div>
                        </div>

                        <label class="form-label fw-bold" style="font-size: 0.75rem;">Rentang Tanggal <span class="text-muted fw-normal">(Pilih tanggal bebas)</span></label>
                        <div class="border rounded p-2 bg-light">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label text-muted" style="font-size: 0.625rem;">Dari Tanggal</label>
                                    <input type="date" name="tanggal_dari" id="rangeDariManual" class="form-control" style="font-size: 0.75rem; padding: 4px 8px;" onchange="validateDateRange()">
                                    <small class="text-muted" style="font-size: 0.5rem;">Klik icon kalender untuk memilih tanggal</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted" style="font-size: 0.625rem;">Sampai Tanggal</label>
                                    <input type="date" name="tanggal_sampai" id="rangeSampaiManual" class="form-control" style="font-size: 0.75rem; padding: 4px 8px;" onchange="validateDateRange()">
                                    <small class="text-muted" style="font-size: 0.5rem;">Klik icon kalender untuk memilih tanggal</small>
                                </div>
                            </div>
                            <div id="rangeValidationMsg" class="alert alert-warning mt-2 mb-0 py-1 px-2" style="font-size: 0.625rem; display: none;">
                                <i class="fas fa-exclamation-triangle me-1"></i>Tanggal awal harus sebelum atau sama dengan tanggal akhir
                            </div>
                            <div class="alert alert-info mt-2 mb-0 py-1 px-2" style="font-size: 0.5rem;">
                                <i class="fas fa-info-circle me-1"></i><strong>Tips:</strong> Sistem akan menampilkan transaksi yang ada di rentang tanggal yang dipilih. Jika tidak ada transaksi di tanggal tertentu, data akan kosong.
                            </div>
                        </div>
                    </div>

                    <!-- Dates Section -->
                    <div id="datesSection" class="export-section" style="display: none;">
                        <label class="form-label fw-bold" style="font-size: 0.75rem;">Pilih Tanggal</label>
                        <input type="hidden" name="tanggal_list" id="tanggalListInput">
                        <div class="border rounded p-2 bg-light">
                            <div class="row g-2 mb-2">
                                <div class="col-md-8">
                                    <select id="dateSelectDropdown" class="form-select" style="font-size: 0.75rem; padding: 4px 8px;">
                                        <option value="">-- Pilih Tanggal --</option>
                                        <?php $__currentLoopData = $availableDates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($date); ?>"><?php echo e(\Carbon\Carbon::parse($date)->translatedFormat('d F Y')); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-primary w-100" onclick="addDateFromDropdown()" style="font-size: 0.75rem; padding: 4px 12px;"><i class="fas fa-plus me-1"></i>Tambah</button>
                                </div>
                            </div>
                            <div id="selectedDates" class="d-flex flex-wrap gap-1"></div>
                            <div id="datesEmpty" class="text-center text-muted py-2" style="font-size: 0.625rem;"><i class="fas fa-calendar-plus me-1"></i>Belum ada tanggal dipilih. Pilih dari dropdown lalu klik Tambah.</div>
                        </div>
                    </div>

                    <!-- Year Section -->
                    <div id="yearSection" class="export-section" style="display: none;">
                        <label class="form-label fw-bold" style="font-size: 0.75rem;">Pilih Tahun</label>
                        <div class="border rounded p-2 bg-light">
                            <select name="tahun" class="form-select" style="font-size: 0.75rem; padding: 4px 8px;">
                                <option value="">-- Pilih Tahun --</option>
                                <?php $__empty_1 = true; $__currentLoopData = $availableYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tahun): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php
                                        $monthCount = isset($monthsByYear[$tahun]) && is_countable($monthsByYear[$tahun]) ? count($monthsByYear[$tahun]) : 0;
                                    ?>
                                    <option value="<?php echo e($tahun); ?>"><?php echo e($tahun); ?> (<?php echo e($monthCount); ?> bulan data)</option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <option value="" disabled>Tidak ada data transaksi</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Year Range Section -->
                    <div id="yearRangeSection" class="export-section" style="display: none;">
                        <label class="form-label fw-bold" style="font-size: 0.75rem;">Rentang Tahun</label>
                        <div class="border rounded p-2 bg-light">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label text-muted" style="font-size: 0.625rem;">Dari Tahun</label>
                                    <select name="tahun_dari" id="yearRangeDari" class="form-select" style="font-size: 0.75rem; padding: 4px 8px;" onchange="filterYearRangeSampai()">
                                        <option value="">-- Pilih --</option>
                                        <?php $__empty_1 = true; $__currentLoopData = $availableYears->sort(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tahun): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <option value="<?php echo e($tahun); ?>"><?php echo e($tahun); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <option value="" disabled>Tidak ada data</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted" style="font-size: 0.625rem;">Sampai Tahun</label>
                                    <select name="tahun_sampai" id="yearRangeSampai" class="form-select" style="font-size: 0.75rem; padding: 4px 8px;">
                                        <option value="">-- Pilih --</option>
                                        <?php $__empty_1 = true; $__currentLoopData = $availableYears->sort(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tahun): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <option value="<?php echo e($tahun); ?>"><?php echo e($tahun); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <option value="" disabled>Tidak ada data</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Month Section -->
                    <div id="monthSection" class="export-section" style="display: none;">
                        <label class="form-label fw-bold" style="font-size: 0.75rem;">Pilih Bulan</label>
                        <div class="border rounded p-2 bg-light">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label text-muted" style="font-size: 0.625rem;">Tahun</label>
                                    <select name="tahun_bulan" id="monthTahun" class="form-select" style="font-size: 0.75rem; padding: 4px 8px;" onchange="updateMonthOptions('monthBulan', this.value)">
                                        <option value="">-- Pilih Tahun --</option>
                                        <?php $__empty_1 = true; $__currentLoopData = $availableYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tahun): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <option value="<?php echo e($tahun); ?>"><?php echo e($tahun); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <option value="" disabled>Tidak ada data</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted" style="font-size: 0.625rem;">Bulan <small class="text-success">(hanya bulan yang ada datanya)</small></label>
                                    <select name="bulan" id="monthBulan" class="form-select" style="font-size: 0.75rem; padding: 4px 8px;" disabled>
                                        <option value="">-- Pilih tahun dulu --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Month Range Section -->
                    <div id="monthRangeSection" class="export-section" style="display: none;">
                        <label class="form-label fw-bold" style="font-size: 0.75rem;">Rentang Bulan</label>
                        <div class="border rounded p-2 bg-light">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="border-end pe-2">
                                        <div class="text-muted fw-bold mb-1" style="font-size: 0.625rem;"><i class="fas fa-play text-success me-1" style="font-size: 0.5rem;"></i>DARI</div>
                                        <select name="tahun_dari" id="monthRangeTahunDari" class="form-select mb-1" style="font-size: 0.75rem; padding: 4px 8px;" onchange="updateMonthOptions('monthRangeBulanDari', this.value)">
                                            <option value="">-- Tah
un --</option>
                                            <?php $__empty_1 = true; $__currentLoopData = $availableYears->sort(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tahun): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <option value="<?php echo e($tahun); ?>"><?php echo e($tahun); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <option value="" disabled>Tidak ada data</option>
                                            <?php endif; ?>
                                        </select>
                                        <select name="bulan_dari" id="monthRangeBulanDari" class="form-select" style="font-size: 0.75rem; padding: 4px 8px;" disabled>
                                            <option value="">-- Pilih tahun dulu --</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="ps-2">
                                        <div class="text-muted fw-bold mb-1" style="font-size: 0.625rem;"><i class="fas fa-stop text-danger me-1" style="font-size: 0.5rem;"></i>SAMPAI</div>
                                        <select name="tahun_sampai" id="monthRangeTahunSampai" class="form-select mb-1" style="font-size: 0.75rem; padding: 4px 8px;" onchange="updateMonthOptions('monthRangeBulanSampai', this.value)">
                                            <option value="">-- Tahun --</option>
                                            <?php $__empty_1 = true; $__currentLoopData = $availableYears->sort(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tahun): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <option value="<?php echo e($tahun); ?>"><?php echo e($tahun); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <option value="" disabled>Tidak ada data</option>
                                            <?php endif; ?>
                                        </select>
                                        <select name="bulan_sampai" id="monthRangeBulanSampai" class="form-select" style="font-size: 0.75rem; padding: 4px 8px;" disabled>
                                            <option value="">-- Pilih tahun dulu --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer py-2 d-flex justify-content-between">
                    <div class="text-muted" style="font-size: 0.6rem;"><i class="fas fa-info-circle me-1"></i>File diunduh dalam format .xlsx (Excel)</div>
                    <div>
                        <button type="button" class="btn btn-light rounded-pill" style="font-size: 0.75rem; padding: 4px 12px;" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-success rounded-pill" style="font-size: 0.75rem; padding: 4px 16px;" id="exportSubmitBtn" onclick="validateAndSubmit()">
                            <i class="fas fa-download me-1"></i>Export
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
// === Data dari server ===
const monthsByYear = <?php echo json_encode($monthsByYear, 15, 512) ?>;
const monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

// Debug: Log data dari server
console.log('monthsByYear data:', monthsByYear);
console.log('Available years:', Object.keys(monthsByYear));

const exportGuides = {
    all: 'Klik <strong>Export</strong> untuk mengunduh seluruh data transaksi dalam format Excel.',
    range: 'Pilih tanggal <strong>awal</strong> dan <strong>akhir</strong> sesuka Anda menggunakan kalender. Sistem akan menampilkan transaksi yang ada di rentang tersebut (meski tidak ada transaksi di tanggal tertentu).',
    dates: 'Pilih satu atau beberapa <strong>tanggal spesifik</strong> dari dropdown, lalu klik Tambah. Cocok untuk export tanggal tertentu saja.',
    year: 'Pilih <strong>satu tahun</strong>. Hanya tahun yang memiliki data transaksi yang ditampilkan.',
    year_range: 'Pilih tahun <strong>awal</strong> dan <strong>akhir</strong>. Tahun sampai otomatis disesuaikan agar tidak lebih kecil dari tahun awal.',
    month: 'Pilih <strong>tahun</strong> dulu, lalu pilih <strong>bulan</strong>. Hanya bulan yang ada transaksinya yang muncul.',
    month_range: 'Pilih tahun dan bulan <strong>awal</strong>, lalu tahun dan bulan <strong>akhir</strong>. Bulan otomatis menyesuaikan data yang tersedia.'
};

// === Export Type Selection ===
function selectExportType(type) {
    // BUG FIX: Add null checks to prevent "Cannot set properties of null" error
    const radioEl = document.getElementById('export_' + type);
    if (radioEl) radioEl.checked = true;
    
    document.querySelectorAll('.export-type-card').forEach(c => c.classList.remove('active'));
    const cardEl = document.querySelector('.export-type-card[onclick*="' + type + '"]');
    if (cardEl) cardEl.classList.add('active');
    
    // Hide all sections
    document.querySelectorAll('.export-section').forEach(s => s.style.display = 'none');
    
    // Show relevant section
    const sectionMap = { range: 'rangeSection', dates: 'datesSection', year: 'yearSection', year_range: 'yearRangeSection', month: 'monthSection', month_range: 'monthRangeSection' };
    if (sectionMap[type]) {
        const sectionEl = document.getElementById(sectionMap[type]);
        if (sectionEl) sectionEl.style.display = 'block';
    }
    
    // Update guide - with null check
    const guideEl = document.getElementById('exportGuideText');
    if (guideEl) guideEl.innerHTML = exportGuides[type] || '';
    
    // Clear irrelevant fields
    clearIrrelevantFields(type);
}

function clearIrrelevantFields(exportType) {
    const fieldMap = { all: [], range: ['tanggal_dari', 'tanggal_sampai'], dates: ['tanggal_list'], year: ['tahun'], year_range: ['tahun_dari', 'tahun_sampai'], month: ['tahun_bulan', 'bulan'], month_range: ['tahun_dari', 'bulan_dari', 'tahun_sampai', 'bulan_sampai'] };
    const allFields = ['tanggal_dari', 'tanggal_sampai', 'tanggal_list', 'tahun', 'tahun_dari', 'tahun_sampai', 'bulan', 'bulan_dari', 'bulan_sampai', 'tahun_bulan'];
    const relevant = fieldMap[exportType] || [];
    allFields.forEach(f => { 
        if (!relevant.includes(f)) { 
            const el = document.querySelector('#exportForm [name="' + f + '"]'); 
            if (el) el.value = ''; 
        } 
    });
    // Reset dropdowns with null checks
    ['rangeDariDropdown', 'rangeSampaiDropdown', 'rangeDariManual', 'rangeSampaiManual'].forEach(id => { 
        const el = document.getElementById(id); 
        if (el) el.value = ''; 
    });
    selectedDates = [];
    updateDateDisplay();
}

// === Dynamic Month Options (hanya bulan yang ada datanya) ===
function updateMonthOptions(selectId, year) {
    const sel = document.getElementById(selectId);
    sel.innerHTML = '';
    
    console.log('updateMonthOptions called:', selectId, year, monthsByYear);
    
    if (!year || !monthsByYear[year]) {
        sel.innerHTML = '<option value="">-- Pilih tahun dulu --</option>';
        sel.disabled = true;
        return;
    }
    
    // Convert to array if it's an object (from JSON)
    let months = monthsByYear[year];
    if (!Array.isArray(months)) {
        months = Object.values(months);
    }
    
    if (months.length === 0) {
        sel.innerHTML = '<option value="">-- Tidak ada data --</option>';
        sel.disabled = true;
        return;
    }
    
    sel.disabled = false;
    sel.innerHTML = '<option value="">-- Pilih Bulan --</option>';
    months.forEach(m => {
        const monthNum = parseInt(m);
        const opt = document.createElement('option');
        opt.value = monthNum;
        opt.textContent = monthNames[monthNum];
        sel.appendChild(opt);
    });
}

// === Year Range: filter "sampai" >= "dari" ===
function filterYearRangeSampai() {
    const dari = document.getElementById('yearRangeDari').value;
    const sampai = document.getElementById('yearRangeSampai');
    Array.from(sampai.options).forEach(opt => {
        if (opt.value && dari) { opt.disabled = parseInt(opt.value) < parseInt(dari); }
        else { opt.disabled = false; }
    });
    if (sampai.value && parseInt(sampai.value) < parseInt(dari)) sampai.value = '';
}

// === Date Range Validation for Manual Date Inputs ===
function validateDateRange() {
    const dariInput = document.getElementById('rangeDariManual');
    const sampaiInput = document.getElementById('rangeSampaiManual');
    const validationMsg = document.getElementById('rangeValidationMsg');
    
    const dari = dariInput ? dariInput.value : '';
    const sampai = sampaiInput ? sampaiInput.value : '';
    
    // If either date is empty, hide validation message
    if (!dari || !sampai) {
        if (validationMsg) validationMsg.style.display = 'none';
        return true;
    }
    
    // Compare dates
    const dariDate = new Date(dari);
    const sampaiDate = new Date(sampai);
    
    if (dariDate > sampaiDate) {
        // Invalid range: show validation message
        if (validationMsg) validationMsg.style.display = 'block';
        
        // Optional: auto-correct by setting sampai = dari
        // Uncomment the next line if you want auto-correction
        // sampaiInput.value = dari;
        
        return false;
    } else {
        // Valid range: hide validation message
        if (validationMsg) validationMsg.style.display = 'none';
        return true;
    }
}

// === Multiple Dates ===
let selectedDates = [];
function addDateFromDropdown() {
    const dd = document.getElementById('dateSelectDropdown');
    if (!dd.value) { alert('Pilih tanggal dari dropdown terlebih dahulu'); return; }
    if (selectedDates.includes(dd.value)) { alert('Tanggal sudah dipilih'); return; }
    selectedDates.push(dd.value);
    selectedDates.sort();
    updateDateDisplay();
    dd.value = '';
}
function removeDate(date) { selectedDates = selectedDates.filter(d => d !== date); updateDateDisplay(); }
function updateDateDisplay() {
    const container = document.getElementById('selectedDates');
    const input = document.getElementById('tanggalListInput');
    const empty = document.getElementById('datesEmpty');
    container.innerHTML = '';
    selectedDates.forEach(date => {
        const d = new Date(date);
        const formatted = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        const chip = document.createElement('div');
        chip.className = 'date-chip';
        chip.innerHTML = formatted + ' <i class="fas fa-times ms-1" onclick="removeDate(\'' + date + '\')" style="cursor:pointer;opacity:0.6;"></i>';
        container.appendChild(chip);
    });
    input.value = selectedDates.join(',');
    if (empty) empty.style.display = selectedDates.length > 0 ? 'none' : 'block';
}

// === Filter sync - ENHANCED TWO-WAY BINDING ===
function syncFilterDari() { 
    const d = document.getElementById('filterDariDropdown'); 
    const m = document.getElementById('filterDariManual');
    if (d.value) {
        m.value = d.value;
        // Trigger change event for any listeners
        m.dispatchEvent(new Event('change'));
    }
}

function syncFilterDariDropdown() { 
    const d = document.getElementById('filterDariDropdown'); 
    const m = document.getElementById('filterDariManual');
    // Reset dropdown first
    d.value = ''; 
    // Then try to match
    for (let i = 0; i < d.options.length; i++) {
        if (d.options[i].value === m.value) {
            d.value = m.value;
            break;
        }
    }
}

function syncFilterSampai() { 
    const d = document.getElementById('filterSampaiDropdown'); 
    const m = document.getElementById('filterSampaiManual');
    if (d.value) {
        m.value = d.value;
        // Trigger change event for any listeners
        m.dispatchEvent(new Event('change'));
    }
}

function syncFilterSampaiDropdown() { 
    const d = document.getElementById('filterSampaiDropdown'); 
    const m = document.getElementById('filterSampaiManual');
    // Reset dropdown first
    d.value = ''; 
    // Then try to match
    for (let i = 0; i < d.options.length; i++) {
        if (d.options[i].value === m.value) {
            d.value = m.value;
            break;
        }
    }
}

// === Validation & Submit ===
function validateAndSubmit() {
    const form = document.getElementById('exportForm');
    const type = document.querySelector('input[name="export_type"]:checked')?.value;
    
    if (!type) { 
        alert('Pilih jenis export terlebih dahulu'); 
        return; 
    }
    
    let msg = '';
    switch(type) {
        case 'range':
            const dari = document.getElementById('rangeDariManual').value;
            const sampai = document.getElementById('rangeSampaiManual').value;
            if (!dari || !sampai) msg = 'Pilih tanggal dari dan sampai';
            else if (dari > sampai) msg = 'Tanggal dari harus lebih kecil dari tanggal sampai';
            break;
        case 'dates':
            if (selectedDates.length === 0) msg = 'Pilih minimal satu tanggal';
            break;
        case 'year':
            if (!form.querySelector('#yearSection select[name="tahun"]').value) msg = 'Pilih tahun';
            break;
        case 'year_range':
            if (!document.getElementById('yearRangeDari').value || !document.getElementById('yearRangeSampai').value) msg = 'Pilih tahun dari dan sampai';
            break;
        case 'month':
            if (!document.getElementById('monthTahun').value || !document.getElementById('monthBulan').value) msg = 'Pilih tahun dan bulan';
            break;
        case 'month_range':
            if (!document.getElementById('monthRangeTahunDari').value || !document.getElementById('monthRangeBulanDari').value || !document.getElementById('monthRangeTahunSampai').value || !document.getElementById('monthRangeBulanSampai').value) msg = 'Lengkapi semua field rentang bulan';
            break;
    }
    
    if (msg) { 
        alert(msg); 
        return; 
    }
    
    const btn = document.getElementById('exportSubmitBtn');
    const orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Memproses...';
    
    // Add timeout warning if export takes too long
    const timeoutWarning = setTimeout(() => {
        alert('Export sedang diproses. Jika tidak terjadi apa-apa setelah 30 detik, coba refresh halaman dan ulangi.');
    }, 30000);
    
    // Close modal before submitting to improve UX
    const modal = bootstrap.Modal.getInstance(document.getElementById('exportModal'));
    if (modal) modal.hide();
    
    form.submit();
    
    // Re-enable button after 5 seconds
    setTimeout(() => { 
        clearTimeout(timeoutWarning);
        btn.disabled = false; 
        btn.innerHTML = orig; 
    }, 5000);
}

// === Bulk Delete ===
const selectAllHeader = document.getElementById('selectAllHeader');
const selectAll = document.getElementById('selectAll');
const itemCheckboxes = document.querySelectorAll('.item-checkbox');
const bulkToolbar = document.getElementById('bulkToolbar');
const selectedCount = document.getElementById('selectedCount');
function updateSelectedCount() {
    const count = document.querySelectorAll('.item-checkbox:checked').length;
    selectedCount.textContent = count;
    bulkToolbar.style.display = count > 0 ? 'block' : 'none';
}
if (selectAllHeader) selectAllHeader.addEventListener('change', function() { itemCheckboxes.forEach(cb => cb.checked = this.checked); updateSelectedCount(); });
if (selectAll) selectAll.addEventListener('change', function() { itemCheckboxes.forEach(cb => cb.checked = this.checked); if (selectAllHeader) selectAllHeader.checked = this.checked; updateSelectedCount(); });
itemCheckboxes.forEach(cb => cb.addEventListener('change', function() {
    updateSelectedCount();
    const allChecked = document.querySelectorAll('.item-checkbox:checked').length === itemCheckboxes.length;
    if (selectAllHeader) selectAllHeader.checked = allChecked;
    if (selectAll) selectAll.checked = allChecked;
}));

// === Polling ===
class TransactionPolling {
    constructor() {
        this.lastTimestamp = document.getElementById('lastTransactionTimestamp').value;
        this.interval = 30000; // 30 detik
        this.url = '<?php echo e(route("api.transactions.check-updates")); ?>';
        this.failCount = 0;
        // Don't poll if page was just refreshed after creating transaction
        this.shouldPoll = !window.location.href.includes('refresh=');
        // Skip first check if there's a success message (user just created transaction)
        this.skipFirstCheck = document.querySelector('.alert-success') !== null;
        this.checkCount = 0;
    }
    start() {
        if (!this.shouldPoll) {
            console.log('Polling disabled: page just refreshed after transaction');
            // Clear the refresh parameter from URL without reloading
            if (window.history.replaceState) {
                const cleanUrl = window.location.href.split('?')[0];
                window.history.replaceState({}, document.title, cleanUrl);
            }
            return;
        }
        if (!this.lastTimestamp) this.lastTimestamp = new Date().toISOString();
        // Delay first check by 5 seconds to avoid immediate refresh after creating transaction
        setTimeout(() => {
            this.timer = setInterval(() => this.check(), this.interval);
        }, 5000);
    }
    async check() {
        this.checkCount++;
        // Skip first check if there's success message (avoid double notification)
        if (this.skipFirstCheck && this.checkCount === 1) {
            console.log('Skipping first poll check due to success message');
            return;
        }
        try {
            const r = await fetch(this.url + '?since=' + encodeURIComponent(this.lastTimestamp), {
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
            });
            if (!r.ok) { this.handleError(); return; }
            this.failCount = 0;
            const d = await r.json();
            if (d.has_new && d.timestamp !== this.lastTimestamp) {
                const toast = new bootstrap.Toast(document.getElementById('newTransactionToast'));
                document.getElementById('newTransactionToastBody').textContent = '⚠️ Ada ' + (d.count || 1) + ' transaksi baru';
                toast.show();
                this.lastTimestamp = d.timestamp;
                window.location.href = window.location.href.split('?')[0] + '?refresh=1';
            }
        } catch(e) { this.handleError(); }
    }
    handleError() {
        this.failCount++;
        if (this.failCount >= 3) { clearInterval(this.timer); } // Stop polling after 3 failures
    }
}
document.addEventListener('DOMContentLoaded', () => new TransactionPolling().start());
// === Auto-dismiss alerts ===
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.auto-dismiss').forEach(alert => {
        const timeout = parseInt(alert.dataset.autoDismiss) || 5000;
        setTimeout(() => {
            // Check if element still exists in DOM
            if (!alert || !document.body.contains(alert)) return;
            
            // Manual fade out and remove (avoid Bootstrap Alert API issues)
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            
            setTimeout(() => {
                if (alert && alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 500);
        }, timeout);
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\inventaris-barang2\inventaris-kantor\resources\views/transaksi/index.blade.php ENDPATH**/ ?>