<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap.min.css')); ?>">
    <title>Resto SMK Login Admin</title>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-4">
                <h1 class="text-center">
                    Login Admin
                </h1>
                <form action="<?php echo e(url('admin/postlogin')); ?>" method="post">
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
    </div>
    <script src="<?php echo e(asset('js/bootstrap.bundle.min.js')); ?>"></script>
</body>
</html>
<?php /**PATH /opt/lampp/htdocs/resto-smk/resources/views/Backends/login.blade.php ENDPATH**/ ?>