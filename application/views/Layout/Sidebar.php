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
				<ul class="menu-inner py-1">
					<?php
					$menu_items = [
						['url' => '', 'icon' => 'bx-home-circle', 'title' => 'Dashboard'],
						['url' => 'users', 'icon' => 'bx-user', 'title' => 'Manajemen Pegawai', 'status' => 'limited'],
						['url' => 'rekap', 'icon' => 'bx-archive-in', 'title' => 'Absensi'],
						['url' => 'laporan', 'icon' => 'bx-book', 'title' => 'Laporan'],
						['url' => 'penilaian', 'icon' => 'bx-label', 'title' => 'Pencatatan Kinerja'],
					];

					foreach ($menu_items as $item): ?>
						<?php
						// Check if menu has 'limited' status and user role is NOT in roleLabels
						$is_limited = isset($item['status']) && $item['status'] === 'limited';
						$employee_role = $session['role'] == 6;

						if ($is_limited && $employee_role) {
							continue; // Skip rendering this menu item
						}
						?>
						<li class="menu-item <?= ($this->uri->uri_string() === $item['url']) ? 'active' : '' ?>">
							<a href="<?= base_url($item['url']) ?>" class="menu-link">
								<i class="menu-icon tf-icons bx <?= $item['icon'] ?>"></i>
								<div data-i18n="Analytics"><?= $item['title'] ?></div>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</aside>
			<!-- / Menu -->
