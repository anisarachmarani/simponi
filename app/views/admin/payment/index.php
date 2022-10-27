<!doctype html>
<html lang="en">

<head>
    <?php $this->load->view("admin/partials/head.php") ?>
    <?php $this->load->view("admin/partials/css.php") ?>
</head>

<body>

    <!-- <body data-layout="horizontal"> -->

    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php $this->load->view("admin/partials/header.php") ?>
        
        <?php $this->load->view("admin/partials/menu.php") ?>
        
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">

                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center
                                justify-content-between">
                                <h4 class="mb-sm-0 font-size-18">Payment</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin/Dashboard') ?>">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Payment</li>
                                    </ol>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row justify-content-between align-items-center">
                                        <h4 class="card-title col-6">Daftar Payment</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <div>
                                                <form action="#" method="post">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="row mb-4">
                                                                <label for="department_id" class="col-sm-3 col-form-label">Department</label>
                                                                <div class="col-sm-9">
                                                                    <select class="form-control" data-trigger name="department_id" id="department_id">
                                                                        <option value="">-- Semua department --</option>
                                                                        <?php foreach ($department as $key => $value) : ?>
                                                                            <option value="<?= $value->id; ?>"><?= $value->name; ?></option>
                                                                        <?php endforeach ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row mb-4">
                                                                <label for="billing_id" class="col-sm-3 col-form-label">ID Billing</label>
                                                                <div class="col-sm-9">
                                                                    <input type="text" class="form-control" id="billing_id" name="billing_id" value="<?php echo $this->session->userdata('billing_id') ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="row mb-4">
                                                                <label for="application_id" class="col-sm-3 col-form-label">Aplikasi</label>
                                                                <div class="col-sm-9">
                                                                    <select class="form-control" data-trigger name="application_id" id="application_id">
                                                                        <option value="">-- Semua aplikasi --</option>
                                                                        <?php foreach ($apps as $key => $value) : ?>
                                                                            <option value="<?= $value->id; ?>"><?= $value->name; ?></option>
                                                                        <?php endforeach ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row mb-4">
                                                                <label for="transaction_id" class="col-sm-3 col-form-label">ID Transaksi</label>
                                                                <div class="col-sm-9">
                                                                    <input type="text" class="form-control" id="transaction_id" name="transaction_id" value="<?php echo $this->session->userdata('billing_id') ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="row mb-4">
                                                                <label for="user_id" class="col-sm-3 col-form-label">User</label>
                                                                <div class="col-sm-9">
                                                                    <select class="form-control" data-trigger name="user_id" id="user_id">
                                                                        <option value="">-- Semua User --</option>
                                                                        <?php foreach ($user as $key => $value) : ?>
                                                                            <option value="<?= $value->id; ?>"><?= $value->name; ?></option>
                                                                        <?php endforeach ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row mb-4">
                                                                <label for="status_label" class="col-sm-3 col-form-label">Status</label>
                                                                <div class="col-sm-9">
                                                                <select class="form-control" data-trigger id="status" name="status" placeholder="Test">
                                                                    <option value="">-- Semua Status --</option>
                                                                    <?php foreach ($status as $key => $value) : ?>
                                                                        <option value="<?= $value->id ?>"><?= $value->name ?></option>
                                                                    <?php endforeach ?>
                                                                </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="mt-1">
                                                        <button type="button"
                                                            class="btn btn-primary w-md" id="btn_cari">Cari</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row d-flex justify-content-center" id="loading"></div>
                                    
                                    <div class="row mt-5">
                                        <div class="col-12" id="data"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->
                </div>
                <!-- container-fluid -->
            </div>
        </div>
        
        <?php $this->load->view("admin/partials/footer.php") ?>

        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <?php $this->load->view("admin/partials/sidebar.php") ?>
    
    <?php $this->load->view("admin/partials/script.php") ?>

    <script>
        $(document).ready(function() {
            data_payment();
            function data_payment(){
                $("#loading").html('<div class="spinner-border text-primary m-1 col-12" role="status"></div>');
                $.post(document.URL + "/data_payment/" , {
                }, function(data) {
                    $('#data').html(data);
                    $("#loading").html('');
                });                    
            }
        })
        $('#btn_cari').on('click',function(){
            $("#loading").html('<div class="spinner-border text-primary m-1 col-12" role="status"></div>');
            $.post(document.URL + "/data_payment/" , {
                department_id: $('#department_id').val(),
                billing_id: $('#billing_id').val(),
                application_id: $('#application_id').val(),
                transaction_id: $('#transaction_id').val(),
                user_id: $('#user_id').val(),
                status: $('#status').val(),
            }, function(data) {
                $('#data').html(data);
                $("#loading").html('');
            });
        })
    </script>

</body>

</html>