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
                                <h4 class="mb-sm-0 font-size-18">User</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin/Dashboard') ?>">Dashboard</a></li>
                                        <li class="breadcrumb-item active">User</li>
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
                                        <h4 class="card-title col-6">Daftar User</h4>
                                        <div class="col-6 text-end">
                                            <a href="<?php echo site_url("index.php/admin/User/create") ?>" class="btn btn-primary">Tambah <span class="d-none d-md-inline">User</span></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">

                                    <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th class="text-wrap">#</th>
                                                <th class="text-wrap">Nama</th>
                                                <th class="text-wrap">Email</th>
                                                <th class="text-wrap">Username</th>
                                                <th class="text-wrap">Role</th>
                                                <th class="text-wrap">Aplikasi</th>
                                                <th class="text-wrap">Department</th>
                                                <th class="text-wrap">Status</th>
                                                <th class="text-wrap">Aksi</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php foreach ($user as $key => $item) : ?>
                                                <?php 
                                                    foreach ($reff as $ref) {
                                                        if ($ref->id == $item->role) {
                                                            $role = $ref->name;
                                                        }
                                                        if ($ref->id == $item->status) {
                                                            $status = $ref->name;
                                                        }
                                                    }
                                                    foreach ($apps as $app) {
                                                        if ($app->id == $item->application_id) {
                                                            $application_id = $app->name;
                                                        }
                                                    }
                                                    foreach ($departments as $depart) {
                                                        if ($depart->id == $item->application_id) {
                                                            $department_id = $depart->name;
                                                        }
                                                    }
                                                ?>
                                                <tr>
                                                    <td class="text-wrap"><?php echo $key+1 ?></td>
                                                    <td class="text-wrap"><?php echo $item->name ?></td>
                                                    <td class="text-wrap"><?php echo $item->email ?></td>
                                                    <td class="text-wrap"><?php echo $item->login ?></td>
                                                    <td class="text-wrap"><?php echo $role ?></td>
                                                    <td class="text-wrap"><?php echo $application_id ?></td>
                                                    <td class="text-wrap"><?php echo $department_id ?></td>
                                                    <td class="text-wrap"><?php echo $status ?></td>
                                                    <td class="text-wrap">
                                                        <a href="<?php echo site_url('admin/User/edit/'.$item->id) ?>" class="btn btn-info">Ubah</a>
                                                        <a href="<?php echo site_url('admin/User/delete/'.$item->id) ?>" class="btn btn-danger">Hapus</a>
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

</body>

</html>