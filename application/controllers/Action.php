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
		$this->sessionUserId = $this->session->userdata('user_id');
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
		$currentUserId = $this->sessionUserId;

		if (!$clock_in_date || !$employee_id) {
			$this->session->set_flashdata('error', 'Data clock-in tidak lengkap.');
			return redirect('rekap');
		}

		// Prevent duplicate clock in for the same day
		$existing = $this->Presensi_model->getByDateAndEmployee($employee_id, $clock_in_date);
		if ($existing) {
			$this->session->set_flashdata('error', 'Anda sudah melakukan Clock In hari ini.');
			return redirect('rekap');
		}

		$this->Presensi_model->clock_in($currentUserId);
		$this->session->set_flashdata('success', 'Clock In berhasil.');
		return redirect('rekap');
	}


	public function clock_out()
	{
		$presensi_id = $this->input->post('presensi_id');

		$presensi = $this->Presensi_model->getById($presensi_id);
		if (!$presensi || $presensi->clock_out) {
			$this->session->set_flashdata('error', 'Clock Out tidak valid.');
			return redirect('presensi');
		}

		$this->Presensi_model->clock_out($presensi_id);
		$this->session->set_flashdata('success', 'Clock Out berhasil.');
		return redirect('rekap');
	}



}
