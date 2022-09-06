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
                                <h4 class="mb-sm-0 font-size-18">Selamat Datang, <?php echo $this->session->userdata('nama') ?> !</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item active">Dashboard</li>
                                    </ol>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="row">
                        <div class="col-12 col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h4 class="card-title">Daftar Billing</h4>
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Nomor</th>
                                                    <th>Jumlah</th>
                                                    <th>Tanggal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($billing as $key => $item) : ?>
                                                    <tr>
                                                        <td><?php echo $item->billing_id ?></td>
                                                        <td><?php echo number_format($item->total);?></td>
                                                        <td><?php echo date('d/m/y', strtotime($item->date_register)) ?></td>
                                                    </tr>
                                                <?php endforeach ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <a href="<?php echo base_url(); ?>index.php/admin/Billing" class="btn btn-primary waves-effect waves-light w-50 mt-3">Selengkapnya</a>
                                </div>
                            </div>
                        </div>
                        <!-- end col -->

                        <div class="col-12 col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h4 class="card-title">Daftar Bank</h4>
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <thead>
                                                <tr>
                                                    <!-- <th>#</th> -->
                                                    <th>Kode</th>
                                                    <th>Nama</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($bank as $key => $item) : ?>
                                                    <tr>
                                                        <!-- <th scope="row"><?php echo $key+1 ?></th> -->
                                                        <td><?php echo $item->code ?></td>
                                                        <td class="text-start"><?php echo $item->name ?></td>
                                                    </tr>
                                                <?php endforeach ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <a href="<?php echo base_url(); ?>index.php/admin/Bank" class="btn btn-primary waves-effect waves-light w-50 mt-3">Selengkapnya</a>
                                </div>
                            </div>
                        </div>
                        <!-- end col -->

                        <div class="col-12 col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h4 class="card-title">Daftar Departemen</h4>
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <thead>
                                                <tr>
                                                    <!-- <th>#</th> -->
                                                    <th>Kode</th>
                                                    <th>Nama</th>
                                                    <th>User ID</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($department as $key => $item) : ?>
                                                    <tr>
                                                        <!-- <th scope="row"><?php echo $key+1 ?></th> -->
                                                        <td><?php echo $item->code_unit ?></td>
                                                        <td class="text-start"><?php echo $item->name ?></td>
                                                        <td><?php echo $item->user_id ?></td>
                                                    </tr>
                                                <?php endforeach ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <a href="<?php echo base_url(); ?>index.php/admin/Department" class="btn btn-primary waves-effect waves-light w-50 mt-3">Selengkapnya</a>
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

</body>

</html>