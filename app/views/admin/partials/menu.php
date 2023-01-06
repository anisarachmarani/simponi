<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

<div data-simplebar class="h-100">

    <!--- Sidemenu -->
    <div id="sidebar-menu">
        <!-- Left Menu Start -->
        <ul class="metismenu list-unstyled" id="side-menu">
            <li>
                <a href="<?php echo base_url(); ?>admin/Dashboard">
                    <i class="mdi mdi-home-account"></i>
                    <span data-key="t-dashboard">Dashboard</span>
                </a>
            </li>

            <li class="menu-title" data-key="t-menu">Menu</li>
            <li>
                <a href="<?php echo base_url(); ?>admin/Billing">
                    <i class="mdi mdi-view-list"></i>
                    <span data-key="t-billing">Billing</span>
                </a>
            </li>
            
            <li>
                <a href="<?php echo base_url(); ?>admin/Payment">
                    <i class="mdi mdi-cash"></i>
                    <span data-key="t-payment">Payment</span>
                </a>
            </li>

            <li>
                <a href="javascript: void(0);" class="has-arrow" class="d-flex justify-content-between">
                    <i class="mdi mdi-office-building"></i>
                    <span data-key="t-department">Departemen</span>
                </a>
                <ul class="sub-menu" aria-expanded="false">
                    <li>
                        <a href="<?php echo base_url(); ?>admin/Department">
                            <span data-key="t-daftar-depertemen">Daftar Departemen</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo site_url("index.php/admin/Department/create") ?>">
                            <span data-key="t-tambah-departemen">Tambah Departemen</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li>
                <a href="javascript: void(0);" class="has-arrow" class="d-flex justify-content-between">
                    <i class="mdi mdi-application"></i>
                    <span data-key="t-application">Aplikasi</span>
                </a>
                <ul class="sub-menu" aria-expanded="false">
                    <li>
                        <a href="<?php echo base_url(); ?>admin/Application">
                            <span data-key="t-daftar-aplikasi">Daftar Aplikasi</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo site_url("index.php/admin/Application/create") ?>">
                            <span data-key="t-tambah-aplikasi">Tambah Aplikasi</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li>
                <a href="javascript: void(0);" class="has-arrow" class="d-flex justify-content-between">
                    <i class="mdi mdi-account-multiple"></i>
                    <span data-key="t-user">User</span>
                </a>
                <ul class="sub-menu" aria-expanded="false">
                    <li>
                        <a href="<?php echo base_url(); ?>admin/User">
                            <span data-key="t-daftar-user">Daftar User</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo site_url("index.php/admin/User/create") ?>">
                            <span data-key="t-tambah-user">Tambah User</span>
                        </a>
                    </li>
                </ul>
            </li>
            
            <li>
                <a href="javascript: void(0);" class="has-arrow" class="d-flex justify-content-between">
                    <i class="mdi mdi-bank"></i>
                    <span data-key="t-bank">Bank</span>
                </a>
                <ul class="sub-menu" aria-expanded="false">
                    <li>
                        <a href="<?php echo base_url(); ?>admin/Bank">
                            <span data-key="t-daftar-bank">Daftar Bank</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo site_url("index.php/admin/Bank/create") ?>">
                            <span data-key="t-tambah-bank">Tambah Bank</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li>
                <a href="javascript: void(0);" class="has-arrow" class="d-flex justify-content-between">
                    <i class="mdi mdi-clipboard-list"></i>
                    <span data-key="t-reff">Referensi</span>
                </a>
                <ul class="sub-menu" aria-expanded="false">
                    <li>
                        <a href="<?php echo base_url(); ?>admin/Reff">
                            <span data-key="t-daftar-referensi">Daftar Referensi</span>
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo site_url("index.php/admin/Reff/create") ?>">
                            <span data-key="t-tambah-referensi">Tambah Referensi</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
    <!-- Sidebar -->
</div>
</div>
<!-- Left Sidebar End -->