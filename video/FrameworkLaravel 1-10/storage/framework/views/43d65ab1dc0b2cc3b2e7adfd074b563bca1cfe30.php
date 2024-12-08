<?php $__env->startSection('admincontent'); ?>
    <div>
        <h1>kategori</h1>
    </div>
    <div>
        <a href="<?php echo e(url('admin/kategori/create')); ?>" class="btn btn-primary">New Data</a>
    </div>
    <div>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kategori</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <?php
                $no = 1;
            ?>
            <tbody>
                <?php $__currentLoopData = $kategoris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($no++); ?></td>
                        <td><?php echo e($kategori->kategori); ?></td>
                        <td><a class="btn btn-info text-white" href="<?php echo e(url('admin/kategori/'.$kategori->idkategori.'/edit')); ?>">Edit</a></td>
                        <td><a class="btn btn-danger" href="<?php echo e(url('admin/kategori/'.$kategori->idkategori)); ?>">Delete</a></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('Backends.back', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/lampp/htdocs/resto-smk/resources/views/Backends/Kategori/select.blade.php ENDPATH**/ ?>