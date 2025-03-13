<!-- Layout container -->
<div class="layout-page">
	<!-- Navbar -->
	<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
		id="layout-navbar">

		<!-- Menu Toggle (For smaller screens) -->
		<div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
			<a class="nav-item nav-link px-0 me-xl-4" href="#">
				<i class="bx bx-menu bx-sm" aria-hidden="true"></i>
			</a>
		</div>

		<div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
			<ul class="navbar-nav flex-row align-items-center ms-auto">
				<!-- User Profile Dropdown -->
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle hide-arrow" href="#" data-bs-toggle="dropdown">
						<span class="fw-semibold d-block">
							<?= $this->session->userdata("username"); ?>
						</span>
						<small class="text-muted">
							<?php
							$roles = [
								2 => "Kepala Sekretariat",
								3 => "Kepala Kesejahteraan Sosial",
								4 => "Kepala Pemerintahan dan Trantibum",
								5 => "Kepala Pemberdayaan Masyarakat dan Pembangunan",
							];
							echo $roles[$this->session->userdata("role")] ?? "Lurah";
							?>
						</small>
					</a>
					<ul class="dropdown-menu dropdown-menu-end">
						<li>
							<a class="dropdown-item" href="<?= base_url('logout'); ?>">
								<i class="bx bx-power-off me-2" aria-hidden="true"></i>
								<span class="align-middle">Log Out</span>
							</a>
						</li>
					</ul>
				</li>
				<!--/ User Profile Dropdown -->
			</ul>
		</div>
	</nav>
	<!-- / Navbar -->
