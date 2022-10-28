<?php 
    foreach ($department as $value) {
        if ($value->id == $billing->department_id) {
            $department_id = $value->name;
        }
    }
    
    foreach ($application as $value) {
        if ($value->id == $billing->application_id) {
            $application_id = $value->name;
        }
    }
    
    foreach ($user as $value) {
        if ($value->id == $billing->user_id) {
            $user_name = $value->name;
        }
    }
    
    foreach ($status as $value) {
        if ($value->id == $billing->status) {
            $status_id = $value->name;
        }
    }
?>
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="myLargeModalLabel">Detail Billing</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body row my-3">
        <div class="col-6">
            <div class="row">
                <p class="col-4">Department</p>
                <p class="col-auto">:</p>
                <p class="col-7 fw-bold"><?= $department_id; ?></p>
            </div>
            <div class="row">
                <p class="col-4">Aplikasi</p>
                <p class="col-auto">:</p>
                <p class="col-7 fw-bold"><?= $application_id ?></p>
            </div>
            <div class="row">
                <p class="col-4">User</p>
                <p class="col-auto">:</p>
                <p class="col-7 fw-bold"><?= $user_name ?></p>
            </div>
        </div>
        <div class="col-6">
            <div class="row">
                <p class="col-4">Tanggal Billing</p>
                <p class="col-auto">:</p>
                <p class="col-7 fw-bold"><?= date('d M Y, H:i', strtotime($billing->date_register)) ?></p>
            </div>
            <div class="row">
                <p class="col-4">Id Billing</p>
                <p class="col-auto">:</p>
                <p class="col-7 fw-bold"><?= $billing->billing_id; ?></p>
            </div>
            <div class="row">
                <p class="col-4">Id Simponi</p>
                <p class="col-auto">:</p>
                <p class="col-7 fw-bold"><?= $billing->simponi_id; ?></p>
            </div>
        </div>
        <div class="col-12 mt-3">
            <div class="table-responsive">
                <table class="table mb-0 table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Detail</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $billing->detail; ?></td>
                            <td class="text-end"><?= number_format($billing->total); ?></td>
                            <td><?= $status_id; ?></td>
                            <td><?= ($billing->error_pay != null) ? $billing->error_pay : '' ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div><!-- /.modal-content -->