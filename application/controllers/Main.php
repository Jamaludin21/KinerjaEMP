<?php
defined('BASEPATH') or exit('No direct script access allowed');

class main extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->library('session');
		$this->load->library('pagination');
	}


	public function login()
	{
		$data['title'] = 'Login';


		$this->load->view('Layout/Header', $data);
		$this->load->view('Auth/Login');
		$this->load->view('Layout/Footer');
	}

	public function postLogin()
	{
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		$this->form_validation->set_rules('password', 'Password', 'required');

		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', 'Login failed. Please try again.');
			redirect('login');
		} else {
			$email = $this->input->post('email');
			$password = $this->input->post('password');

			$this->db->where('email', $email);
			$query = $this->db->get('users');
			$user = $query->row_array();

			if ($user && password_verify($password, $user['password'])) {
				$user_data = array(
					'user_id' => $user['id'],
					'username' => $user['name'],
					'email' => $user['email'],
					'role' => $user['role'],
					'logged_in' => TRUE
				);
				$this->session->set_userdata($user_data);
				redirect('');
			} else {
				$this->session->set_flashdata('error', 'Invalid email or password');
				redirect('login');
			}
		}
	}



	public function logout()
	{
		$this->session->sess_destroy();
		redirect('login');
	}

	public function register()
	{
		$role = $this->db->get('role')->result_array();

		$data = array(
			'title' => 'Register',
			'role' => $role,
		);

		$this->load->view('Layout/Header', $data);
		$this->load->view('Auth/Register', $data);
		$this->load->view('Layout/Footer');
	}

	public function postRegister()
	{
		$this->form_validation->set_rules('name', 'Name', 'required');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]');
		$this->form_validation->set_rules('password', 'Password', 'required');
		$this->form_validation->set_rules('role', 'Role', 'required');

		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', 'Registration failed. Please try again.');
			redirect('register');
		} else {
			$data = array(
				'name' => $this->input->post('name'),
				'email' => $this->input->post('email'),
				'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
				'role' => $this->input->post('role'),
				'created_at' => date('Y-m-d H:i:s')
			);

			$this->load->database();

			$insert_result = $this->db->insert('users', $data);

			if ($insert_result) {
				$this->session->set_flashdata('success', 'Registration successful. You can now log in.');
				redirect('login');
			} else {
				$this->session->set_flashdata('error', 'Registration failed. Please try again.');
				redirect('register');
			}
		}
	}
}
