<?php $__env->startSection('admincontent'); ?>
<div>
    <h2>Insert Data</h2>
</div>
<div class="row">
    <div class="col-6">
        <form action="<?php echo e(url('admin/menu/'.$menu->idmenu)); ?>" method="post" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <select class="form-select" name="idkategori" id="">
                <?php $__currentLoopData = $kategoris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($kategori->idkategori); ?>"><?php echo e($kategori->kategori); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <div class="mt-2">
                <label class="form-label" for="">Menu</label>
                <input class="form-control" value="<?php echo e($menu->menu); ?>" type="text" name="menu" id="">
                <span class="text-danger">
                    <?php $__errorArgs = ['menu'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <?php echo e($message); ?>

                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </span>
            </div>
            <div class="mt-2">
                <label class="form-label" for="">Deskripsi</label>
                <input class="form-control" value="<?php echo e($menu->deskripsi); ?>" type="text" name="deskripsi" id="">
                <span class="text-danger">
                    <?php $__errorArgs = ['deskripsi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <?php echo e($message); ?>

                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </span>
            </div>
            <div class="mt-2">
                <label class="form-label" for="">Gambar</label>
                <input class="form-control" value="<?php echo e($menu->gambar); ?>" type="file" name="gambar" id="">
                <span class="text-danger">
                    <?php $__errorArgs = ['gambar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <?php echo e($message); ?>

                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </span>
            </div>
            <div class="mt-2">
                <label class="form-label" for="">Harga</label>
                <input class="form-control" value="<?php echo e($menu->harga); ?>" type="number" name="harga" id="">
                <span class="text-danger">
                    <?php $__errorArgs = ['harga'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <?php echo e($message); ?>

                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </span>
            </div>
            <div class="mt-4">
                <button class="btn btn-primary" type="submit">Save</button>
            </div>
        </form>
    </div>
</div>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('Backends.back', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/lampp/htdocs/resto-smk/resources/views/Backends/Menu/update.blade.php ENDPATH**/ ?>