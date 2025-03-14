<?php
defined('BASEPATH') or exit('No direct script access allowed');

class action extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library(['form_validation', 'session']);
		$this->load->model('User_model');
	}

	public function add_user()
	{
		$this->form_validation->set_rules('username', 'Username', 'required|trim');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
		$this->form_validation->set_rules('role', 'Role', 'required');

		if ($this->form_validation->run() === FALSE) {
			echo json_encode(['status' => 'error', 'message' => validation_errors()]);
			return;
		}

		// Default password and encryption
		$default_password = "123456";
		$hashed_password = password_hash($default_password, PASSWORD_BCRYPT);

		$data = [
			'username' => $this->input->post('username', true),
			'email' => $this->input->post('email', true),
			'role' => $this->input->post('role', true),
			'password' => $hashed_password,
			'created_at' => date('Y-m-d H:i:s')
		];

		$insert = $this->User_model->insert_user($data);

		if ($insert) {
			echo json_encode(['status' => 'success', 'message' => 'Success add user']);
		} else {
			echo json_encode(['status' => 'failed', 'message' => 'Failed to add user.']);
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



}
