<?php $__env->startSection('admincontent'); ?>
    <div>
        <h1>Order Details</h1>
    </div>
    <div>
        <form action="<?php echo e(url('admin/orderdetail/create')); ?>" method="get">
            <div class="row">
                <div class="col-4 mt-5">
                    <label class="form-label" for="">Tanggal awal</label>
                    <input class="form-control" type="date" name="tglawal" id="">
                </div>
                <div class="col-4 mt-5">
                    <label class="form-label" for="">Tanggal akhir</label>
                    <input class="form-control" type="date" name="tglakhir" id="">
                </div>
                <div class='col-4 mt-5'>
                    <input class="btn btn-primary" type="submit" value="Search">
                </div>
            </div>
        </form>
    </div>
    <div>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Pelanggan</th>
                    <th>Menu</th>
                    <th>Jumlah</th>
                    <th>Harga</th>
                    <th>Total</th>
                </tr>
            </thead>
            <?php
                $no = 1;
            ?>
            <tbody>
                <?php $__currentLoopData = $details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($no++); ?></td>
                        <td><?php echo e($detail->tglorder); ?></td>
                        <td><?php echo e($detail->pelanggan); ?></td>
                        <td><?php echo e($detail->menu); ?></td>
                        <td><?php echo e($detail->jumlah); ?></td>
                        <td><?php echo e($detail->harga); ?></td>
                        <td><?php echo e($detail->total); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center mt-5">
        <?php echo e($details->withQueryString()->links()); ?>

    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('Backends.back', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/lampp/htdocs/resto-smk/resources/views/Backends/Detail/select.blade.php ENDPATH**/ ?>