<!doctype html>
<html lang="en">

<head>
    <?php $this->load->view("admin/partials/head.php") ?>
    <!-- DataTables -->
    <link href="<?php echo base_url() ?>assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url() ?>assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url() ?>assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
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
                                <h4 class="mb-sm-0 font-size-18">Bank</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="<?php echo site_url('index.php/admin') ?>">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Bank</li>
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
                                        <h4 class="card-title col-6">Data Bank</h4>
                                        <div class="col-6 text-end">
                                            <a href="<?php echo site_url("index.php/admin/Bank/create") ?>" class="btn btn-primary">Tambah <span class="d-none d-md-inline">Bank</span></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">

                                    <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th class="text-wrap">Nomor</th>
                                                <th class="text-wrap">Nama Bank</th>
                                                <th class="text-wrap">Kode Bank</th>
                                                <th class="text-wrap">Aksi</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php foreach ($bank as $key => $item) : ?>
                                                <tr>
                                                    <td class="text-wrap"><?php echo $key+1 ?></td>
                                                    <td class="text-wrap"><?php echo $item->name ?></td>
                                                    <td class="text-wrap"><?php echo $item->code ?></td>
                                                    <td class="text-wrap">
                                                        <a href="<?php echo site_url('index.php/admin/Bank/edit/'.$item->id) ?>" class="btn btn-info">Ubah</a>
                                                        <a href="<?php echo site_url('index.php/admin/Bank/delete/'.$item->id) ?>" class="btn btn-danger">Hapus</a>
                                                    </td>
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

</body>

</html>