<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Report_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); // Load the database library
	}

	/**
	 * Fetch reports submitted by staff and awaiting evaluation by kepala seksi.
	 *
	 * @return array List of submitted reports.
	 */
	// Ambil laporan yang statusnya 'Submitted'
	public function getSubmittedReports()
	{
		$this->db->select('
		r.*,
		e.id as employee_id,
		u.username as employee_username,
		r.assigned_by as supervisor_id,
		sup.username as supervisor_username,
		role.role_name as supervisor_role_name
	');
		$this->db->from('reports r');
		$this->db->join('employees e', 'r.employee_id = e.id', 'left');
		$this->db->join('users u', 'e.user_id = u.id', 'left'); // untuk employee
		$this->db->join('users sup', 'r.assigned_by = sup.id', 'left'); // untuk supervisor
		$this->db->join('role', 'sup.role = role.id ', 'left'); // Supervisor Role
		$this->db->where('r.status', 'Submitted');
		$this->db->order_by('r.submitted_at', 'DESC');

		return $this->db->get()->result();
	}


	// Ambil laporan yang sudah dievaluasi (Approved atau Evaluated)
	public function getArchivedReports($currentUserId, $employee_id, $currentUserRole)
	{
		$this->db->select('r.*, e.id as employee_id, u.username as employee_username,r.assigned_by as supervisor_id,
		sup.username as supervisor_username,
		role.role_name as supervisor_role_name');
		$this->db->from('reports r');
		$this->db->join('employees e', 'r.employee_id = e.id');
		$this->db->join('users u', 'e.user_id = u.id');
		$this->db->join('users sup', 'r.assigned_by = sup.id'); // untuk supervisor
		$this->db->join('role', 'sup.role = role.id '); // Supervisor Role
		$this->db->where_in('r.status', ['Approved', 'Evaluated']);

		// Jika bukan lurah (role 1), filter berdasarkan employee_id
		if ($currentUserRole != 1) {
			if (in_array($currentUserRole, [2, 3, 4, 5])) {
				// Only get reports of employees supervised by current user
				$this->db->where('e.supervisor_id', $currentUserId);
			}

			// Optional additional filter by specific employee (e.g., dropdown select)
			if ($employee_id !== null) {
				$this->db->where('r.employee_id', $employee_id);
			}
		}

		$this->db->order_by('r.evaluated_at', 'DESC');
		return $this->db->get()->result();
	}


	public function getReportsBySupervisor($supervisor_id)
	{
		$this->db->select('r.*, e.id as employee_id, u.username as employee_username');
		$this->db->from('reports r');
		$this->db->join('employees e', 'r.employee_id = e.id', 'left');
		$this->db->join('users u', 'e.user_id = u.id', 'left');
		$this->db->where('r.status', 'Submitted');
		$this->db->where('e.supervisor_id', $supervisor_id); // You must ensure `supervisor_id` exists in `employees`
		$this->db->order_by('r.submitted_at', 'DESC');
		return $this->db->get()->result();
	}

	public function getReportsByStaff($employee_id)
	{
		$this->db->select('r.*, e.id as employee_id, u.username as employee_username, r.assigned_by as supervisor_id,
		sup.username as supervisor_username,
		role.role_name as supervisor_role_name');
		$this->db->from('reports r');
		$this->db->join('employees e', 'r.employee_id = e.id', 'left');
		$this->db->join('users u', 'e.user_id = u.id', 'left');
		$this->db->join('users sup', 'r.assigned_by = sup.id', 'left'); // untuk supervisor
		$this->db->join('role', 'sup.role = role.id ', 'left'); // Supervisor Role
		$this->db->where_in('r.status', 'Submitted');
		$this->db->where('r.employee_id', $employee_id);
		$this->db->order_by('r.submitted_at', 'DESC');
		return $this->db->get()->result();
	}

	public function createTask($data)
	{
		$data['created_at'] = date('Y-m-d H:i:s');
		$this->db->insert('reports', $data);
	}

	public function submitReport($report_id, $file)
	{
		$this->db->where('id', $report_id);
		$this->db->update('reports', [
			'report_file' => $file,
			'submitted_at' => date('Y-m-d H:i:s'),
		]);
	}

	public function evaluateReport($report_id, $status, $evaluation = null)
	{
		$this->db->where('id', $report_id);
		$this->db->update('reports', [
			'status' => $status,
			'evaluation' => $evaluation,
			'evaluated_at' => date('Y-m-d H:i:s'),
		]);
	}

}
