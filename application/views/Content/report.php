<?php $role = isset($role) ?? null; ?>
<!-- Content wrapper -->
<div class="content-wrapper">
	<!-- Content -->
	<div class="container-xxl flex-grow-1 container-p-y">
		<div class="card">
			<div class="card-header">
				<h4 class="mb-0">Rekap Laporan</h4>
			</div>
			<?php if ($this->session->flashdata('success')): ?>
				<div class="alert alert-success mx-3" role="alert">
					<?= $this->session->flashdata('success') ?>
				</div>
			<?php endif; ?>
			<?php if ($this->session->flashdata('error')): ?>
				<div class="alert alert-danger mx-3" role="alert">
					<?= $this->session->flashdata('error') ?>
				</div>
			<?php endif; ?>
			<div class="card-body">
				<!-- Bootstrap Tabs -->
				<ul class="nav nav-pills mb-4 justify-content-center" id="reportTabs" role="tablist">
					<li class="nav-item" role="presentation">
						<button class="nav-link active" id="submitted-tab" data-bs-toggle="tab"
							data-bs-target="#submitted-reports" type="button" role="tab"
							aria-controls="submitted-reports" aria-selected="true">
							Laporan
						</button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" id="archived-tab" data-bs-toggle="tab"
							data-bs-target="#archived-reports" type="button" role="tab" aria-controls="archived-reports"
							aria-selected="false">
							Arsip Laporan
						</button>
					</li>
				</ul>

				<!-- Tab Content -->
				<div class="tab-content" id="reportTabsContent">
					<!-- Submitted Reports Tab -->
					<div class="tab-pane fade show active" id="submitted-reports" role="tabpanel"
						aria-labelledby="submitted-tab">
						<div class="table-responsive">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th>Nama Staff</th>
										<th>Tanggal Pengajuan</th>
										<th>Judul Laporan</th>
										<th class="text-center">Aksi</th>
									</tr>
								</thead>
								<tbody>
									<?php if (!empty($submittedReports)): ?>
										<?php foreach ($submittedReports as $report): ?>
											<tr>
												<td><?= $report->employee_username ?></td>
												<td><?= date('d-m-Y', strtotime($report->submitted_at)) ?></td>
												<td><?= $report->title ?></td>
												<td class="text-center">
													<?php if ($role >= 2 && $role <= 5): ?>
														<!-- Kepala Seksi Only -->
														<button class="btn btn-sm btn-primary" data-bs-toggle="modal"
															data-bs-target="#evaluateModal_<?= $report->id ?>">
															Evaluasi
														</button>
													<?php elseif ($role == 6 && $report->can_download): ?>
														<!-- Staff Only -->
														<a href="<?= base_url('report/download/' . $report->id) ?>"
															class="btn btn-sm btn-success">
															Download Tugas
														</a>
														<a href="<?= base_url('report/upload/' . $report->id) ?>"
															class="btn btn-sm btn-warning">
															Kirim Kembali
														</a>
													<?php else: ?>
														<!-- Lurah or others: read-only -->
														<span class="text-muted">Hanya lihat</span>
													<?php endif; ?>
												</td>
											</tr>
										<?php endforeach; ?>
									<?php else: ?>
										<tr>
											<td colspan="4" class="text-center">Tidak ada laporan diajukan.</td>
										</tr>
									<?php endif; ?>
								</tbody>
							</table>
						</div>
					</div>

					<!-- Archived Reports Tab -->
					<div class="tab-pane fade" id="archived-reports" role="tabpanel" aria-labelledby="archived-tab">
						<div class="table-responsive">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th>Nama Staff</th>
										<th>Tanggal Pengajuan</th>
										<th>Judul Laporan</th>
										<th class="text-center">Status Evaluasi</th>
									</tr>
								</thead>
								<tbody>
									<?php if (!empty($archivedReports)): ?>
										<?php foreach ($archivedReports as $report): ?>
											<tr>
												<td><?= $report->employee_username ?></td>
												<td><?= date('d-m-Y', strtotime($report->submitted_at)) ?></td>
												<td><?= $report->title ?></td>
												<td class="text-center">
													<span
														class="badge bg-<?= $report->status === 'Approved' ? 'success' : ($report->status === 'Evaluated' ? 'danger' : 'warning') ?>">
														<?= $report->status ?>
													</span>
												</td>
											</tr>
										<?php endforeach; ?>
									<?php else: ?>
										<tr>
											<td colspan="4" class="text-center">Tidak ada laporan dievaluasi.</td>
										</tr>
									<?php endif; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- / Content -->
