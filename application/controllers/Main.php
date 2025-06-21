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
		$this->load->model('Report_model');
		$this->load->model('Achievement_model');
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
		$data['is_login'] = $is_login; // Pass this flag to views
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
		$staffPresensi = [];
		$employeeId = null;
		$presensiList = [];
		$presensiBulanan = [];

		$user = $this->db->get_where('users', ['id' => $currentUserId])->row();
		$employee = $this->db->get_where('employees', ['user_id' => $currentUserId])->row();

		if (in_array($currentUserRole, [2, 3, 4, 5, 6]) && !$employee) {
			$this->session->set_flashdata('error', 'Data pegawai tidak ditemukan.');
			redirect('');
		}

		if ($currentUserRole === 6) {
			// Pegawai
			$employeeId = $userId;
			$userId = $currentUserId;
			$presensiList = $this->Presensi_model->get_presensi_by_employee($employeeId, $bulan, $tahun);
			$user = $this->db->get_where('users', ['id' => $userId])->row();
		} else if (in_array($currentUserRole, [2, 3, 4, 5])) {
			// Head of Division
			$employeeId = $employee->id;
			$employeeUserId = $employee->user_id;
			$presensiList = $this->Presensi_model->get_presensi_by_employee($employeeId, $bulan, $tahun);
			$staffList = $this->db->get_where('employees', ['supervisor_id' => $employeeUserId])->result();

			$staffPresensi = [];
			foreach ($staffList as $staff) {
				$presensi = $this->Presensi_model->get_presensi_by_employee($staff->id, $bulan, $tahun);
				if (!empty($presensi)) {
					foreach ($presensi as $p) {
						// Tambahkan role & email ke $p
						$userStaff = $this->db->get_where('users', ['id' => $staff->user_id])->row();
						$role = $userStaff->role ?? null;
						$p->employee_email = $userStaff->email ?? '-';
						$p->roleLabel = (object) ($this->roleLabels[$role] ?? ['label' => 'Unknown', 'color' => 'secondary']);
						$staffPresensi[] = $p;
						$presensiBulanan[$p->employee_id] = $this->Presensi_model->getPresensiBulanan($p->employee_id, $bulan, $tahun);
					}
				}
			}
		} else if ($currentUserRole === 1) {
			// Lurah / Admin
			$presensiList = $this->Presensi_model->get_presensi_all_today();
			foreach ($presensiList as $p) {
				// Fetch user's role from users table if role not part of presensi object
				if (!isset($p->role)) {
					$emp = $this->db->get_where('employees', ['id' => $p->employee_id])->row();
					if ($emp) {
						$userRole = $this->db->get_where('users', ['id' => $emp->user_id])->row()->role ?? null;
						$roleLabelInfo = $this->roleLabels[$userRole] ?? ['label' => 'Unknown', 'color' => 'secondary'];
					} else {
						$roleLabelInfo = ['label' => 'Unknown', 'color' => 'secondary'];
					}
				} else {
					$roleLabelInfo = $this->roleLabels[$p->role] ?? ['label' => 'Unknown', 'color' => 'secondary'];
				}

				// Attach roleLabel info to presensi object
				$p->roleLabel = (object) $roleLabelInfo;

				// Also get monthly summary for employee
				$presensiBulanan[$p->employee_id] = $this->Presensi_model->getPresensiBulanan($p->employee_id, $bulan, $tahun);
			}
		} else {
			$this->session->set_flashdata('error', 'Akses tidak diizinkan.');
			redirect('');
		}

		$data = [
			'title' => 'Data Rekap',
			'data' => [
				'presensiBulanan' => $presensiBulanan,
				'presensi' => $presensiList ?? [],
				'staffPresensi' => $staffPresensi ?? [],
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



	// report Page
	public function report()
	{
		$this->require_login();
		$currentUserId = $this->sessionUserId;
		$currentUserRole = (int) $this->sessionUserRole;
		$supervisedStaff = [];
		$employee = $this->User_model->getEmployeeByUserId($currentUserId);


		if ($currentUserRole == 1) {
			$submittedReports = $this->Report_model->getSubmittedReports();
			$archivedReports = $this->Report_model->getArchivedReports(null, null, $currentUserRole);
		} elseif (in_array($currentUserRole, [2, 3, 4, 5])) {
			$submittedReports = $this->Report_model->getReportsBySupervisor($currentUserId);
			$archivedReports = $this->Report_model->getArchivedReports($currentUserId, null, $currentUserRole);
			$supervisedStaff = $this->User_model->getStaffBySupervisor($currentUserId);
		} elseif ($currentUserRole == 6) {
			$submittedReports = $this->Report_model->getReportsByStaff($employee->id);
			$archivedReports = $this->Report_model->getArchivedReports(null, $employee->id, $currentUserRole);
		} else {
			$this->session->set_flashdata('error', 'Tidak ada izin untuk akses halaman ini!');
			redirect('');
		}


		$data = [
			'title' => 'Data Laporan',
			'submittedReports' => $submittedReports,
			'supervisedStaff' => $supervisedStaff,
			'archivedReports' => $archivedReports,
			'role' => $currentUserRole,
		];

		$this->render('Content/report', $data);
	}


	// Achievement Page
	public function achievement()
	{
		$this->require_login();
		$role = (int) $this->session->userdata('role');
		$userId = $this->session->userdata('user_id');
		$employee = $this->db->get_where('employees', ['user_id' => $userId])->row();
		$bulan = $this->input->get('bulan') ?? date('m');
		$tahun = $this->input->get('tahun') ?? date('Y');

		$achievementData = [];

		if (in_array($role, [2, 3, 4, 5])) {
			// Kepala departemen, menilai bawahannya
			$staffList = $this->db->get_where('employees', ['supervisor_id' => $userId])->result();
			foreach ($staffList as $staff) {
				$user = $this->db->get_where('users', ['id' => $staff->user_id])->row();
				$nilai = $this->Achievement_model->get_by_employee($staff->id, $bulan, $tahun);
				$rekap = $this->Achievement_model->get_rekap_by_employee($staff->id);
				$achievementData[] = [
					'employee' => $staff,
					'user' => $user,
					'nilai' => $nilai,
					'rekap' => $rekap
				];
			}
		} elseif ($role === 6) {
			// Staff hanya melihat dirinya
			$user = $this->db->get_where('users', ['id' => $userId])->row();
			$nilai = $this->Achievement_model->get_by_employee($employee->id, $bulan, $tahun);
			$rekap = $this->Achievement_model->get_rekap_by_employee($employee->id);
			$achievementData[] = [
				'employee' => $employee,
				'user' => $user,
				'nilai' => $nilai,
				'rekap' => $rekap
			];
		} elseif ($role === 1) {
			// Admin melihat semua
			$this->db->select('e.*, u.username');
			$this->db->from('employees e');
			$this->db->join('users u', 'e.user_id = u.id');
			$staffList = $this->db->get()->result();

			foreach ($staffList as $staff) {
				$nilai = $this->Achievement_model->get_by_employee($staff->id, $bulan, $tahun);
				$rekap = $this->Achievement_model->get_rekap_by_employee($staff->id);
				$achievementData[] = [
					'employee' => $staff,
					'user' => (object)['username' => $staff->username],
					'nilai' => $nilai,
					'rekap' => $rekap
				];
			}
		} else {
			show_error('Akses tidak diizinkan', 403);
		}

		$data = [
			'title' => 'Rekap Pencapaian Kinerja',
			'bulan' => $bulan,
			'tahun' => $tahun,
			'data' => $achievementData,
			'role' => $role
		];

		$this->render('Content/achievement', $data);
	}
}
