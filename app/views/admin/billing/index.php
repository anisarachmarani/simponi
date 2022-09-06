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
                                                                <label for="datepicker-basic" class="col-sm-3 col-form-label">Tanggal Simponi</label>
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

                                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>ID Billing</th>
                                                <th>Tanggal Billing</th>
                                                <th>Tanggal Simponi</th>
                                                <th>Jumlah</th>
                                                <th>Status</th>
                                                <th>Error</th>
                                                <th>Error Pembayaran</th>
                                                <th>NPWP</th>
                                                <th>Tanggal Expired</th>
                                                <th>Tanggal Respon</th>
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
                                                <td><span class="text-wrap"><?php echo $item->npwp ?></span></td>
                                                <td><span class="text-wrap"><?php echo date('d M Y', strtotime($item->date_expired)) ?></span></td>
                                                <td><span class="text-wrap"><?php echo date('d M Y', strtotime($item->date_response)) ?></span></td>
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