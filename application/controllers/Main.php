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
		$this->form_validation->set_rules('username_or_email', 'Username or Email', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');

		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', 'Login failed. Please try again.');
			redirect('login');
		} else {
			$username_or_email = $this->input->post('username_or_email');
			$password = $this->input->post('password');

			if (filter_var($username_or_email, FILTER_VALIDATE_EMAIL)) {
				$this->db->where('email', $username_or_email);
			} else {
				$this->db->where('username', $username_or_email);
			}

			$query = $this->db->get('users');
			$user = $query->row_array();

			if ($user && password_verify($password, $user['password'])) {
				$user_data = array(
					'user_id' => $user['id'],
					'username' => $user['username'],
					'email' => $user['email'],
					'role' => $user['role'],
					'logged_in' => TRUE
				);
				$this->session->set_userdata($user_data);
				redirect('');
			} else {
				$this->session->set_flashdata('error', 'Invalid username/email or password');
				redirect('login');
			}
		}
	}
	public function logout()
	{
		$this->session->sess_destroy();
		redirect('login');
	}



	public function index() {
		$data['title'] = "Main Dashboard";

		$this->load->view('Layout/Header', $data);
		$this->load->view('Layout/Sidebar');
		$this->load->view('Layout/Navbar');
		$this->load->view('Content/index');
		$this->load->view('Layout/Footer');
	}


	public function users() {
		$data['title'] = "Data Pengguna";

		$this->load->view('Layout/Header', $data);
		$this->load->view('Layout/Sidebar');
		$this->load->view('Layout/Navbar');
		$this->load->view('Content/users');
		$this->load->view('Layout/Footer');
	}

}
