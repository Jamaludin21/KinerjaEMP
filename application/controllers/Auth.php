<?php
defined('BASEPATH') or exit('No direct script access allowed');

class auth extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library(['form_validation', 'session']);

	}

	// Validate User Credentials
	private function validate_credentials($username_or_email, $password)
	{
		$field = filter_var($username_or_email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
		$user = $this->db->get_where('users', [$field => $username_or_email])->row_array();
		return ($user && password_verify($password, $user['password'])) ? $user : null;
	}

	// Handle Login Submission
	public function postLogin()
	{
		$this->form_validation->set_rules('username_or_email', 'Username or Email', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');

		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', 'Login failed. Please try again.');
			redirect('login');
		}

		$username_or_email = $this->input->post('username_or_email');
		$password = $this->input->post('password');

		if ($user = $this->validate_credentials($username_or_email, $password)) {
			$this->session->set_userdata([
				'user_id' => $user['id'],
				'username' => $user['username'],
				'email' => $user['email'],
				'role' => $user['role'],
				'logged_in' => TRUE
			]);
			redirect('');
		} else {
			$this->session->set_flashdata('error', 'Invalid username/email or password');
			redirect('login');
		}
	}

	// Logout
	public function logout()
	{
		$this->session->sess_destroy();
		redirect('login');
	}


}
