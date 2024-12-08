<?php $__env->startSection('content'); ?>
    <div class="row">
        <?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card mx-2 mb-3" style="width: 18rem;">
                <img style="height: 300px" src="<?php echo e(asset('img/'.$menu->gambar)); ?>" class="card-img-top" alt="<?php echo e($menu->gambar); ?>">
                <div class="card-body">
                    <h5 class="card-title"><?php echo e($menu->menu); ?></h5>
                    <p class="card-text"><?php echo e($menu->deskripsi); ?></p>
                    <h5 class="card-title"><?php echo e($menu->harga); ?></h5>
                    <a href="<?php echo e(url('beli/'.$menu->idmenu)); ?>" class="btn btn-primary">Buy Now</a>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <div class="d-flex justify-content-center mt-5">
        <?php echo e($menus -> links()); ?>

    </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('front', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/lampp/htdocs/resto-smk/resources/views/menu.blade.php ENDPATH**/ ?>