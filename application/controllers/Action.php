<?php
defined('BASEPATH') or exit('No direct script access allowed');

class action extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->library(['form_validation', 'session']);
		$this->load->model('User_model');
		$this->load->model('Presensi_model');
		$this->load->model('Report_model');
		$this->sessionUserId = $this->session->userdata('user_id');
		$this->sessionUserRole = $this->session->userdata('role');
	}

	private function require_login()
	{
		if (!$this->session->userdata('user_id')) {
			redirect('login');
		}
	}

	public function save_user()
	{
		$this->form_validation->set_rules('username', 'Username', 'required|trim');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
		$this->form_validation->set_rules('role', 'Role', 'required');

		if ($this->form_validation->run() === FALSE) {
			echo json_encode(['status' => 'error', 'message' => validation_errors()]);
			return;
		}

		$id = $this->input->post('id', true);
		$password = $this->input->post('password', true);

		$data = [
			'username' => $this->input->post('username', true),
			'email' => $this->input->post('email', true),
			'role' => $this->input->post('role', true),
		];

		// Hash password if provided
		if (!empty($password)) {
			$data['password'] = password_hash($password, PASSWORD_BCRYPT);
		}

		// Get current session user info
		$currentUserId = $this->session->userdata('user_id');
		$currentUserRole = $this->session->userdata('role');

		$newRole = (int) $this->input->post('role', true);

		// Prevent Lurah from creating Pegawai
		if ($currentUserRole == 1 && $newRole == 6) {
			echo json_encode(['status' => 'error', 'message' => 'Lurah cannot create Pegawai']);
			return;
		}

		// Prevent Kepala Divisi from creating any role other than Pegawai
		if (in_array($currentUserRole, [2, 3, 4, 5]) && $newRole != 6) {
			echo json_encode(['status' => 'error', 'message' => 'You can only create Pegawai']);
			return;
		}


		if ($id) {
			// Update
			$this->db->where('id', $id);
			$update = $this->User_model->update_user($data);

			if ($update) {
				echo json_encode(['status' => 'success', 'message' => 'User updated successfully']);
			} else {
				echo json_encode(['status' => 'error', 'message' => 'Failed to update user']);
			}
		} else {
			// Create
			$data['password'] = password_hash("123456", PASSWORD_BCRYPT);
			$data['created_at'] = date('Y-m-d H:i:s');

			$insert = $this->User_model->insert_user($data);

			if ($insert) {
				$newUserId = $this->db->insert_id();

				// If current user is Head Division (2, 3, or 4), insert into employees table
				if (in_array($currentUserRole, [2, 3, 4, 5])) {
					$this->db->insert('employees', [
						'user_id' => $newUserId,
						'supervisor_id' => $currentUserId,
					]);
				}

				echo json_encode(['status' => 'success', 'message' => 'User added successfully']);
			} else {
				echo json_encode(['status' => 'error', 'message' => 'Failed to add user']);
			}
		}
	}



	public function delete_user($id)
	{
		$this->db->where('id', $id);
		$this->db->delete('users');

		$response = array();

		if ($this->db->affected_rows() > 0) {
			$response['status'] = 'success';
			$response['message'] = 'User Data deleted successfully';
		} else {
			$response['status'] = 'error';
			$response['message'] = 'Failed to delete User Data';
		}

		echo json_encode($response);
	}


	public function clock_in()
	{
		$employee_id = $this->input->post('employee_id');
		$clock_in_date = $this->input->post('clock_in_date_hidden');
		$clock_in_location_latitude = $this->input->post('clock_in_location_latitude');
		$clock_in_location_longitude = $this->input->post('clock_in_location_longitude');
		$currentUserId = $this->sessionUserId;

		if (!$clock_in_date || !$employee_id || !$clock_in_location_latitude || !$clock_in_location_longitude) {
			$this->session->set_flashdata('error', 'Data clock-in tidak lengkap.');
			return redirect('rekap');
		}

		// Prevent duplicate clock in for the same day
		$existing = $this->Presensi_model->getByDateAndEmployee($employee_id, $clock_in_date);
		if ($existing) {
			$this->session->set_flashdata('error', 'Anda sudah melakukan Clock In hari ini!');
			return redirect('rekap');
		}

		$this->Presensi_model->clock_in($currentUserId, $employee_id, $clock_in_location_latitude, $clock_in_location_longitude);
		$this->session->set_flashdata('success', 'Clock In berhasil.');
		return redirect('rekap');
	}


	public function clock_out()
	{
		$presensi_id = $this->input->post('presensi_id');
		$employee_id = $this->input->post('employee_id');
		$clock_out_location_latitude = $this->input->post('clock_out_location_latitude');
		$clock_out_location_longitude = $this->input->post('clock_out_location_longitude');
		$currentUserId = $this->sessionUserId;

		var_dump($presensi_id, $employee_id, $clock_out_location_latitude, $clock_out_location_longitude);

		if (!$employee_id || !$presensi_id || !$clock_out_location_latitude || !$clock_out_location_longitude) {
			$this->session->set_flashdata('error', 'Data clock-out tidak lengkap.');
			return redirect('rekap');
		}

		$presensi = $this->Presensi_model->getById($presensi_id);
		if (!$presensi || $presensi->clock_out) {
			$this->session->set_flashdata('error', 'Clock Out tidak valid.');
			return redirect('rekap');
		}

		$this->Presensi_model->clock_out($currentUserId, $employee_id, $presensi_id, $clock_out_location_latitude, $clock_out_location_longitude);
		$this->session->set_flashdata('success', 'Clock Out berhasil.');
		return redirect('rekap');
	}



	public function assignTask()
	{
		$this->require_login();
		$currentUserId = $this->sessionUserId;
		$currentUserRole = (int) $this->sessionUserRole;

		if (!in_array($currentUserRole, [2, 3, 4, 5])) {
			$this->session->set_flashdata('error', 'Unauthorized');
			redirect('laporan');
		}

		$employee_id = $this->input->post('employee_id');
		$title = $this->input->post('title');
		$description = $this->input->post('description');
		$file = $_FILES['file'];

		$filename = null;

		if ($file['name']) {
			$config['upload_path'] = './assets/uploads/tasks/';
			$config['allowed_types'] = 'pdf|doc|docx|xls|xlsx|csv';
			$config['file_name'] = time() . '_' . $file['name'];

			$this->load->library('upload', $config);
			if (!$this->upload->do_upload('file')) {
				$this->session->set_flashdata('error', $this->upload->display_errors());
				redirect('laporan');
			} else {
				$filename = $this->upload->data('file_name');
			}
		}

		$this->Report_model->createTask([
			'title' => $title,
			'description' => $description,
			'employee_id' => $employee_id,
			'assigned_by' => $currentUserId,
			'assigned_file' => $filename,
			'status' => 'Submitted',
		]);

		$this->session->set_flashdata('success', 'Tugas berhasil di submit kepada Staff anda');
		redirect('laporan');
	}

	public function upload()
	{
		$this->require_login();
		$currentUserRole = (int) $this->sessionUserRole;

		if ($currentUserRole != 6) {
			$this->session->set_flashdata('error', 'Unauthorized');
			redirect('laporan');
		}

		$report_id = $this->input->post('report_id');
		$file = $_FILES['file'];

		if (!$file['name']) {
			$this->session->set_flashdata('error', 'File tidak boleh kosong.');
			redirect('report');
		}

		var_dump($report_id, $file);

		$config['upload_path'] = './assets/uploads/reports/';
		$config['allowed_types'] = 'pdf|doc|docx|xls|xlsx|csv';
		$config['file_name'] = time() . '_' . $file['name'];

		$this->load->library('upload', $config);
		if (!$this->upload->do_upload('file')) {
			$this->session->set_flashdata('error', $this->upload->display_errors());
			redirect('laporan');
		}

		$this->Report_model->submitReport($report_id, $this->upload->data('file_name'));

		$this->session->set_flashdata('success', 'Tugas berhasil dikirim.');
		redirect('laporan');
	}

	public function evaluate()
	{
		$this->require_login();
		$currentUserRole = (int) $this->sessionUserRole;

		if (!in_array($currentUserRole, [2, 3, 4, 5])) {
			$this->session->set_flashdata('error', 'Unauthorized');
			redirect('laporan');
		}

		$report_id = $this->input->post('report_id');
		$status = $this->input->post('status');
		$evaluation = $this->input->post('evaluation');

		if (!in_array($status, ['Approved', 'Evaluated'])) {
			$this->session->set_flashdata('error', 'Status tidak valid.');
			redirect('laporan');
		}

		if ($status === 'Evaluated' && empty(trim($evaluation))) {
			$this->session->set_flashdata('error', 'Catatan evaluasi wajib diisi jika status revisi.');
			redirect('laporan');
		}

		$this->Report_model->evaluateReport($report_id, $status, $evaluation);

		$this->session->set_flashdata('success', 'Laporan berhasil dievaluasi.');
		redirect('laporan');
	}

	public function save_achieve()
	{
		$role = (int) $this->session->userdata('role');
		if (!in_array($role, [2, 3, 4, 5])) {
			show_error('Tidak diizinkan menilai', 403);
		}

		$data = $this->input->post();
		$data['created_by'] = $this->session->userdata('id');

		$this->Achievement_model->upsert($data);
		$this->session->set_flashdata('success', 'Penilaian berhasil disimpan.');
		redirect('pencapaian');
	}
}
