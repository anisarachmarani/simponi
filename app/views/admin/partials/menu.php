<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

<div data-simplebar class="h-100">

    <!--- Sidemenu -->
    <div id="sidebar-menu">
        <!-- Left Menu Start -->
        <ul class="metismenu list-unstyled" id="side-menu">
            <li class="menu-title" data-key="t-menu">Menu</li>
            <li>
                <a href="<?php echo base_url(); ?>index.php/admin/Billing">
                    <i class="mdi mdi-view-list"></i>
                    <span data-key="t-billing">Billing</span>
                </a>
            </li>

            <li>
                <a href="javascript: void(0);" class="has-arrow" class="d-flex justify-content-between">
                    <i class="mdi mdi-bank"></i>
                    <span data-key="t-bank">Bank</span>
                </a>
                <ul class="sub-menu" aria-expanded="false">
                    <li>
                        <a href="<?php echo base_url(); ?>index.php/admin/Bank">
                            <span data-key="t-daftar-bank">Daftar Bank</span>
                        </a>
                    </li>

                    <li>
                        <a href="#">
                            <span data-key="t-tambah-bank">Tambah Bank</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li>
                <a href="javascript: void(0);" class="has-arrow" class="d-flex justify-content-between">
                    <i class="mdi mdi-office-building"></i>
                    <span data-key="t-bank">Departemen</span>
                </a>
                <ul class="sub-menu" aria-expanded="false">
                    <li>
                        <a href="<?php echo base_url(); ?>index.php/admin/Department">
                            <span data-key="t-daftar-depertemen">Daftar Departemen</span>
                        </a>
                    </li>

                    <li>
                        <a href="#">
                            <span data-key="t-tambah-departemen">Tambah Departemen</span>
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