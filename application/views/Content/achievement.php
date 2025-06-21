<!-- Content wrapper -->
<div class="content-wrapper">
	<div class="container-xxl flex-grow-1 container-p-y">
		<div class="card">
			<h5 class="card-header">Tabel Pencapaian Kinerja</h5>
			<div class="table-responsive text-nowrap">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th>Nama</th>
							<th class="text-center">Rekap</th>
							<?php if (in_array($role, [2, 3, 4, 5])): ?>
								<th class="text-center">Aksi</th>
							<?php endif; ?>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($data as $row): ?>
							<tr>
								<td><?= $row['user']->username ?></td>
								<td class="text-center">
									<?php $isRekapKosong = empty($row['rekap']); ?>
									<button class="btn btn-sm btn-info"
										data-bs-toggle="modal"
										data-bs-target="#rekapModal_<?= $row['employee']->id ?>"
										<?= $isRekapKosong ? 'disabled' : '' ?>>
										Lihat
									</button>
								</td>
								<?php if (in_array($role, [2, 3, 4, 5])): ?>
									<td class="text-center">
										<?php $sudahDinilaiBulanIni = !empty($row['nilai']); ?>
										<button class="btn btn-sm btn-primary"
											data-bs-toggle="modal"
											data-bs-target="#nilaiModal_<?= $row['employee']->id ?>"
											<?= $sudahDinilaiBulanIni ? 'disabled' : '' ?>>
											Nilai
										</button>
									</td>
								<?php endif; ?>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
