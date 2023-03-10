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
                                <h4 class="mb-sm-0 font-size-18">Billing</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin/Dashboard') ?>">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Billing</li>
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
                                        <h4 class="card-title col-6">Daftar Billing</h4>
                                        <div class="col-6 text-end">
                                            <!-- <a href="" class="btn btn-primary">Tambah <span class="d-none d-md-inline">Billing</span></a> -->
                                        </div>
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
                                                                <label for="department" class="col-sm-3 col-form-label">Department</label>
                                                                <div class="col-sm-9">
                                                                    <select class="form-control" data-trigger name="department" id="department">
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
                                                                <label for="datepicker-basic" class="col-sm-3 col-form-label">Tanggal Billing</label>
                                                                <div class="col-sm-9">
                                                                    <input type="date" class="form-control" id="date_register" name="date_register" value="<?php echo $this->session->userdata('date_register') ?>">
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

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="row mb-4">
                                                                <label for="date_simponi" class="col-sm-3 col-form-label">Tanggal Simponi</label>
                                                                <div class="col-sm-9">
                                                                    <input type="date" class="form-control" id="date_simponi" name="date_simponi" value="<?php echo $this->session->userdata('date_simponi') ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="mt-1">
                                                        <button type="button" class="btn btn-primary w-md" id="btn_cari">Cari</button>
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
            data_billing();

            function data_billing() {
                $("#loading").html('<div class="spinner-border text-primary m-1 col-12" role="status"></div>');
                $.post(document.URL + "/data_billing/", {
                    // thnpengadaan: '',
                    // blnpengadaan: '',
                    // ipska: '',
                }, function(data) {
                    $('#data').html(data);
                    $("#loading").html('');
                });
            }
        })
        $('#btn_cari').on('click', function() {
            $("#loading").html('<div class="spinner-border text-primary m-1 col-12" role="status"></div>');
            $.post(document.URL + "/data_billing/", {
                department: $('#department').val(),
                billing_id: $('#billing_id').val(),
                date_register: $('#date_register').val(),
                status: $('#status').val(),
                date_simponi: $('#date_simponi').val(),
            }, function(data) {
                $('#data').html(data);
                $("#loading").html('');
            });
        })
    </script>

</body>

</html>