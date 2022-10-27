<!doctype html>
<html lang="en">

<head>
    <?php $this->load->view("client/partials/head.php") ?>
    <?php $this->load->view("client/partials/css.php") ?>
</head>

<body>

    <!-- <body data-layout="horizontal"> -->

    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php $this->load->view("client/partials/header.php") ?>
        
        <?php $this->load->view("client/partials/menu.php") ?>
        
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
                                        <li class="breadcrumb-item"><a href="<?php echo site_url('client/Dashboard') ?>">Dashboard</a></li>
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

                                    <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th class="text-wrap">#</th>
                                                <th class="text-wrap">Aplikasi</th>
                                                <th class="text-wrap">ID Transaksi</th>
                                                <th class="text-wrap">Department</th>
                                                <th class="text-wrap">User</th>
                                                <th class="text-wrap">Detail</th>
                                                <th class="text-wrap">Jumlah</th>
                                                <th class="text-wrap">Status</th>
                                                <th class="text-wrap">ID Simponi</th>
                                                <th class="text-wrap">ID Billing</th>
                                                <th class="text-wrap">NTPN</th>
                                                <th class="text-wrap">NTB</th>
                                                <th class="text-wrap">Bank</th>
                                                <th class="text-wrap">Channel</th>
                                                <th class="text-wrap">Error Pembayaran</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php foreach ($payment as $key => $item) : ?>
                                                <?php 
                                                    foreach ($reff as $value) {
                                                        if ($value->id == $item->channel) {
                                                            $channel = $value->name;
                                                        }
                                                    }
                                                ?>
                                                <tr>
                                                    <td class="text-wrap"><?php echo $key+1 ?></td>
                                                    <td class="text-wrap"><?php echo $item->application_name ?></td>
                                                    <td class="text-wrap"><?php echo $item->transaction_id ?></td>
                                                    <td class="text-wrap"><?php echo $item->department_name ?></td>
                                                    <td class="text-wrap"><?php echo $item->user_name ?></td>
                                                    <td class="text-wrap"><?php echo $item->detail ?></td>
                                                    <td class="text-wrap text-end"><?php echo number_format($item->total) ?></td>
                                                    <td class="text-wrap"><?php echo $item->status_name ?></td>
                                                    <td class="text-wrap"><?php echo $item->simponi_id ?></td>
                                                    <td class="text-wrap"><?php echo $item->billing_id ?></td>
                                                    <td class="text-wrap"><?php echo $item->ntpn ?></td>
                                                    <td class="text-wrap"><?php echo $item->ntb ?></td>
                                                    <td class="text-wrap"><?php echo $item->bank_name ?></td>
                                                    <td class="text-wrap"><?php echo $channel ?></td>
                                                    <td class="text-wrap">
                                                        <?php 
                                                            if ($item->error_pay == NULL) {
                                                                echo "-";
                                                            } else {
                                                                echo $item->error_pay;
                                                            }
                                                            
                                                        ?>
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
        
        <?php $this->load->view("client/partials/footer.php") ?>

        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <?php $this->load->view("client/partials/sidebar.php") ?>
    
    <?php $this->load->view("client/partials/script.php") ?>

</body>

</html>