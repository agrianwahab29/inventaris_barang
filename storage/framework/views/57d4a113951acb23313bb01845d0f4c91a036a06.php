<?php $__env->startSection('title', 'Edit Transaksi'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Edit Transaksi</h1>
        <a href="<?php echo e(route('transaksi.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Form Edit Transaksi #<?php echo e($transaksi->id); ?></h5>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('transaksi.update', $transaksi)); ?>" method="POST" id="formEditTransaksi">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="barang_id" class="form-label">Barang <span class="text-danger">*</span></label>
                            <select class="form-select <?php $__errorArgs = ['barang_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="barang_id" name="barang_id" required>
                                <option value="">Pilih Barang</option>
                                <?php $__currentLoopData = $barangs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $barang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($barang->id); ?>" data-satuan="<?php echo e($barang->satuan); ?>" data-stok="<?php echo e($barang->stok); ?>"
                                        <?php echo e(old('barang_id', $transaksi->barang_id) == $barang->id ? 'selected' : ''); ?>>
                                        <?php echo e($barang->nama_barang); ?> (Stok: <?php echo e($barang->stok); ?> <?php echo e($barang->satuan); ?>)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['barang_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="alert alert-info" id="stokInfo">
                            <strong>Stok Saat Ini:</strong> <span id="stokSaatIni"><?php echo e($transaksi->barang->stok ?? 0); ?></span> <span id="satuanBarang"><?php echo e($transaksi->barang->satuan ?? ''); ?></span>
                        </div>

                        <div class="mb-3">
                            <label for="jumlah_masuk" class="form-label">Jumlah Masuk</label>
                            <div class="input-group">
                                <input type="number" class="form-control <?php $__errorArgs = ['jumlah_masuk'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    id="jumlah_masuk" name="jumlah_masuk" min="0" 
                                    value="<?php echo e(old('jumlah_masuk', $transaksi->jumlah_masuk)); ?>">
                                <span class="input-group-text" id="satuanMasuk"><?php echo e($transaksi->barang->satuan ?? 'Buah'); ?></span>
                            </div>
                            <?php $__errorArgs = ['jumlah_masuk'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                            <input type="date" class="form-control <?php $__errorArgs = ['tanggal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                id="tanggal" name="tanggal" required
                                value="<?php echo e(old('tanggal', $transaksi->tanggal->format('Y-m-d'))); ?>">
                            <?php $__errorArgs = ['tanggal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control <?php $__errorArgs = ['keterangan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                id="keterangan" name="keterangan" rows="3"><?php echo e(old('keterangan', $transaksi->keterangan)); ?></textarea>
                            <?php $__errorArgs = ['keterangan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="jumlah_keluar" class="form-label">Jumlah Keluar</label>
                            <div class="input-group">
                                <input type="number" class="form-control <?php $__errorArgs = ['jumlah_keluar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    id="jumlah_keluar" name="jumlah_keluar" min="0"
                                    value="<?php echo e(old('jumlah_keluar', $transaksi->jumlah_keluar)); ?>">
                                <span class="input-group-text" id="satuanKeluar"><?php echo e($transaksi->barang->satuan ?? 'Buah'); ?></span>
                            </div>
                            <?php $__errorArgs = ['jumlah_keluar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Tanggal Keluar - Selalu tampil dan bisa diedit -->
                        <div class="mb-3" id="tanggalKeluarDiv">
                            <label for="tanggal_keluar" class="form-label">Tanggal Keluar</label>
                            <input type="date" class="form-control <?php $__errorArgs = ['tanggal_keluar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                id="tanggal_keluar" name="tanggal_keluar"
                                value="<?php echo e(old('tanggal_keluar', $transaksi->tanggal_keluar ? $transaksi->tanggal_keluar->format('Y-m-d') : '')); ?>"
                                placeholder="Kosongkan jika tidak ada">
                            <small class="text-muted">Kosongkan jika barang tidak keluar</small>
                            <?php $__errorArgs = ['tanggal_keluar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <input type="hidden" name="tipe_pengambil" value="nama_ruangan">

                        <div class="mb-3" id="ruanganDiv">
                            <label for="ruangan_id" class="form-label">Ruangan Tujuan <span class="text-danger">*</span></label>
                            <select class="form-select <?php $__errorArgs = ['ruangan_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="ruangan_id" name="ruangan_id">
                                <option value="">Pilih Ruangan...</option>
                                <?php $__currentLoopData = $ruangans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ruangan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($ruangan->id); ?>" 
                                        <?php echo e(old('ruangan_id', $transaksi->ruangan_id) == $ruangan->id ? 'selected' : ''); ?>>
                                        <?php echo e($ruangan->nama_ruangan); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['ruangan_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="mb-3" id="namaPengambilDiv">
                            <label for="nama_pengambil" class="form-label">Nama Pengambil <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php $__errorArgs = ['nama_pengambil'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                id="nama_pengambil" name="nama_pengambil" placeholder="Masukkan nama pengambil"
                                value="<?php echo e(old('nama_pengambil', $transaksi->nama_pengambil)); ?>">
                            <?php $__errorArgs = ['nama_pengambil'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-between">
                    <a href="<?php echo e(route('transaksi.index')); ?>" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const barangSelect = document.getElementById('barang_id');
    
    function updateSatuan() {
        if (barangSelect && barangSelect.selectedOptions.length > 0 && barangSelect.value) {
            const selectedOption = barangSelect.selectedOptions[0];
            const satuan = selectedOption.getAttribute('data-satuan') || 'Buah';
            const stok = selectedOption.getAttribute('data-stok') || 0;
            
            const elMasuk = document.getElementById('satuanMasuk');
            const elKeluar = document.getElementById('satuanKeluar');
            const elBarang = document.getElementById('satuanBarang');
            const elStok = document.getElementById('stokSaatIni');
            
            if (elMasuk) elMasuk.textContent = satuan;
            if (elKeluar) elKeluar.textContent = satuan;
            if (elBarang) elBarang.textContent = satuan;
            if (elStok) elStok.textContent = stok;
        }
    }
    
    // Event listeners
    if (barangSelect) barangSelect.addEventListener('change', updateSatuan);
    
    // Initial check
    updateSatuan();
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\inventaris-barang2\inventaris-kantor\resources\views/transaksi/edit.blade.php ENDPATH**/ ?>