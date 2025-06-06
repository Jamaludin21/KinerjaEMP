<!-- Content wrapper -->
<div class="content-wrapper">
	<!-- Content -->
	<div class="container-xxl container-p-y d-grid gap-5">
		<div class="card">
			<div class="card-header d-flex justify-content-between align-items-center">
				<span>
					<h4>Rekap Presensi</h4>
					<p>Tanggal-Bulan-Tahun : <?= $data['tanggal'] ?></p>
				</span>
				<?php if ((int) $this->session->userdata('role') !== 1): ?>
					<button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
						data-bs-target="#clockInModal">
						Absen Kehadiran
					</button>
				<?php endif; ?>

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
			<div class="table-responsive text-nowrap">
				<table class="table table-bordered">
					<thead>
						<tr>
							<?php if ((int) $this->session->userdata('role') == 1): ?>
								<th>Nama</th>
								<th>Email</th>
							<?php endif; ?>
							<?php if ((int) $this->session->userdata('role') !== 1): ?>
								<th>Tanggal</th>
								<th>Clock In</th>
								<th>Clock Out</th>
								<th class="text-center">Status In</th>
								<th class="text-center">Status Out</th>
							<?php endif; ?>

							<?php if ((int) $this->session->userdata('role') == 1): ?>
								<th class="text-center">Aksi</th>
							<?php endif; ?>
						</tr>
					</thead>
					<tbody>
						<?php if (!empty($data['presensi'])): ?>
							<?php
							$shownEmployeeIds = [];
							$role = (int) $this->session->userdata('role');
							?>
							<?php foreach ($data['presensi'] as $p): ?>
								<?php
								$employee_id = $p->employee_id;
								?>

								<?php if ($role == 1): ?>
									<?php if (!in_array($employee_id, $shownEmployeeIds)): ?>
										<tr>
											<td><?= $p->employee_name ?? '-' ?></td>
											<td><?= $p->employee_email ?? '-' ?></td>
											<td class="text-center">
												<button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
													data-bs-target="#modalPresensi_<?= $employee_id ?>"
													data-employee-id="<?= $employee_id ?>">
													Rekap Bulanan
												</button>
											</td>
										</tr>
										<?php $shownEmployeeIds[] = $employee_id; ?>
									<?php endif; ?>
								<?php elseif ($role !== 1): ?>
									<tr>
										<td><?= date('d-m-Y', strtotime($p->created_at)) ?></td>
										<td><?= $p->clock_in ?? '-' ?></td>
										<?php $currentDate = date('Y-m-d'); ?>
										<?php $recordDate = date('Y-m-d', strtotime($p->created_at)); ?>
										<td class="<?= !$p->clock_out ? "text-center" : "" ?>">
											<?php if (!$p->clock_out): ?>
												<?php if ($currentDate === $recordDate): ?>
													<form action="<?= base_url('clockOut') ?>" method="POST" style="display:inline;"
														class="clock-out-form">
														<input type="hidden" name="presensi_id" value="<?= $p->id ?>">
														<input type="hidden" name="employee_id" value="<?= $p->employee_id ?>">
														<input type="hidden" name="clock_out_location_latitude"
															id="clock_out_location_latitude">
														<input type="hidden" name="clock_out_location_longitude"
															id="clock_out_location_longitude">
														<button type="submit" class="btn btn-sm btn-warning clock_out">Clock Out</button>
													</form>
												<?php else: ?>
													<span class="badge bg-danger text-left">Expired Clock Out</span>
												<?php endif; ?>
											<?php else: ?>
												<?= $p->clock_out ?>
											<?php endif; ?>
										</td>
										<td class="text-center">
											<span
												class="badge rounded-pill <?= $p->status_in == "ontime" ? "bg-success" : "bg-danger" ?>">
												<?= $p->status_in ?? '-' ?>
											</span>
										</td>
										<td class="text-center">
											<span
												class="badge rounded-pill <?= ($p->status_out == "ontime") ? "bg-success" : (($p->status_out == "early") ? "bg-warning" : "bg-info") ?>">
												<?= $p->status_out ?? 'Belum Clock Out' ?>
											</span>
										</td>
									</tr>
								<?php else: ?>
									<tr>
										<td colspan="<?= (int) $this->session->userdata('role') == 1 ? 3 : 6 ?>">
											<div class="alert alert-warning text-center align-middle" role="alert">
												Belum ada Data Presensi Kehadiran Pegawai
											</div>
										</td>
									</tr>
								<?php endif; ?>
							<?php endforeach; ?>
						<?php else: ?>
							<tr>
								<td colspan="<?= (int) $this->session->userdata('role') == 1 ? 3 : 6 ?>">
									<div class="alert alert-warning text-center align-middle" role="alert">
										Tidak ada Data Presensi
									</div>
								</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="card">
			<!-- Tambahan untuk Role 2-5: Tampilkan Presensi Staff -->
			<?php if (in_array((int) $this->session->userdata('role'), [2, 3, 4, 5])): ?>

				<div class="card-header">
					<h4>Presensi Staff Anda</h4>
					<p>Tanggal-Bulan-Tahun : <?= $data['tanggal'] ?></p>
				</div>
				<div class="table-responsive text-nowrap">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Nama</th>
								<th>Tanggal</th>
								<th>Clock In</th>
								<th>Clock Out</th>
								<th class="text-center">Status In</th>
								<th class="text-center">Status Out</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($data['staffPresensi'])): ?>
								<?php foreach ($data['staffPresensi'] as $sp): ?>
									<tr>
										<td><?= $sp->employee_name ?? '-' ?></td>
										<td><?= date('d-m-Y', strtotime($sp->created_at)) ?></td>
										<td><?= $sp->clock_in ?? '-' ?></td>
										<td><?= $sp->clock_out ?? '-' ?></td>
										<td class="text-center">
											<span
												class="badge rounded-pill <?= $sp->status_in == "ontime" ? "bg-success" : "bg-danger" ?>">
												<?= $sp->status_in ?? '-' ?>
											</span>
										</td>
										<td class="text-center">
											<span
												class="badge rounded-pill <?= ($sp->status_out == "ontime") ? "bg-success" : (($sp->status_out == "early") ? "bg-warning" : "bg-info") ?>">
												<?= $sp->status_out ?? 'Belum Clock Out' ?>
											</span>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td colspan="6">
										<div class="alert alert-warning text-center" role="alert">
											Belum ada data presensi staff di bawah Anda.
										</div>
									</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
<!-- / Content -->
