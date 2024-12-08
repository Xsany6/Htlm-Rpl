<?php $__env->startSection('content'); ?>

    <div class="row">
        <div class="col-6">
            <form action="<?php echo e(url('/postlogin')); ?>" method="post">
                <?php echo csrf_field(); ?>
                <?php if(Session::has('pesan')): ?>
                    <div class="alert alert-danger">
                        <?php echo e(Session::get('pesan')); ?>

                    </div>
                <?php endif; ?>
                <div class="mt-2">
                    <label class="form-label" for="">Email</label>
                    <input class="form-control" value="<?php echo e(old('email')); ?>" type="email" name="email" id="">
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
                <div class="mt-2">
                    <label class="form-label" for="">Password</label>
                    <input class="form-control" type="password" name="password" id="">
                    <span class="text-danger">
                        <?php $__errorArgs = ['password'];
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
                    <input class="btn btn-primary" type="submit" value="Login" name="submit">
                </div>
            </form>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('front', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/lampp/htdocs/resto-smk/resources/views/login.blade.php ENDPATH**/ ?>