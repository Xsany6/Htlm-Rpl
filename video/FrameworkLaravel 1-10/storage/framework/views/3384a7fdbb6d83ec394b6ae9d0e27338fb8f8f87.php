<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap.min.css')); ?>">
    <title>Resto SMK</title>
</head>
<body>
    <div class="container">
        <div class="mt-5">
            <nav class="navbar navbar-expand-lg bg-dark">
                <div class="container-fluid">
                    <a class="navbar-brand text-white" href="<?php echo e(url('/')); ?>">
                        <img style="height: 40px" src="<?php echo e(asset('20220305_051524.jpg')); ?>" alt="" srcset="" class="d-inline-block align-text-center">
                        Project-Resto_Laravel
                    </a>
                    <ul class="navbar-nav gap-5">
                        <?php if(session()->has('cart')): ?>
                            <li class="nav-item"><a class=" btn btn-info text-white" href="<?php echo e(url('cart')); ?>">Cart (
                                <?php
                                    $count = count(session('cart'));
                                    echo $count;
                                ?>
                            )</a></li>
                        <?php else: ?>
                            <li class="nav-item text-white btn btn-info">Cart</li>
                        <?php endif; ?>
                        <?php if(session()->missing('idpelanggan')): ?>
                            <li class="nav-item text-white"><a class="btn btn-primary" href="<?php echo e(url('register')); ?>">Register</a></li>
                            <li class="nav-item text-white"><a class="btn btn-success" href="<?php echo e(url('login')); ?>">Login</a></li>
                        <?php endif; ?>
                        <?php if(session()->has('idpelanggan')): ?>
                            <li class="nav-item text-white btn btn-primary"> <?php echo e(session('idpelanggan')['email']); ?> </li>
                            <li class="nav-item "><a href="<?php echo e(url('logout')); ?>" class="btn btn-danger">Logout</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>
        </div>
        <div class="row">
            <div class="col-2">
                <ul class="list-group">
                    <?php $__currentLoopData = $kategoris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="list-group-item"><a href="<?php echo e(url('show/'.$kategori->idkategori)); ?>"><?php echo e($kategori -> kategori); ?></a></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>

            </div>
            <div class="col-10 mt-5">
                <?php echo $__env->yieldContent('content'); ?>
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
<?php /**PATH /opt/lampp/htdocs/resto-smk/resources/views/front.blade.php ENDPATH**/ ?>