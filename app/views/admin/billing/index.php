<!doctype html>
<html lang="en">

<head>
    <?php $this->load->view("admin/partials/head.php") ?>
    <!-- DataTables -->
    <link href="<?php echo base_url() ?>assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url() ?>assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url() ?>assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <!-- choices css -->
    <link href="<?php echo base_url() ?>assets/libs/choices.js/public/assets/styles/choices.min.css" rel="stylesheet" type="text/css" />

    <!-- color picker css -->
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/libs/@simonwep/pickr/themes/classic.min.css" />
    <!-- 'classic' theme -->
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/libs/@simonwep/pickr/themes/monolith.min.css" />
    <!-- 'monolith' theme -->
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/libs/@simonwep/pickr/themes/nano.min.css" />
    <!-- 'nano' theme -->

    <!-- datepicker css -->
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/libs/flatpickr/flatpickr.min.css">
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
                                        <li class="breadcrumb-item"><a href="<?php echo site_url('index.php/admin') ?>">Dashboard</a></li>
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
                                                <h5 class="font-size-14 mb-4">Form groups</h5>
                                                <form action="<?php echo site_url('index.php/admin/Billing/index') ?>" method="post">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="row mb-4">
                                                                <label for="detail" class="col-sm-3 col-form-label">Nama</label>
                                                                <div class="col-sm-9">
                                                                    <input type="text" class="form-control" id="detail" name="detail" value="<?php echo $this->session->userdata('detail') ?>">
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
                                                                    <input type="date" class="form-control" id="datepicker-basic" name="date_register" value="<?php echo $this->session->userdata('date_register') ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row mb-4">
                                                                <label for="status_label" class="col-sm-3 col-form-label">Status</label>
                                                                <div class="col-sm-9">
                                                                <select class="form-control" data-trigger name="status" id="choices-single-default" placeholder="Test">
                                                                    <option value="">-- Semua Status --</option>
                                                                    <option <?php echo ($this->session->userdata('status') == 'Baru') ? 'selected' : '' ?> value="Baru">Baru</option>
                                                                    <option <?php echo ($this->session->userdata('status') == 'Terkirim') ? 'selected' : '' ?> value="Terkirim">Terkirim</option>
                                                                    <option <?php echo ($this->session->userdata('status') == 'Kirim Ulang') ? 'selected' : '' ?> value="Kirim Ulang">Kirim Ulang</option>
                                                                    <option <?php echo ($this->session->userdata('status') == 'Tidak Terkirim') ? 'selected' : '' ?> value="Tidak Terkirim">Tidak Terkirim</option>
                                                                    <option <?php echo ($this->session->userdata('status') == 'Belum Terbayar') ? 'selected' : '' ?> value="Belum Terbayar">Belum Terbayar</option>
                                                                    <option <?php echo ($this->session->userdata('status') == 'Terbayarar') ? 'selected' : '' ?> value="Terbayar">Terbayar</option>
                                                                    <option <?php echo ($this->session->userdata('status') == 'Selesai') ? 'selected' : '' ?> value="Selesai">Selesai</option>
                                                                    <option <?php echo ($this->session->userdata('status') == 'Tidak Berlaku') ? 'selected' : '' ?> value="Tidak Berlaku">Tidak Berlaku</option>
                                                                </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="row mb-4">
                                                                <label for="datepicker-basic" class="col-sm-3 col-form-label">Tanggal Bayar</label>
                                                                <div class="col-sm-9">
                                                                    <input type="date" class="form-control" id="datepicker-basic" name="date_simponi" value="<?php echo $this->session->userdata('date_simponi') ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="mt-1">
                                                        <button type="submit"
                                                            class="btn btn-primary w-md">Cari</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th class="text-wrap">No</th>
                                                <th class="text-wrap">Nama</th>
                                                <th class="text-wrap">ID Billing</th>
                                                <th class="text-wrap">Tanggal Billing</th>
                                                <th class="text-wrap">Tanggal Bayar</th>
                                                <th class="text-wrap">Jumlah</th>
                                                <th class="text-wrap">Status</th>
                                                <th class="text-wrap">Error</th>
                                                <th class="text-wrap">Error Pembayaran</th>
                                                <!-- <th class="text-wrap">Aksi</th> -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($billing as $key => $item) : ?>
                                            <tr>
                                                <td><span class="text-wrap"><?php echo $key+1 ?></span></td>
                                                <td><span class="text-wrap"><?php echo $item->detail ?></span></td>
                                                <td><span class="text-wrap"><?php echo $item->billing_id ?></span></td>
                                                <td><span class="text-wrap"><?php echo date('d M Y', strtotime($item->date_register)) ?></span></td>
                                                <td><span class="text-wrap"><?php echo date('d M Y', strtotime($item->date_simponi)) ?></span></td>
                                                <td class="text-end"><span class="text-wrap"><?php echo number_format($item->total);?></span></td>
                                                <td><span class="text-wrap"><?php echo $item->name ?></span></td>
                                                <td><span class="text-wrap"><?php echo ($item->error !== null) ? $item->error : '-' ?></span></td>
                                                <td><span class="text-wrap"><?php echo ($item->error_pay !== null) ? $item->error_pay : '-' ?></span></td>
                                                <!-- <td class="text-wrap">
                                                        <a href="" class="btn btn-info">Ubah</a>
                                                        <a href="" class="btn btn-danger">Hapus</a>
                                                    </td> -->
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>

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
    <!-- Required datatable js -->
    <script src="<?php echo base_url() ?>assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url() ?>assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <!-- Buttons examples -->
    <script src="<?php echo base_url() ?>assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="<?php echo base_url() ?>assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
    <script src="<?php echo base_url() ?>assets/libs/jszip/jszip.min.js"></script>
    <script src="<?php echo base_url() ?>assets/libs/pdfmake/build/pdfmake.min.js"></script>
    <script src="<?php echo base_url() ?>assets/libs/pdfmake/build/vfs_fonts.js"></script>
    <script src="<?php echo base_url() ?>assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="<?php echo base_url() ?>assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="<?php echo base_url() ?>assets/libs/datatables.net-buttons/js/buttons.colVis.min.js"></script>
    <!-- Responsive examples -->
    <script src="<?php echo base_url() ?>assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="<?php echo base_url() ?>assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
    <!-- Datatable init js -->
    <script src="<?php echo base_url() ?>assets/js/pages/datatables.init.js"></script>
    <!-- choices js -->
    <script src="<?php echo base_url() ?>assets/libs/choices.js/public/assets/scripts/choices.min.js"></script>
    <!-- color picker js -->
    <script src="<?php echo base_url() ?>assets/libs/@simonwep/pickr/pickr.min.js"></script>
    <script src="<?php echo base_url() ?>assets/libs/@simonwep/pickr/pickr.es5.min.js"></script>
    <!-- datepicker js -->
    <script src="<?php echo base_url() ?>assets/libs/flatpickr/flatpickr.min.js"></script>
    <!-- init js -->
    <script src="<?php echo base_url() ?>assets/js/pages/form-advanced.init.js"></script>

</body>

</html>