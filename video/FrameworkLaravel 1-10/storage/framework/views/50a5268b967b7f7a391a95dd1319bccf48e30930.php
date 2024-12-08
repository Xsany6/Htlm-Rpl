<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap.min.css')); ?>">
    <title>Resto SMK | Admin Page</title></head>
<body>
    <div class="container">
        <div class="mt-4">
            <nav class="navbar navbar-expand-lg bg-dark">
                <div class="container-fluid">
                    <h2 class="text-white">Admin Page</h2>
                    <ul class="navbar-nav gap-5">
                        <li class="nav-item text-white">
                            <?php echo e(Auth::user()->email); ?>

                        </li>
                        <li class="nav-item text-white">
                            Level: <?php echo e(Auth::user()->level); ?>

                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(url('admin/logout')); ?>" class="text-white btn btn-danger">Logout</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
        <div class="row mt-5">
            <div class="col-2">
                <ul class="list-group">
                    <?php if(Auth::user()->level == 'admin'): ?>
                        <li class="list-group-item"><a href="<?php echo e(url('admin/user')); ?>">User</a></li>
                    <?php elseif(Auth::user()->level == 'kasir'): ?>
                        <li class="list-group-item"><a href="">Order</a></li>
                        <li class="list-group-item"><a href="">Order Detail</a></li>
                    <?php elseif(Auth::user()->level == 'manager'): ?>
                        <li class="list-group-item"><a href="<?php echo e(url('admin/kategori')); ?>">Kategori</a></li>
                        <li class="list-group-item"><a href="<?php echo e(url('admin/menu')); ?>">Menu</a></li>
                        <li class="list-group-item"><a href="<?php echo e(url('admin/pelanggan')); ?>">Pelanggan</a></li>
                        <li class="list-group-item"><a href="<?php echo e(url('admin/order')); ?>">Order</a></li>
                        <li class="list-group-item"><a href="<?php echo e(url('admin/orderdetail')); ?>">Order Detail</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="col-10">
                <?php echo $__env->yieldContent("admincontent"); ?>
            </div>
        </div>
        <div>
            <footer class="row mt-5">
                <div class="col">
                    <p class="text-center">2022 - Copyright@Mangaonlen.com</p>
                </div>
            </footer>
        </div>
    </div>
    <script src="<?php echo e(asset('js/bootstrap.bundle.min.js')); ?>"></script>
</body>
</html>
<?php /**PATH /opt/lampp/htdocs/resto-smk/resources/views/Backends/back.blade.php ENDPATH**/ ?>