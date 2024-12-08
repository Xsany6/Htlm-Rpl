<?php $__env->startSection('admincontent'); ?>

<div class="row">
    <div>
        <h1><?php echo e(number_format($order->total)); ?></h1>
    </div>
    <div class="col-6">
        <form action="<?php echo e(url('admin/order/'.$order->idorder)); ?>" method="post">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="mt-2">
                <label class="form-label" for="">Order</label>
                <input class="form-control" min="<?php echo e($order->total); ?>" value="<?php echo e($order->total); ?>" type="text" name="bayar" id="">
                <span class="text-danger">
                    <?php $__errorArgs = ['kategori'];
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
                <button type="submit" class="btn btn-primary">Bayar</button>
            </div>
        </form>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('Backends.back', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/lampp/htdocs/resto-smk/resources/views/Backends/Order/update.blade.php ENDPATH**/ ?>