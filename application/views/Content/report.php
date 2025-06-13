<?php $role = isset($role) ? $role : null; ?>
<!-- Content wrapper -->
<div class="content-wrapper">
	<!-- Content -->
	<div class="container-xxl flex-grow-1 container-p-y">
		<div class="card">
			<div class="card-header">
				<h4 class="mb-0">Rekap Laporan</h4>
			</div>
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

				<?php if (in_array($role, [2, 3, 4, 5])): ?>
					<div class="mb-3 text-end" style="padding-right: 1.5rem">
						<button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#assignTaskModal">
							<i class="bx bx-plus"></i> Submit Tugas
						</button>
					</div>
				<?php endif; ?>

				<?php if ($this->session->flashdata('success')): ?>
					<div class="alert alert-success mx-4 mt-4" role="alert">
						<?= $this->session->flashdata('success') ?>
					</div>
				<?php endif; ?>
				<?php if ($this->session->flashdata('error')): ?>
					<div class="alert alert-danger mx-4 mt-4" role="alert">
						<?= $this->session->flashdata('error') ?>
					</div>
				<?php endif; ?>

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
										<?php if (!in_array($role, [2, 3, 4, 5])): ?>
											<th>Ditugaskan Oleh</th>
										<?php endif; ?>
										<th>Submit</th>
										<?php if ($role !== 1): ?>
											<th class="text-center">Aksi</th>
										<?php endif; ?>
									</tr>
								</thead>
								<tbody>
									<?php if (!empty($submittedReports)): ?>
										<?php foreach ($submittedReports as $report): ?>
											<tr>
												<td><?= $report->employee_username ?></td>
												<td><?= date('d-m-Y', strtotime($report->created_at)) ?></td>
												<td><?= $report->title ?></td>
												<?php if (!in_array($role, [2, 3, 4, 5])): ?>
													<td><span class="badge bg-info">
															<?= $report->supervisor_role_name ?>
														</span></td>
												<?php endif; ?>
												<td><?= date('d-m-Y', strtotime($report->submitted_at)) ?></td>
												<?php if ($role !== 1): ?>
													<td class="text-center">
														<?php
														$isSubmitted = $report->status === 'Submitted';
														$hasFile = !empty($report->report_file);
														$canEvaluate = in_array($role, [2, 3, 4, 5]) && $isSubmitted && $hasFile;
														$canUpload = $role == 6 && $isSubmitted && !$hasFile;
														?>

														<?php if ($canEvaluate): ?>
															<button class="btn btn-sm btn-warning" data-bs-toggle="modal"
																data-bs-target="#evaluateModal"
																onclick="setEvaluateId(<?= $report->id ?>)">
																<i class="bx bxs-report me-1"></i>Evaluasi
															</button>
														<?php elseif ($canUpload): ?>
															<button class="btn btn-sm btn-danger" data-bs-toggle="modal"
																data-bs-target="#uploadModal" onclick="setUploadId(<?= $report->id ?>)">
																<i class="bx bx-upload me-1"></i>Upload Tugas
															</button>
														<?php elseif ($role == 6 && $hasFile): ?>
															<span class="badge bg-info"><i class="bx bx-loader me-1"></i>Menunggu
																Penilaian</span>
														<?php elseif ($role >= 2 && $role <= 5): ?>
															<span class="badge bg-info"><i class="bx bx-loader me-1"></i>Menunggu File
																Tugas</span>
														<?php else: ?>
															<span class="text-muted">-</span>
														<?php endif; ?>
													</td>
												<?php endif; ?>
											</tr>
										<?php endforeach; ?>
									<?php else: ?>
										<tr>
											<td colspan="10" class="text-center">Tidak ada laporan diajukan.</td>
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
										<?php if (!in_array($role, [2, 3, 4, 5])): ?>
											<th>Ditugaskan Oleh</th>
										<?php endif; ?>
										<th>Submit</th>
										<th>Evalusi</th>
										<th>Detail Evaluasi</th>
										<th class="text-center">Status Evaluasi</th>
									</tr>
								</thead>
								<tbody>
									<?php if (!empty($archivedReports)): ?>
										<?php foreach ($archivedReports as $report): ?>
											<tr>
												<td><?= $report->employee_username ?></td>
												<td><?= date('d-m-Y', strtotime($report->created_at)) ?></td>
												<td><?= $report->title ?></td>
												<?php if (!in_array($role, [2, 3, 4, 5])): ?>
													<td><span class="badge bg-info">
															<?= $report->supervisor_role_name ?>
														</span></td>
												<?php endif; ?>
												<td><?= date('d-m-Y', strtotime($report->submitted_at)) ?></td>
												<td><?= date('d-m-Y', strtotime($report->evaluated_at)) ?></td>
												<td class="<?= $report->evaluation ?? "text-center" ?>">
													<?= $report->evaluation ?? '-' ?>
												</td>
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
