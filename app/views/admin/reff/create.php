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
                                <h4 class="mb-sm-0 font-size-18">Referensi</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin/Dashboard') ?>">Dashboard</a></li>
                                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin/Reff') ?>">Referensi</a></li>
                                        <li class="breadcrumb-item active">Tambah Referensi</li>
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
                                        <h4 class="card-title col-6">Tambah Referensi</h4>
                                    </div>
                                </div>
                                <div class="card-body">

                                    <div class="row">
                                        <div class="col-md-12 col-lg-6">
                                            <div class="mt-4 mt-lg-0">
                                                <form action="<?php echo site_url("admin/Reff/store") ?>" method="POST">
                                                    <div class="row mb-4">
                                                        <label for="id" class="col-sm-3 col-form-label">ID Referensi</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control" id="id" name="id" required>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-4">
                                                        <label for="name" class="col-sm-3 col-form-label">Nama Referensi</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control" id="name" name="name" required>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-4">
                                                        <label for="type" class="col-sm-3 col-form-label">Tipe Referensi</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control" id="type" name="type" required>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-4">
                                                        <label for="code" class="col-sm-3 col-form-label">Kode Referensi</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control" id="code" name="code">
                                                        </div>
                                                    </div>

                                                    <div class="row justify-content-end">
                                                        <div class="col-sm-9">
                                                            <div>
                                                                <button type="submit" class="btn btn-primary w-md">Simpan</button>
                                                                <a href="<?php echo site_url("admin/Reff") ?>" class="btn btn-secondary w-md">Batal</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
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

</body>

</html>