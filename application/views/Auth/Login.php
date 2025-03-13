<body class="d-flex align-items-center justify-content-center vh-100">
	<!-- Content -->
	<div class="container-xxl max-h-auto">
		<div class="authentication-wrapper authentication-basic container-p-y">
			<div class="authentication-inner">
				<!-- Register -->
				<div class="card">
					<div class="card-body">
						<h4 class="mb-2">Welcome! 👋</h4>
						<p class="mb-4">Please sign-in to your account</p>
						<?php if ($this->session->flashdata('success')): ?>
							<div class="alert alert-success" role="alert">
								<?= $this->session->flashdata('success') ?>
							</div>
						<?php endif; ?>
						<?php if ($this->session->flashdata('error')): ?>
							<div class="alert alert-danger" role="alert">
								<?= $this->session->flashdata('error') ?>
							</div>
						<?php endif; ?>
						<form id="formAuthentication" class="mb-3" action="<?= base_url('postLogin'); ?>" method="POST">
							<div class="mb-3">
								<label for="email" class="form-label">Email or Username</label>
								<input type="text" class="form-control" id="username_or_email" name="username_or_email"
									placeholder="Enter your email or username" autofocus />
							</div>
							<div class="mb-3 form-password-toggle">
								<div class="d-flex justify-content-between">
									<label class="form-label" for="password">Password</label>
								</div>
								<div class="input-group input-group-merge">
									<input type="password" id="password" class="form-control" name="password"
										placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
										aria-describedby="password" />
									<span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
								</div>
							</div>
							<div class="mb-3">
								<button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
							</div>
						</form>
					</div>
				</div>
				<!-- /Register -->
			</div>
		</div>
	</div>
