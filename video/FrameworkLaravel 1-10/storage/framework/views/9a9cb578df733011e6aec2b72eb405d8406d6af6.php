<?php $__env->startSection('admincontent'); ?>
    <div>
        <h1>User</h1>
    </div>
    <div>
        <a href="<?php echo e(url('admin/user/create')); ?>" class="btn btn-primary">New Data</a>
    </div>
    <div>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>No</th>
                    <th>user</th>
                    <th>Email</th>
                    <th>Level</th>
                    <th>Ubah</th>
                    <th>Hapus</th>
                </tr>
            </thead>
            <?php
                $no = 1;
            ?>
            <tbody>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($no++); ?></td>
                        <td><?php echo e($user->name); ?></td>
                        <td><?php echo e($user->email); ?></td>
                        <td><?php echo e($user->level); ?></td>
                        <td><a class="btn btn-info text-white" href="<?php echo e(url('admin/user/'.$user->id.'/edit')); ?>">Edit</a></td>
                        <td><a class="btn btn-danger" href="<?php echo e(url('admin/user/'.$user->id)); ?>">Delete</a></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('Backends.back', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/lampp/htdocs/resto-smk/resources/views/Backends/User/select.blade.php ENDPATH**/ ?>