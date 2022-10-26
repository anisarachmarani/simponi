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
                                <h4 class="mb-sm-0 font-size-18">Bank</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin/Dashboard') ?>">Dashboard</a></li>
                                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin/Bank') ?>">Bank</a></li>
                                        <li class="breadcrumb-item active">Ubah Bank</li>
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
                                        <h4 class="card-title col-6">Ubah User <?php echo $user->name ?></h4>
                                    </div>
                                </div>
                                <div class="card-body">

                                    <form action="<?php echo site_url("admin/User/update") ?>" method="POST">
                                        <input type="hidden" name="id" value="<?= $user->id; ?>">
                                        <div class="row">
                                            <div class="col-md-12 col-lg-6">
                                                <div class="mt-4 mt-lg-0">
                                                    <div class="row mb-4">
                                                        <label for="name" class="col-sm-3 col-form-label">Nama</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control" id="name" name="name" value="<?= $user->name; ?>">
                                                        </div>
                                                    </div>

                                                    <div class="row mb-4">
                                                        <label for="email" class="col-sm-3 col-form-label">Email</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control" id="email" name="email" value="<?= $user->email; ?>">
                                                        </div>
                                                    </div>

                                                    <div class="row mb-4">
                                                        <label for="login" class="col-sm-3 col-form-label">Username</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control" id="login" name="login" value="<?= $user->login; ?>">
                                                        </div>
                                                    </div>

                                                    <div class="row mb-4">
                                                        <label for="password" class="col-sm-3 col-form-label">Password Baru</label>
                                                        <div class="col-sm-9">
                                                            <input type="password" class="form-control" id="password" name="password">
                                                            <input type="password" name="password_old" value="<?= $user->password; ?>" hidden>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-6">
                                                <div class="mt-4 mt-lg-0">
                                                    <div class="row mb-4">
                                                        <label for="department_id" class="col-sm-3 col-form-label">Department</label>
                                                        <div class="col-sm-9">
                                                            <select class="form-control" data-trigger name="department_id" id="department_id">
                                                                <?php foreach ($department as $value) : ?>
                                                                    <option <?= ($value->id == $user->department_id) ? 'selected' : '' ?> value="<?= $value->id; ?>"><?= $value->name; ?></option>
                                                                <?php endforeach ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-4">
                                                        <label for="application_id" class="col-sm-3 col-form-label">Aplikasi</label>
                                                        <div class="col-sm-9">
                                                            <select class="form-control" data-trigger name="application_id" id="application_id">
                                                                <?php foreach ($app as $value) : ?>
                                                                    <option <?= ($value->id == $user->application_id) ? 'selected' : '' ?> value="<?= $value->id; ?>"><?= $value->name; ?></option>
                                                                <?php endforeach ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-4">
                                                        <label for="role" class="col-sm-3 col-form-label">Role</label>
                                                        <div class="col-sm-9">
                                                            <select class="form-control" data-trigger name="role" id="role">
                                                                <?php foreach ($roles as $value) : ?>
                                                                    <option <?= ($value->id == $user->role) ? 'selected' : '' ?> value="<?= $value->id; ?>"><?= $value->name; ?></option>
                                                                <?php endforeach ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-4">
                                                        <label for="status" class="col-sm-3 col-form-label">Status</label>
                                                        <div class="col-sm-9">
                                                            <select class="form-control" data-trigger name="status" id="status">
                                                                <option value="US01" <?= ($user->status == "US01") ? 'selected' : '' ?> >Aktif</option>
                                                                <option value="US02" <?= ($user->status == "US02") ? 'selected' : '' ?> >Non-Aktif</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-6 row justify-content-end">
                                                <div class="col-sm-9">
                                                    <div>
                                                        <button type="submit" class="btn btn-primary w-md">Simpan</button>
                                                        <a href="<?php echo site_url("admin/User") ?>" class="btn btn-secondary w-md">Batal</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>

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