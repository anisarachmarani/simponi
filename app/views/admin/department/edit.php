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
                                <h4 class="mb-sm-0 font-size-18">Departemen</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a
                                                href="<?php echo site_url('admin') ?>">Dashboard</a></li>
                                        <li class="breadcrumb-item"><a
                                                href="<?php echo site_url('admin/Department') ?>">Departemen</a>
                                        </li>
                                        <li class="breadcrumb-item active">Ubah Departemen</li>
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
                                        <h4 class="card-title col-6">Ubah Departemen (<?= $department->name ?>)</h4>
                                    </div>
                                </div>
                                <div class="card-body">

                                    <div class="row">
                                        <div class="col-12 px-0 px-md-5">
                                            <div class="mt-0">
                                                <form
                                                    action="<?php echo site_url("admin/Department/update") ?>"
                                                    method="POST">
                                                    <input type="hidden" name="id" value="<?php echo $department->id?>" />
                                                    <input type="hidden" name="user_id" value="<?php echo $department->user_id?>" />
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label for="name" class="col-form-label">Nama
                                                                    Departemen</label>
                                                                <input type="text" class="form-control" id="name"
                                                                    value="<?= $department->name; ?>" name="name" required>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="currency"
                                                                    class="col-form-label">Currency</label>
                                                                <input type="text" class="form-control" id="currency"
                                                                    value="<?= $department->currency; ?>" name="currency">
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label for="pnbp" class="col-form-label">PNBP</label>
                                                                <input type="text" class="form-control" id="pnbp"
                                                                    value="<?= $department->pnbp; ?>" name="pnbp">
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="password"
                                                                    class="col-form-label">Password</label>
                                                                <input type="text" class="form-control" id="password"
                                                                    value="<?= $department->password; ?>" name="password" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-4">
                                                            <div class="mb-3">
                                                                <label for="code_unit" class="col-form-label">Kode
                                                                    Unit</label>
                                                                <input type="text" class="form-control" id="code_unit"
                                                                    value="<?= $department->code_unit; ?>" name="code_unit">
                                                            </div>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="mb-3">
                                                                <label for="code_ga" class="col-form-label">Kode
                                                                    GA</label>
                                                                <input type="text" class="form-control" id="code_ga"
                                                                    value="<?= $department->code_ga; ?>" name="code_ga" value="090" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="mb-3">
                                                                <label for="code_echelon_1" class="col-form-label">
                                                                    <span class="d-none d-md-inline">Kode Echelon
                                                                        1</span>
                                                                    <small class="d-md-none d-inline">Kode Echelon
                                                                        1</small>
                                                                </label>
                                                                <input type="text" class="form-control"
                                                                    id="code_echelon_1" value="<?= $department->code_echelon_1; ?>" name="code_echelon_1" value="03"
                                                                    required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-4">
                                                            <div class="mb-3">
                                                                <label for="code_1" class="col-form-label">Kode
                                                                    1</label>
                                                                <input type="text" class="form-control" id="code_1"
                                                                    value="<?= $department->code_1; ?>" name="code_1" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="mb-3">
                                                                <label for="code_2" class="col-form-label">Kode
                                                                    2</label>
                                                                <input type="text" class="form-control" id="code_2"
                                                                    value="<?= $department->code_2; ?>" name="code_2">
                                                            </div>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="mb-3">
                                                                <label for="code_3" class="col-form-label">Kode
                                                                    3</label>
                                                                <input type="text" class="form-control" id="code_3"
                                                                    value="<?= $department->code_3; ?>" name="code_3">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row justify-content-start">
                                                        <div class="col-sm-9">
                                                            <div>
                                                                <button type="submit"
                                                                    class="btn btn-primary w-md">Simpan</button>
                                                                <a href="<?php echo site_url("admin/Department") ?>"
                                                                    class="btn btn-secondary w-md">Batal</a>
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