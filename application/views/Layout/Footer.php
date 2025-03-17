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
