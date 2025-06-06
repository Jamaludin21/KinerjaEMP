<div class="content-backdrop fade"></div>
</div>
<!-- Content wrapper -->
</div>
<!-- / Layout page -->
</div>

<!-- Overlay -->
<div class="layout-overlay layout-menu-toggle"></div>
</div>
<!-- / Layout wrapper -->

<?php if (!isset($is_login) || !$is_login): ?>

	<!-- Modal Add User -->
	<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" role="dialog"
		aria-hidden="false" aria-modal="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form class="global-ajax-form" data-url="<?= base_url('saveUser') ?>" method="POST">
					<div class="modal-body">
						<div class="mb-3">
							<label for="username" class="form-label">Username</label>
							<input type="text" class="form-control" id="username" name="username" required>
						</div>
						<div class="mb-3">
							<label for="email" class="form-label">Email</label>
							<input type="email" class="form-control" id="email" name="email" required>
						</div>
						<div class="mb-3">
							<label for="role" class="form-label">Role</label>
							<select class="form-select" id="role" name="role" required>
								<option value="" disabled selected>Pilih Role</option>
								<?php foreach ($roles as $role): ?>
									<option value="<?= htmlspecialchars($role['id']) ?>">
										<?= htmlspecialchars($role['role_name']) ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary">Add User</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Modal Edit User -->
	<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" role="dialog"
		aria-hidden="false" aria-modal="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form class="global-ajax-form" data-url="<?= base_url('saveUser') ?>" method="POST">
					<div class="modal-body">
						<input type="hidden" id="editUserId" name="id">
						<div class="mb-3">
							<label for="username" class="form-label">Username</label>
							<input type="text" class="form-control" id="editUsername" name="username" required>
						</div>
						<div class="mb-3">
							<label for="email" class="form-label">Email</label>
							<input type="email" class="form-control" id="editEmail" name="email" required>
						</div>
						<div class="mb-3">
							<label for="role" class="form-label">Role</label>
							<select class="form-select" id="editRole" name="role" required>
								<option value="" disabled selected>Pilih Role</option>
								<?php foreach ($roles as $role): ?>
									<option value="<?= htmlspecialchars($role['id']) ?>">
										<?= htmlspecialchars($role['role_name']) ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary">Update User</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Modal Delete User -->
	<div class="modal fade" id="deleteModal" tabindex="-1">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content bg-white">
				<div class="modal-header">
					<h5 class="modal-title text-danger">Delete Data</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<input type="hidden" id="deleteId">
					<input type="hidden" id="url">
					<p>Are you sure you want to delete this data?</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
					<button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
				</div>
			</div>
		</div>
	</div>


	<!-- Modal Clock In -->
	<div class="modal fade" id="clockInModal" tabindex="-1" aria-labelledby="clockInModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Absen Clock In</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
				</div>
				<form action="<?= base_url('clockIn') ?>" method="POST">
					<div class="modal-body">
						<div class="mb-3">
							<label for="clock_in_date" class="form-label">Tanggal</label>
							<input type="date" class="form-control" id="clock_in_date" value="<?= $data['tanggal'] ?>"
								disabled>
							<input type="hidden" name="clock_in_date_hidden" id="clock_in_date_hidden"
								value="<?= $data['tanggal'] ?>">
						</div>
						<div class="mb-3">
							<label for="clock_in" class="form-label">Jam Clock In</label>
							<input type="time" class="form-control" name="clock_in_time" id="clock_in_time" disabled>
							<input type="hidden" name="clock_in_time_hidden" id="clock_in_time_hidden">
						</div>
						<!-- Hidden employee id from session -->
						<input type="hidden" name="employee_id" id="employee_id" value="<?= $data['employee_id'] ?>">
						<input type="hidden" name="clock_in_location_latitude" id="clock_in_location_latitude">
						<input type="hidden" name="clock_in_location_longitude" id="clock_in_location_longitude">
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary">Submit Clock In</button>
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Modals for Presensi Bulanan -->
	<?php if (!empty($data['presensi'])): ?>
		<?php
		$unique_employee_ids = [];
		foreach ($data['presensi'] as $p):
			$employee_id = $p->employee_id;
			$employee_name = $p->employee_name;

			if (!in_array($employee_id, $unique_employee_ids)) {
				$unique_employee_ids[] = $employee_id;
				$bulanan = $data['presensiBulanan'][$employee_id] ?? [];
				?>

				<div class="modal fade" id="modalPresensi_<?= $employee_id ?>" tabindex="-1"
					aria-labelledby="modalLabel_<?= $employee_id ?>" aria-hidden="true">
					<div class="modal-dialog modal-dialog-centered modal-lg">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="modalLabel_<?= $employee_id ?>">Rekap Bulanan <?= $employee_name ?></h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">
								<?php if (!empty($bulanan)): ?>
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
													<td class="text-center">
														<span
															class="badge rounded-pill <?= $b->status_in === 'ontime' ? 'bg-success' : 'bg-danger' ?>">
															<?= $b->status_in ?? '-' ?>
														</span>
													</td>
													<td class="text-center">
														<span
															class="badge rounded-pill 
														<?= $b->status_out === 'ontime' ? 'bg-success' : ($b->status_out === 'early' ? 'bg-warning' : 'bg-info') ?>">
															<?= $b->status_out ?? 'Belum Clock Out' ?>
														</span>
													</td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								<?php else: ?>
									<p>Tidak ada data presensi bulanan.</p>
								<?php endif; ?>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
							</div>
						</div>
					</div>
				</div>

			<?php } ?>
		<?php endforeach; ?>
	<?php endif; ?>



	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.all.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
	<script>
		$(document).ready(function () {
			// Global AJAX function for CRUD operations
			function sendAjaxRequest(url, type, data, modalId) {
				console.log(url, type, data, modalId)
				$.ajax({
					url: url,
					type: type,
					data: data,
					dataType: "JSON",
					success: function (response) {
						$(modalId).modal("hide");
						if (response.status === "success") {
							Swal.fire("Success", response.message, "success").then(function () {
								window.location.reload();
							});
						} else {
							Swal.fire("Error", response.message, "error");
						}
					},
					error: function () {
						$(modalId).modal("hide");
						Swal.fire("Error", "An error occurred while processing your request.", "error");
					}
				});
			}

			// Handle all form submissions dynamically (Create & Update)
			$(document).on("submit", ".global-ajax-form", function (e) {
				e.preventDefault();
				let form = $(this);
				let url = form.data("url"); // Get dynamic action URL
				let type = form.attr("method") || "POST"; // Default to POST if not specified
				let data = form.serialize();
				let modalId = "#" + form.closest(".modal").attr("id"); // Get modal ID dynamically
				sendAjaxRequest(url, type, data, modalId);
			});

			// Open Edit Modal & Dynamically Populate Fields
			$(document).on("click", ".open-edit-modal", function () {
				let modal = $($(this).data("target")); // Get target modal dynamically
				let form = modal.find(".global-ajax-form"); // Find form inside modal
				let url = $(this).data("url"); // Get dynamic URL
				let id = $(this).data("id");
				let username = $(this).data("username");
				let email = $(this).data("email");
				let role = $(this).data("role");

				$("#editUserId").val(id);
				$("#editUsername").val(username);
				$("#editEmail").val(email);
				$("#editRole").val(role);

				// Set form action URL
				form.data("url", url);

				modal.modal("show");
			});

			// Open Delete Modal & Set Dynamic Data
			$(document).on("click", ".open-delete-modal", function () {
				let modal = $($(this).data("target"));
				let deleteId = $(this).data("id");
				let url = $(this).data("url");
				$("#url").val(url);

				modal.modal("show");
			});

			// Handle Delete Action
			$(document).on("click", "#confirmDelete", function () {
				let url = $("#url").val();

				if (!url) {
					console.error("Error: URL is undefined");
					return;
				}
				sendAjaxRequest(url, "DELETE", {}, "#deleteModal");
			});
		});

		document.addEventListener('DOMContentLoaded', function () {

			// Clock In Modal Handling
			const clockInModal = document.getElementById('clockInModal');
			if (clockInModal) {
				clockInModal.addEventListener('show.bs.modal', function () {
					const now = new Date();

					const dateStr = now.toISOString().split('T')[0];
					const timeStr = now.toTimeString().split(' ')[0].slice(0, 5); // HH:MM

					// Set disabled fields
					document.getElementById('clock_in_date').value = dateStr;
					document.getElementById('clock_in_time').value = timeStr;

					// Set hidden fields for form submission
					document.getElementById('clock_in_date_hidden').value = dateStr;
					document.getElementById('clock_in_time_hidden').value = timeStr;


					// Geolocation
					const locationInputLatitude = document.getElementById('clock_in_location_latitude');
					const locationInputLongitude = document.getElementById('clock_in_location_longitude');
					if (navigator.geolocation && locationInputLatitude && locationInputLongitude) {
						navigator.geolocation.getCurrentPosition(
							pos => {
								locationInputLatitude.value = `${pos.coords.latitude}`;
								locationInputLongitude.value = `${pos.coords.longitude}`;
							},
							err => {
								console.warn('Geolocation error:', err.message);
								locationInputLatitude.value = 'Unknown';
								locationInputLongitude.value = 'Unknown';
							}
						);
					} else {
						locationInputLatitude.value = 'Not supported';
						locationInputLongitude.value = 'Not supported';
					}
				});
			}

			// Clock Out Button Form Handling
			document.querySelectorAll('.clock-out-form').forEach(form => {
				form.addEventListener('submit', function (e) {
					e.preventDefault(); // Prevent immediate form submission

					const locationInputLatitude = form.querySelector("input[name='clock_out_location_latitude']");
					const locationInputLongitude = form.querySelector("input[name='clock_out_location_longitude']");

					if (navigator.geolocation && locationInputLatitude && locationInputLongitude) {
						// Show loading state
						const submitButton = form.querySelector('button[type="submit"]');
						const originalText = submitButton.textContent;
						submitButton.disabled = true;
						submitButton.textContent = 'Getting location...';

						navigator.geolocation.getCurrentPosition(
							position => {
								locationInputLatitude.value = `${position.coords.latitude}`;
								locationInputLongitude.value = `${position.coords.longitude}`;

								// Submit the form after getting location
								form.submit();
							},
							err => {
								console.warn("Geolocation error:", err.message);
								locationInputLatitude.value = 'Unknown';
								locationInputLongitude.value = 'Unknown';

								// Submit the form even if geolocation fails
								form.submit();
							},
							{
								timeout: 10000, // 10 second timeout
								enableHighAccuracy: true
							}
						);
					} else {
						if (locationInputLatitude && locationInputLongitude) {
							locationInputLatitude.value = 'Not supported';
							locationInputLongitude.value = 'Not supported';
						}
						// Submit the form
						form.submit();
					}
				});
			});

		});
	</script>

<?php endif; ?>


<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->

<script src="<?= base_url('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js'); ?>"></script>

<script src="<?= base_url('assets/vendor/js/menu.js'); ?>"></script>
<!-- endbuild -->

<!-- Vendors JS -->
<script src="<?= base_url('assets/vendor/libs/apex-charts/apexcharts.js'); ?>"></script>

<!-- Main JS -->
<script src="<?= base_url('assets/js/main.js'); ?>"></script>

<!-- Page JS -->
<script src="<?= base_url('assets/js/dashboards-analytics.js'); ?>"></script>

<!-- Place this tag in your head or just before your close body tag. -->
<script async defer src="https://buttons.github.io/buttons.js'); ?>"></script>
</body>

</html>
