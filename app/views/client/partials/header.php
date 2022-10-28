<header id="page-topbar">
            <div class="navbar-header">
                <div class="d-flex">
                    <!-- LOGO -->
                    <div class="navbar-brand-box">
                        <a href="index.html" class="logo logo-dark">
                            <span class="logo-sm">
                                <img src="<?php echo base_url() ?>assets/images/logo-sm.svg" alt="" height="24">
                            </span>
                            <span class="logo-lg">
                                <img src="<?php echo base_url() ?>assets/images/logo-sm.svg" alt="" height="24"> <span
                                    class="logo-txt">E-Payment</span>
                            </span>
                        </a>

                        <a href="index.html" class="logo logo-light">
                            <span class="logo-sm">
                                <img src="<?php echo base_url() ?>assets/images/logo-sm.svg" alt="" height="24">
                            </span>
                            <span class="logo-lg">
                                <img src="<?php echo base_url() ?>assets/images/logo-sm.svg" alt="" height="24"> <span
                                    class="logo-txt">E-Payment</span>
                            </span>
                        </a>
                    </div>

                    <button type="button" class="btn btn-sm px-3
                            font-size-16 header-item" id="vertical-menu-btn">
                        <i class="fa fa-fw fa-bars"></i>
                    </button>
                </div>

                <div class="d-flex">

                    <div class="dropdown d-none d-sm-inline-block">
                        <button type="button" class="btn header-item" id="mode-setting-btn">
                            <i data-feather="moon" class="icon-lg
                                    layout-mode-dark"></i>
                            <i data-feather="sun" class="icon-lg
                                    layout-mode-light"></i>
                        </button>
                    </div>

                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn header-item bg-soft-light border-start border-end d-flex align-items-center" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <!-- <img class="rounded-circle header-profile-user" src="<?php echo base_url() ?>assets/images/users/avatar-1.jpg" alt="Header Avatar"> -->
                            <i class="mdi mdi-24px mdi-account-circle"></i>
                            <span class="d-none d-xl-inline-block ms-1 fw-medium"><?php echo $this->session->userdata('nama') ?></span>
                            <i class="mdi mdi-chevron-down d-none
                                    d-xl-inline-block"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="<?php echo base_url('index.php/Login/logout'); ?>">
                                <i class="mdi mdi-logout font-size-16 align-middle me-1"></i> Keluar
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </header>