<!-- Content wrapper -->
<div class="content-wrapper">
	<!-- Content -->
	<div class="container-xxl flex-grow-1 container-p-y">
		<div class="card">
			<div class="card-header">
				<h4>Rekap Presensi</h4>
				<p>Tanggal-Bulan-Tahun : <?= $data['tanggal'] ?></p>
			</div>
			<div class="table-responsive text-nowrap">
				<table class="table table-bordered">
					<thead>
						<tr>
							<?php if ((int) $this->session->userdata('role_id') !== 6): ?>
								<th>Nama</th>
								<th>Email</th>
							<?php endif; ?>
							<th>Tanggal</th>
							<th>Clock In</th>
							<th>Clock Out</th>
							<th>Status In</th>
							<th>Status Out</th>
							<th>Aksi</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($data['presensi'] as $p): ?>
							<tr>
								<?php if ((int) $this->session->userdata('role_id') !== 6): ?>
									<td><?= $p->employee_name ?? '-' ?></td>
									<td><?= $p->employee_email ?? '-' ?></td>
								<?php endif; ?>
								<td><?= date('d-m-Y', strtotime($p->created_at)) ?></td>
								<td><?= $p->clock_in ?? '-' ?></td>
								<td><?= $p->clock_out ?? '-' ?></td>
								<td><?= $p->status_in ?? '-' ?></td>
								<td><?= $p->status_out ?? '-' ?></td>
								<td>
									<?php
									$employee_id = $p->employee_id;
									$modal_id = 'modalPresensi_' . $employee_id;
									?>
									<button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
										data-bs-target="#<?= $modal_id ?>" data-user-id="<?= $p->employee_id ?>"
										data-employee-id="<?= $p->employee_id ?>">
										Rekap Bulanan
									</button>
								</td>
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
										<th>Status In</th>
										<th>Status Out</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($bulanan as $b): ?>
										<tr>
											<td><?= date('d-m-Y', strtotime($b->created_at)) ?></td>
											<td><?= $b->clock_in ?? '-' ?></td>
											<td><?= $b->clock_out ?? '-' ?></td>
											<td><?= $b->status_in ?? '-' ?></td>
											<td><?= $b->status_out ?? '-' ?></td>
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
			</div>
		</div>
	</div>
</div>
<!-- / Content -->
