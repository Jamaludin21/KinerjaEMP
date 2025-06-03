<!-- Content wrapper -->
<div class="content-wrapper">
	<!-- Content -->
	<div class="container-xxl flex-grow-1 container-p-y">
		<div class="card">
			<div class="card-header d-flex justify-content-between align-items-center">
				<span>
					<h4>Rekap Presensi</h4>
					<p>Tanggal-Bulan-Tahun : <?= $data['tanggal'] ?></p>
				</span>
				<?php if ((int) $this->session->userdata('role') == 6): ?>
					<button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
						data-bs-target="#clockInModal">
						Absen Kehadiran
					</button>
				<?php endif; ?>
			</div>

			<div class="table-responsive text-nowrap">
				<table class="table table-bordered">
					<thead>
						<tr>
							<?php if ((int) $this->session->userdata('role') !== 6): ?>
								<th>Nama</th>
								<th>Email</th>
							<?php endif; ?>
							<?php if ((int) $this->session->userdata('role') == 6): ?>
								<th>Tanggal</th>
								<th>Clock In</th>
								<th>Clock Out</th>
								<th class="text-center">Status In</th>
								<th class="text-center">Status Out</th>
							<?php endif; ?>

							<?php if ((int) $this->session->userdata('role') !== 6): ?>
								<th class="text-center">Aksi</th>
							<?php endif; ?>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($data['presensi'] as $p): ?>
							<tr>
								<?php if ((int) $this->session->userdata('role') !== 6): ?>
									<td><?= $p->employee_name ?? '-' ?></td>
									<td><?= $p->employee_email ?? '-' ?></td>
								<?php endif; ?>
								<?php if ((int) $this->session->userdata('role') == 6): ?>
									<td><?= date('d-m-Y', strtotime($p->created_at)) ?></td>
									<td><?= $p->clock_in ?? '-' ?></td>
									<td class="<?= !$p->clock_out ? "text-center" : "" ?>">
										<?php if (!$p->clock_out): ?>
											<!-- Clock Out Form Inline -->
											<form action="<?= base_url('presensi/clock_out') ?>" method="POST"
												style="display:inline;">
												<input type="hidden" name="presensi_id" value="<?= $p->id ?>">
												<button type="submit" class="btn btn-sm btn-warning">Clock Out</button>
											</form>
										<?php else: ?>
											<?= $p->clock_out ?>
										<?php endif; ?>
									</td>
									<td class="text-center"><span
											class="badge rounded-pill <?= $p->status_in == "ontime" ? "bg-success" : "bg-danger" ?>"><?= $p->status_in ?? '-' ?></span>
									</td>
									<td class="text-center"><span
											class="badge rounded-pill <?= ($p->status_out == "ontime") ? "bg-success" : (($p->status_out == "early") ? "bg-warning" : "bg-info") ?>"><?= $p->status_out ?? 'Belum Clock Out' ?></span>
									</td>
								<?php endif; ?>

								<?php if ((int) $this->session->userdata('role') !== 6): ?>
									<td class="text-center">
										<?php
										$employee_id = $p->employee_id;
										$modal_id = 'modalPresensi_' . $employee_id;
										?>
										<button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
											data-bs-target="#<?= $modal_id ?>" data-employee-id="<?= $employee_id ?>">
											Rekap Bulanan
										</button>
									</td>
								<?php endif; ?>

							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<!-- Modals for Presensi Bulanan -->
				<?php
				$unique_employee_ids = [];

				foreach ($data['presensi'] as $p) {
					$employee_id = $p->employee_id;
					$employee_name = $p->employee_name;
					if (!in_array($employee_id, $unique_employee_ids)) {
						$unique_employee_ids[] = $employee_id;

						$bulanan = $data['presensiBulanan'][$employee_id] ?? [];

						echo '<div class="modal fade" id="modalPresensi_' . $employee_id . '" tabindex="-1" aria-labelledby="modalLabel_' . $employee_id . '" aria-hidden="true">';
						echo '<div class="modal-dialog modal-dialog-centered modal-lg">';
						echo '<div class="modal-content">';
						echo '<div class="modal-header">';
						echo '<h5 class="modal-title" id="modalLabel_' . $employee_id . '">Rekap Bulanan ' . $employee_name . '</h5>';
						echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
						echo '</div>';
						echo '<div class="modal-body">';
						if ($bulanan):
							?>
							<table class="table table-sm table-striped">
								<thead>
									<tr>
										<th>Tanggal</th>
										<th>Clock In</th>
										<th>Clock Out</th>
										<th class="text-center">Status In</th>
										<th class="text-center">Status Out</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($bulanan as $b): ?>
										<tr>
											<td><?= date('d-m-Y', strtotime($b->created_at)) ?></td>
											<td><?= $b->clock_in ?? '-' ?></td>
											<td><?= $b->clock_out ?? '-' ?></td>
											<td class="text-center"><span
													class="badge rounded-pill <?= $b->status_in == "ontime" ? "bg-success" : "bg-danger" ?>"><?= $b->status_in ?? '-' ?></span>
											</td>
											<td class="text-center"><span
													class="badge rounded-pill <?= ($b->status_out == "ontime") ? "bg-success" : (($b->status_out == "early") ? "bg-warning" : "bg-info") ?>"><?= $b->status_out ?? 'Belum Clock Out' ?></span>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
							<?php
						else:
							echo "<p>Tidak ada data presensi bulanan.</p>";
						endif;
						echo '</div>'; // modal-body
						echo '<div class="modal-footer">';
						echo '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>';
						echo '</div>';
						echo '</div>';
						echo '</div>';
						echo '</div>';
					}
				}
				?>

				<?php if ((int) $this->session->userdata('role') == 6): ?>
					<!-- Modal Clock In -->
					<div class="modal fade" id="clockInModal" tabindex="-1" aria-labelledby="clockInModalLabel"
						aria-hidden="true">
						<div class="modal-dialog">
							<form action="<?= base_url('presensi/clock_in') ?>" method="POST">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title">Absen Clock In</h5>
										<button type="button" class="btn-close" data-bs-dismiss="modal"
											aria-label="Tutup"></button>
									</div>
									<div class="modal-body">
										<div class="mb-3">
											<label for="tanggal" class="form-label">Tanggal</label>
											<input type="date" class="form-control" name="tanggal" required
												value="<?= date('Y-m-d') ?>">
										</div>
										<div class="mb-3">
											<label for="clock_in" class="form-label">Jam Clock In</label>
											<input type="time" class="form-control" name="clock_in" required>
										</div>
										<!-- Hidden employee id from session -->
										<input type="hidden" name="employee_id"
											value="<?= $this->session->userdata('user_id') ?>">
									</div>
									<div class="modal-footer">
										<button type="submit" class="btn btn-primary">Submit Clock In</button>
										<button type="button" class="btn btn-secondary"
											data-bs-dismiss="modal">Batal</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				<?php endif; ?>

			</div>
		</div>
	</div>
</div>
<!-- / Content -->
