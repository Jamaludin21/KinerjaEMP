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

<!-- Modal Add User -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" role="dialog"
	aria-hidden="false" aria-modal="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="addUserForm">
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

<!-- Modal Delete User -->
<div class="modal fade" id="deleteBtnUser" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content bg-white">
			<div class="modal-header">
				<h5 class="modal-title text-primary">Delete User</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form>
					<input type="hidden" name="id" id="id" class="form-control">
				</form>
				<p>Are you sure to delete this data user ?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">cancel</button>
				<button type="button" class="btn btn-danger" id="DeleteUser">Delete</button>
			</div>
		</div>
	</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
	$(document).ready(function () {
		$("#addUserForm").on("submit", function (e) {
			e.preventDefault();
			$.ajax({
				type: "POST",
				url: "<?= base_url('addUser') ?>",
				data: $(this).serialize(),
				dataType: "JSON",
				success: function (response) {
					if (response.status === "success") {
						$("#addUserModal").modal("hide");
						Swal.fire("Success", response.message, "success").then(function () {
							window.location.reload();
						});
					} else {
						Swal.fire("Error", response.message, "error");
					}
				},
				error: function () {
					Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data.', 'error');
				}
			});
		});
	});

	$(document).on('click', '.deleteBtnUser', function () {
		var id = $(this).data('id');
		$('#id').val(id);
	});

	$(document).on('click', '#DeleteUser', function () {
		var id = $('#id').val();

		$.ajax({
			url: "<?= site_url('deleteUser/') ?>" + id,
			type: 'DELETE',
			dataType: 'JSON',
			success: function (response) {
				if (response.status === 'success') {
					$("#deleteBtnUser").modal("hide");
					Swal.fire('Success', response.message, 'success').then(function () {
						window.location.reload();
					});
				} else {
					Swal.fire('Error', response.message, 'error').then(function () {
						window.location.reload();
					});
				}
			},
			error: function (xhr, status, error) {
				console.log(status, error)
				Swal.fire('Error', 'An error occurred while deleting data.', 'error').then(function () {
					window.location.reload();
				});
			}
		});
	})
</script>


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
