<!-- Content wrapper -->
<div class="content-wrapper">

	<!-- Content -->
	<div class="container-xxl flex-grow-1 container-p-y">
		<?php if ($this->session->flashdata('error')): ?>
			<div class="alert alert-danger" role="alert">
				<?= $this->session->flashdata('error') ?>
			</div>
		<?php endif; ?>
		<div class="row">
			<div class="col-lg-3 col-md-12 col-6 mb-4">
				<div class="card">
					<div class="card-body">
						<span class="fw-semibold d-block mb-1">Pengguna</span>
						<h3 class="card-title mb-2"><?= $penggunaCount ?></h3>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-12 col-6 mb-4">
				<div class="card">
					<div class="card-body">
						<span class="fw-semibold d-block mb-1">Data Absensi</span>
						<h3 class="card-title mb-2"><?= $absensiCount ?></h3>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-12 col-6 mb-4">
				<div class="card">
					<div class="card-body">
						<span class="fw-semibold d-block mb-1">Data Laporan</span>
						<h3 class="card-title mb-2">1</h3>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-12 col-6 mb-4">
				<div class="card">
					<div class="card-body">
						<span class="fw-semibold d-block mb-1">Data Pencapaian</span>
						<h3 class="card-title mb-2">2</h3>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- / Content -->
