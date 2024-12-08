<?php $__env->startSection('admincontent'); ?>
    <div>
        <h1>Menu</h1>
    </div>
    <div>
        <a href="<?php echo e(url('admin/menu/create')); ?>" class="btn btn-primary">New Data</a>
    </div>
    <div class="row mt-4">
        <div class="col-4">
            <form action="<?php echo e(url('admin/select')); ?>" method="get">
                <select class="form-select" name="idkategori" id="" onchange="this.form.submit()">
                    <option value="">-- Pilih Kategori --</option>
                    <?php $__currentLoopData = $kategoris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($kategori->idkategori); ?>"><?php echo e($kategori->kategori); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </form>
        </div>
    </div>
    <div>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kategori</th>
                    <th>Menu</th>
                    <th>Deskripsi</th>
                    <th>Gambar</th>
                    <th>Harga</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <?php
                $no = 1;
            ?>
            <tbody>
                <?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($no++); ?></td>
                        <td><?php echo e($menu->kategori); ?></td>
                        <td><?php echo e($menu->menu); ?></td>
                        <td><?php echo e($menu->deskripsi); ?></td>
                        <td><img width="100px" src="<?php echo e(asset('img/'.$menu->gambar)); ?>" alt=""></td>
                        <td><?php echo e($menu->harga); ?></td>
                        <td><a class="btn btn-info text-white" href="<?php echo e(url('admin/menu/'.$menu->idmenu.'/edit')); ?>">Edit</a></td>
                        <td><a class="btn btn-danger" href="<?php echo e(url('admin/menu/'.$menu->idmenu)); ?>">Delete</a></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center mt-5">
        <?php echo e($menus->withQueryString()->links()); ?>

    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('Backends.back', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/lampp/htdocs/resto-smk/resources/views/Backends/Menu/select.blade.php ENDPATH**/ ?>