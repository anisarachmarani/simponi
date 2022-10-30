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
            <td>
                <span class="text-wrap">
                    <a href="javascript:void(0)" data-bs-toggle="modal" class="view_data" data-bs-target=".bs-example-modal-lg" type="button" id="<?php echo $item->id ?>">
                        <?php echo $item->billing_id ?>
                    </a>
                </span>
            </td>
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

<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" id="detail">
        
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Required datatable js -->
<script src="<?= base_url() ?>assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>

<!-- Responsive examples -->
<script src="<?= base_url() ?>assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?= base_url() ?>assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>

<!-- Datatable init js -->
<script src="<?= base_url() ?>assets/js/pages/datatables.init.js"></script>

<script>
    $(document).ready(function() {
        $('.view_data').on('click',function(){
            var idBilling = $(this).attr("id");
            $("#loading").html('<div class="spinner-border text-primary m-1 col-12" role="status"></div>');
            $.post(document.URL + "/detail_billing/" , {
                id: idBilling,
            }, function(data) {
                $('#detail').html(data);
                $("#loading").html('');
            });
        })
    })
</script>