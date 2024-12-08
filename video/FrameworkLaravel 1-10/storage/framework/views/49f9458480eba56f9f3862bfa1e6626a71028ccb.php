<?php $__env->startSection('admincontent'); ?>
<div>
    <h2>Insert Data</h2>
</div>
<div class="row">
    <div class="col-6">
        <form action="<?php echo e(url('admin/user/'.$users->id)); ?>" method="post" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <select class="form-select" name="level" id="">
                <option value="admin">Admin</option>
                <option value="kasir">Kasir</option>
                <option value="manager">Manager</option>
                <option value="koki">Koki</option>
            </select>
            <div class="mt-2">
                <label class="form-label" for="">Nama</label>
                <input class="form-control" value="<?php echo e($users->name); ?>" type="text" name="name" id="">
                <span class="text-danger">
                    <?php $__errorArgs = ['mene'];
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
                <label class="form-label" for="">Email</label>
                <input class="form-control" value="<?php echo e($users->email); ?>" type="email" name="email" id="">
                <span class="text-danger">
                    <?php $__errorArgs = ['email'];
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

<?php echo $__env->make('Backends.back', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/lampp/htdocs/resto-smk/resources/views/Backends/User/update.blade.php ENDPATH**/ ?>