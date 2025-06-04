<?php
defined('BASEPATH') or exit('No direct script access allowed');

class main extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library(['form_validation', 'session', 'pagination']);
		$this->load->model('User_model');
		$this->load->model('Presensi_model');
		$this->sessionUserId = $this->session->userdata('user_id');
		$this->sessionUserRole = $this->session->userdata('role');
		$this->roleLabels = [
			1 => ['id' => 1, 'label' => 'Lurah', 'color' => 'danger'],
			2 => ['id' => 2, 'label' => 'Kepala Sekretariat', 'color' => 'primary'],
			3 => ['id' => 3, 'label' => 'Kepala Kesejahteraan Sosial', 'color' => 'primary'],
			4 => ['id' => 4, 'label' => 'Kepala Pemerintahan dan Trantibum', 'color' => 'primary'],
			5 => ['id' => 5, 'label' => 'Kepala Pemberdayaan Masyarakat dan Pembangunan', 'color' => 'primary'],
			6 => ['id' => 6, 'label' => 'Pegawai', 'color' => 'warning'],
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
		$data = [
			'title' => 'Main Dashboard',
			'penggunaCount' => $this->User_model->count_users(),
			'absensiCount' => $this->Presensi_model->count_absensi(),
		];
		$this->render('Content/index', $data);
	}

	// Users Page
	public function users()
	{
		$this->require_login();
		$currentUserId = $this->session->userdata('user_id');
		$currentRoleId = $this->session->userdata('role');
		$userdata = $this->User_model->get_all_users($currentUserId, $currentRoleId);
		$roles = $this->User_model->get_role();

		// ⛔ Apply role-based restrictions:
		$filteredRoles = array_filter($roles, function ($role) use ($currentRoleId) {
			$roleId = (int) $role['id'];

			// ✅ Lurah (1) cannot assign Pegawai (6)
			if ($currentRoleId === 1 && $roleId === 6) {
				return false;
			}

			// ✅ Head of Division (2-5) can only assign Pegawai (6)
			if (in_array($currentRoleId, [2, 3, 4, 5]) && $roleId !== 6) {
				return false;
			}

			// Allow all others
			return true;
		});

		// Process user roles and disable conditions
		$usersData = [];
		foreach ($userdata as $user) {
			$role = $this->roleLabels[$user['role']] ?? ['label' => 'Unknown', 'color' => 'secondary'];
			$isDisabled = $user['id'] == $currentUserId && $user['role'] == $currentRoleId;

			$usersData[] = [
				'id' => $user['id'],
				'username' => $user['username'],
				'email' => $user['email'],
				'role' => $role,
				'created_at' => $user['created_at'],
				'isDisabled' => $isDisabled,
				'currentRoleId' => $currentRoleId
			];
		}

		// Pass processed and filtered data to the view
		$this->render('Content/users', [
			'title' => 'Data Pengguna',
			'users' => $usersData,
			'roles' => $filteredRoles // ⬅️ Use filtered roles for role select dropdown
		]);
	}

	// Recap Page
	public function recap()
	{
		$this->require_login();

		$bulan = $this->input->get('bulan') ?? date('m');
		$tahun = $this->input->get('tahun') ?? date('Y');
		$tanggal = $this->input->get('tanggal') ?? date('Y-m-d');

		$currentUserId = $this->sessionUserId;
		$currentUserRole = (int) $this->sessionUserRole;

		$presensiList = [];
		$presensiBulanan = [];
		$user = null;

		if ($currentUserRole === 6) {
			// Pegawai
			$employee = $this->db->get_where('employees', ['user_id' => $currentUserId])->row();
			if (!$employee) {
				$this->session->set_flashdata('error', 'Data pegawai tidak ditemukan.');
				redirect('');
			}

			$employeeId = $employee->id;
			$userId = $currentUserId;
			$presensiList = $this->Presensi_model->get_presensi_by_employee($employeeId, $bulan, $tahun);
			$user = $this->db->get_where('users', ['id' => $userId])->row();
		} elseif (in_array($currentUserRole, [2, 3, 4, 5])) {
			// Head of Division
			$presensiList = $this->Presensi_model->get_presensi_by_supervisor($currentUserId, $tanggal);
			foreach ($presensiList as $p) {
				$presensiBulanan[$p->employee_id] = $this->Presensi_model->getPresensiBulanan($p->employee_id, $bulan, $tahun);
			}
			$user = $this->db->get_where('users', ['id' => $currentUserId])->row();
		} elseif ($currentUserRole === 1) {
			// Lurah / Admin
			$presensiList = $this->Presensi_model->get_presensi_all_today($tanggal);
			foreach ($presensiList as $p) {
				$presensiBulanan[$p->employee_id] = $this->Presensi_model->getPresensiBulanan($p->employee_id, $bulan, $tahun);
			}
			$user = $this->db->get_where('users', ['id' => $currentUserId])->row();
		} else {
			$this->session->set_flashdata('error', 'Akses tidak diizinkan.');
			redirect('');
		}

		$data = [
			'title' => 'Data Rekap',
			'data' => [
				'presensiBulanan' => $presensiBulanan,
				'presensi' => $presensiList,
				'user' => $user,
				'employee_id' => $employeeId ?? null,
				'user_id' => $userId ?? null,
				'bulan' => $bulan,
				'tahun' => $tahun,
				'tanggal' => $tanggal
			]
		];

		$this->render('Content/recap', $data);
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
