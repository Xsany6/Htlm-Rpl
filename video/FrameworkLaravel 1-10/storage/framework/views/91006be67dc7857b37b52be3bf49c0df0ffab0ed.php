<?php $__env->startSection('admincontent'); ?>
    <div>
        <h1>Order Detail</h1>
    </div>
    <div>
        <h2>Pelanggan : <?php echo e($orders[0] ['pelanggan']); ?></h2>
        <h2>Total : <?php echo e(number_format($orders [0]['total'])); ?></h2>
    </div>
    <div>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Menu</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Total</th>
                </tr>
            </thead>
            <?php
                $no = 1;
            ?>
            <tbody>
                <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($no++); ?></td>
                        <td><?php echo e($order->menu); ?></td>
                        <td><?php echo e($order->harga); ?></td>
                        <td><?php echo e($order->jumlah); ?></td>
                        <td><?php echo e($order->jumlah * $order->harga); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('Backends.back', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/lampp/htdocs/resto-smk/resources/views/Backends/Order/details.blade.php ENDPATH**/ ?>