<table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
    <thead>
        <tr>
            <th class="text-wrap">#</th>
            <th class="">Tgl Bayar</th>
            <th class="text-wrap">Aplikasi</th>
            <!-- <th class="text-wrap">ID Transaksi</th> -->
            <th class="text-wrap">Department</th>
            <th class="text-wrap">User</th>
            <th class="text-wrap">Detail</th>
            <th class="text-wrap">Jumlah</th>
            <th class="text-wrap">Status</th>
            <th class="text-wrap">ID Simponi</th>
            <th class="text-wrap">ID Billing</th>
            <th class="text-wrap">NTPN</th>
            <th class="text-wrap">NTB</th>
            <th class="text-wrap">Bank</th>
            <th class="text-wrap">Channel</th>
            <th class="text-wrap">Error Pembayaran</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($payment as $key => $item) : ?>
            <?php 
                foreach ($apps as $value) {
                    if ($value->id == $item->application_id) {
                        $application_name = $value->name;
                    }
                }

                foreach ($department as $value) {
                    if ($value->id == $item->department_id) {
                        $department_name = $value->name;
                    }
                }

                foreach ($user as $value) {
                    if ($value->id == $item->user_id) {
                        $user_name = $value->name;
                    }
                }

                foreach ($status as $value) {
                    if ($value->id == $item->status) {
                        $status_name = $value->name;
                    }
                }

                foreach ($bank as $value) {
                    if ($value->id == $item->bank_id) {
                        $bank_name = $value->name;
                    }
                }

                foreach ($reff as $value) {
                    if ($value->id == $item->channel) {
                        $channel = $value->name;
                    }
                }
            ?>
            <tr>
                <td class="text-wrap"><?php echo $key+1 ?></td>
                <td class="text-wrap"><?php echo date('d M Y, H:i', strtotime($item->date_simponi)) ?></td>
                <td class="text-wrap"><?php echo $application_name ?></td>
                <!-- <td class="text-wrap"><?php echo $item->transaction_id ?></td> -->
                <td class="text-wrap"><?php echo $department_name ?></td>
                <td class="text-wrap"><?php echo $user_name ?></td>
                <td class="text-wrap"><?php echo $item->detail ?></td>
                <td class="text-wrap text-end"><?php echo number_format($item->total) ?></td>
                <td class="text-wrap"><?php echo $status_name ?></td>
                <td class="text-wrap"><?php echo $item->simponi_id ?></td>
                <td class="text-wrap"><?php echo $item->billing_id ?></td>
                <td class="text-wrap"><?php echo $item->ntpn ?></td>
                <td class="text-wrap"><?php echo $item->ntb ?></td>
                <td class="text-wrap"><?php echo $bank_name ?></td>
                <td class="text-wrap"><?php echo $channel ?></td>
                <td class="text-wrap">
                    <?php 
                        if ($item->error_pay == NULL) {
                            echo "-";
                        } else {
                            echo $item->error_pay;
                        }
                        
                    ?>
                </td>
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