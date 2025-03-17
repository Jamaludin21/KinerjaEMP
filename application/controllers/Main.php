<?php
defined('BASEPATH') or exit('No direct script access allowed');

class main extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library(['form_validation', 'session', 'pagination']);
		$this->load->model('User_model');
		$this->sessionUserId = $this->session->userdata('user_id');
		$this->sessionUserRole = $this->session->userdata('role');
		$this->roleLabels = [
			1 => ['id' => 1, 'label' => 'Lurah', 'color' => 'danger'],
			2 => ['id' => 2, 'label' => 'Kepala Sekretariat', 'color' => 'primary'],
			3 => ['id' => 3, 'label' => 'Kepala Kesejahteraan Sosial', 'color' => 'primary'],
			4 => ['id' => 4, 'label' => 'Kepala Pemerintahan dan Trantibum', 'color' => 'primary'],
			5 => ['id' => 5, 'label' => 'Kepala Pemberdayaan Masyarakat dan Pembangunan', 'color' => 'primary'],
		];
	}

	// Render function to handle layout structure dynamically
	private function render($view, $data = [], $is_login = false)
	{
		$data['session'] = $this->session->userdata();
		$this->load->view('Layout/Header', $data);

		if (!$is_login) {
			$this->load->view('Layout/Sidebar', $data);
			$this->load->view('Layout/Navbar', $data);
		}

		$this->load->view($view, $data);
		$this->load->view('Layout/Footer', $data);
	}

	// Ensure User is Logged In
	private function require_login()
	{
		if (!$this->session->userdata('user_id')) {
			redirect('login');
		}
	}

	// Display Login Page (Only Header, Content, and Footer)
	public function login()
	{
		$this->render('Auth/Login', ['title' => 'Login'], true);
	}

	// Dashboard
	public function index()
	{
		$this->require_login();
		$this->render('Content/index', ['title' => 'Main Dashboard']);
	}


	// Users Page
	public function users()
	{
		$this->require_login();
		$userdata = $this->User_model->get_all_users();
		$roles = $this->User_model->get_role();

		// Process user roles and disable conditions
		$usersData = [];
		foreach ($userdata as $user) {
			$role = $this->roleLabels[$user['role']] ?? ['label' => 'Unknown', 'color' => 'secondary'];
			$isDisabled = $user['id'] == $this->sessionUserId && $user['role'] == $this->sessionUserRole;

			$usersData[] = [
				'id' => $user['id'],
				'username' => $user['username'],
				'email' => $user['email'],
				'role' => $role,
				'created_at' => $user['created_at'],
				'isDisabled' => $isDisabled
			];
		}

		// Pass processed data to the view
		$this->render('Content/users', [
			'title' => 'Data Pengguna',
			'users' => $usersData,
			'roles' => $roles
		]);
	}


	// Recap Page
	public function recap()
	{
		$this->require_login();
		$this->render('Content/recap', ['title' => 'Data Rekap']);
	}

	// Evaluated Page
	public function evaluated()
	{
		$this->require_login();
		$this->render('Content/evaluated', ['title' => 'Data Penilaian']);
	}

	// Achievement Page
	public function achievement()
	{
		$this->require_login();
		$this->render('Content/achievement', ['title' => 'Data Pencapaian']);
	}

}
