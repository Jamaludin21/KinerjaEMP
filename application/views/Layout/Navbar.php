 <!-- Layout container -->
 <div class="layout-page">
     <!-- Navbar -->

     <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
         id="layout-navbar">
         <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
             <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                 <i class="bx bx-menu bx-sm"></i>
             </a>
         </div>

         <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
             <ul class="navbar-nav flex-row align-items-center ms-auto">
                 <!-- User -->
                 <li class="nav-item navbar-dropdown dropdown-user dropdown">
                     <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                         data-bs-toggle="dropdown">
                         <div class="d-flex">
                             <div class="flex-grow-1">
                                 <span class="fw-semibold d-block">
                                     <?php echo $this->session->userdata("username"); ?></span>
                                 <small class="text-muted">
                                     <?php
                                if ($this->session->userdata("role") == 2) {
                                    echo "Kepala Sekretariat";
                                } else if ($this->session->userdata("role") == 3) {
                                    echo "Kepala Kesejahteraan Sosial";
                                } else if ($this->session->userdata("role") == 4) {
                                    echo "Kepala Pemerintahan dan Trantibum";
                                } else if ($this->session->userdata("role") == 5) {
                                    echo "Kepala Pemberdayaan Masyarakat dan Pembangunan";
                                } else {
                                    echo "Lurah";
                                }
                            ?>
                                 </small>
                             </div>
                         </div>
                     </a>
                     <ul class="dropdown-menu dropdown-menu-end">
                         <li>
                             <button class="dropdown-item" href="<?= base_url('Main/logout'); ?>">
                                 <i class="bx bx-power-off me-2"></i>
                                 <span class="align-middle">Log Out</span>
                             </button>
                         </li>
                     </ul>
                 </li>
                 <!--/ User -->
             </ul>
         </div>
     </nav>

     <!-- / Navbar -->
