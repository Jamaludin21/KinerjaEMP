<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <span>E-Kinerja Kelurahan</span>
                    <a href="javascript:void(0);"
                        class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                        <i class="bx bx-chevron-left bx-sm align-middle"></i>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1">
                    <!-- Dashboard -->
                    <li class="menu-item <?= ($this->uri->uri_string() === '') ? 'active' : '' ?>">
                        <a href="<?= base_url('') ?>" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-home-circle"></i>
                            <div data-i18n="Analytics">Dashboard</div>
                        </a>
                    </li>

                    <li class="menu-item <?= ($this->uri->uri_string() === 'users') ? 'active' : '' ?>">
                        <a href="<?= base_url('users') ?>" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-user"></i>
                            <div data-i18n="Analytics">Data Pengguna</div>
                        </a>
                    </li>

                    <li class="menu-item <?= ($this->uri->uri_string() === 'rekap') ? 'active' : '' ?>">
                        <a href="<?= base_url('rekap') ?>" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-archive-in"></i>
                            <div data-i18n="Analytics">Rekap Absensi</div>
                        </a>
                    </li>

                    <li class="menu-item <?= ($this->uri->uri_string() === 'penilaian') ? 'active' : '' ?>">
                        <a href="<?= base_url('penilaian') ?>" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-label"></i>
                            <div data-i18n="Analytics">Penilaian Kerja</div>
                        </a>
                    </li>

                    <li class="menu-item <?= ($this->uri->uri_string() === 'pencapaian') ? 'active' : '' ?>">
                        <a href="<?= base_url('pencapaian') ?>" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-pie-chart-alt-2"></i>
                            <div data-i18n="Analytics">Pencapaian Kerja</div>
                        </a>
                    </li>

                </ul>
            </aside>
            <!-- / Menu -->
