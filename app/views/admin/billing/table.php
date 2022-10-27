<table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal Billing</th>
            <th>Tanggal Simponi</th>
            <th>Department</th>
            <th>ID Billing</th>
            <th>Jumlah</th>
            <th>Status</th>
            <th>Error</th>
            <th>Error Pembayaran</th>
            <th>Tanggal Expired</th>
            <th>Tanggal Respon</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($billing as $key => $item) : ?>
        <tr>
            <td><span class="text-wrap"><?php echo $key+1 ?></span></td>
            <td><span class="text-wrap"><?php echo date('d M Y, H:i', strtotime($item->date_register)) ?></span></td>
            <td><span class="text-wrap"><?php echo date('d M Y, H:i', strtotime($item->date_simponi)) ?></span></td>
            <td><span class="text-wrap"><?php echo $item->department_name ?></span></td>
            <td><span class="text-wrap"><?php echo $item->billing_id ?></span></td>
            <td class="text-end"><span class="text-wrap"><?php echo number_format($item->total);?></span></td>
            <td><span class="text-wrap"><?php echo $item->status_name ?></span></td>
            <td><span class="text-wrap"><?php echo ($item->error !== null) ? $item->error : '-' ?></span></td>
            <td><span class="text-wrap"><?php echo ($item->error_pay !== null) ? $item->error_pay : '-' ?></span></td>
            <td><span class="text-wrap"><?php echo date('d M Y', strtotime($item->date_expired)) ?></span></td>
            <td><span class="text-wrap"><?php echo date('d M Y', strtotime($item->date_response)) ?></span></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Required datatable js -->
<script src="<?= base_url() ?>assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>

<!-- Responsive examples -->
<script src="<?= base_url() ?>assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?= base_url() ?>assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>

<!-- Datatable init js -->
<script src="<?= base_url() ?>assets/js/pages/datatables.init.js"></script>