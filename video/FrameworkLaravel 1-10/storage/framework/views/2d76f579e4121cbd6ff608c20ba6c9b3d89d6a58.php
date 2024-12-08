<?php $__env->startSection('admincontent'); ?>
    <div>
        <h1>Pelanggan</h1>
    </div>
    
    <div>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Gender</th>
                    <th>Alamat</th>
                    <th>Telp</th>
                    <th>Email</th>
                    <th>Aktif</th>
                </tr>
            </thead>
            <?php
                $no = 1;
            ?>
            <tbody>
                <?php $__currentLoopData = $pelanggans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pelanggan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($no++); ?></td>
                        <td><?php echo e($pelanggan->pelanggan); ?></td>
                        <td><?php echo e($pelanggan->jeniskelamin); ?></td>
                        <td><?php echo e($pelanggan->alamat); ?></td>
                        <td><?php echo e($pelanggan->telp); ?></td>
                        <td><?php echo e($pelanggan->email); ?></td>
                        <?php
                            if($pelanggan->aktif == 1) {
                                $aktif = '<a href="'.url('admin/pelanggan/'.$pelanggan->idpelanggan).'"> Aktif </a>';
                            } else {
                                $aktif = '<a href="'.url('admin/pelanggan/'.$pelanggan->idpelanggan).'"> Banned </a>';
                            }
                        ?>
                        <td><?php echo $aktif; ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center mt-5">
        <?php echo e($pelanggans->withQueryString()->links()); ?>

    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('Backends.back', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/lampp/htdocs/resto-smk/resources/views/Backends/Pelanggan/select.blade.php ENDPATH**/ ?>