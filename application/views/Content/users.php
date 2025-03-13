<!-- Content wrapper -->
<div class="content-wrapper">
	<div class="container-xxl flex-grow-1 container-p-y">
		<div class="card">
			<div class="d-flex justify-content-between align-items-center p-3">
				<h5 class="card-header mb-0">Data Pengguna</h5>
				<button class="btn btn-primary btn-sm">
					<i class="bx bx-plus"></i> Add
				</button>
			</div>
			<div class="table-responsive text-nowrap">
				<table class="table">
					<thead>
						<tr class="text-center">
							<th>Username</th>
							<th>Email</th>
							<th>Role</th>
							<th>Created At</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody class="table-border-bottom-0 text-center">
						<?php foreach ($users as $user): ?>
							<tr>
								<td><?= htmlspecialchars($user['username']) ?></td>
								<td><?= htmlspecialchars($user['email']) ?></td>
								<td>
									<span class="badge bg-label-<?= htmlspecialchars($user['role']['color']) ?>">
										<?= htmlspecialchars($user['role']['label']) ?>
									</span>
								</td>
								<td><?= date('d M Y, H:i', strtotime($user['created_at'])) ?></td>
								<td>
									<button class="btn btn-success btn-sm">
										<i class="bx bx-edit-alt me-1"></i> Edit
									</button>
									<button class="btn btn-danger btn-sm" <?= $user['isDisabled'] ? 'disabled' : '' ?>>
										<i class="bx bx-trash me-1"></i> Delete
									</button>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<?php if (empty($users)): ?>
					<p class="text-center mt-3">No users found.</p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
