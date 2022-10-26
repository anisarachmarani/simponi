<!doctype html>
<html lang="en">

    <head>

        <meta charset="utf-8" />
        <title>E-Payment</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
        <meta content="Themesbrand" name="author" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="<?php echo base_url() ?>assets/images/favicon.ico">

        <!-- preloader css -->
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/preloader.min.css" type="text/css" />

        <!-- Bootstrap Css -->
        <link href="<?php echo base_url() ?>assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="<?php echo base_url() ?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="<?php echo base_url() ?>assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />

    </head>

    <body>

    <!-- <body data-layout="horizontal"> -->
        <div class="auth-page">
            <div class="container-fluid p-0">
                <div class="row g-0">
                    <div class="col-xxl-3 col-lg-4 col-md-5">
                        <div class="auth-full-page-content d-flex p-sm-5 p-4">
                            <div class="w-100">
                                <div class="d-flex flex-column h-100">
                                    <div class="mb-4 mb-md-5 text-center">
                                        <a href="index.html" class="d-block auth-logo">
                                            <img src="<?php echo base_url() ?>assets/images/logo-sm.svg" alt="" height="28"> <span class="logo-txt">E-Payment</span>
                                        </a>
                                    </div>
                                    <div class="auth-content my-auto">
                                        <div class="text-center">
                                            <h5 class="mb-0">Welcome!</h5>
                                            <p class="text-muted mt-2">Sign in to continue to E-Payment.</p>
                                        </div>
                                        <form class="custom-form mt-4 pt-2" action="<?php echo site_url("admin/Login/proses") ?>" method="POST">
                                            <div class="mb-3">
                                                <label class="form-label" for="name">Username</label>
                                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter username">
                                            </div>
                                            <div class="mb-3">
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-grow-1">
                                                        <label class="form-label" for="password">Password</label>
                                                    </div>
                                                </div>
                                                
                                                <div class="input-group auth-pass-inputgroup">
                                                    <input type="password" class="form-control" placeholder="Enter password" aria-label="Password" id="password" name="password" aria-describedby="password-addon">
                                                    <button class="btn btn-light ms-0" type="button" id="password-addon"><i class="mdi mdi-eye-outline"></i></button>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <button class="btn btn-primary w-100 waves-effect waves-light" type="submit">Log In</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="mt-4 mt-md-5 text-center">
                                        <p class="mb-0">© <script>document.write(new Date().getFullYear())</script> E-Payment by AGS</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end auth full page content -->
                    </div>
                    <!-- end col -->
                    <div class="col-xxl-9 col-lg-8 col-md-7">
                        <div class="auth-bg pt-md-5 p-4 d-flex">
                            <div class="bg-overlay bg-primary"></div>
                            <ul class="bg-bubbles">
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                            </ul>
                            <!-- end bubble effect -->
                            <div class="row justify-content-center align-items-center">
                                <div class="col-xl-7">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container fluid -->
        </div>


        <!-- JAVASCRIPT -->
        <script src="<?php echo base_url() ?>assets/libs/jquery/jquery.min.js"></script>
        <script src="<?php echo base_url() ?>assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="<?php echo base_url() ?>assets/libs/metismenu/metisMenu.min.js"></script>
        <script src="<?php echo base_url() ?>assets/libs/simplebar/simplebar.min.js"></script>
        <script src="<?php echo base_url() ?>assets/libs/node-waves/waves.min.js"></script>
        <script src="<?php echo base_url() ?>assets/libs/feather-icons/feather.min.js"></script>
        <!-- pace js -->
        <script src="<?php echo base_url() ?>assets/libs/pace-js/pace.min.js"></script>
        <!-- password addon init -->
        <script src="<?php echo base_url() ?>assets/js/pages/pass-addon.init.js"></script>

    </body>

</html>