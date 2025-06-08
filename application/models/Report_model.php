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
		$this->db->select('r.*, e.id as employee_id, u.username as employee_username');
		$this->db->from('reports r');
		$this->db->join('employees e', 'r.employee_id = e.id', 'left');
		$this->db->join('users u', 'e.user_id = u.id', 'left');
		$this->db->where('r.status', 'Submitted');
		$this->db->order_by('r.submitted_at', 'DESC');
		return $this->db->get()->result();
	}

	// Ambil laporan yang sudah dievaluasi (Approved atau Evaluated)
	public function getArchivedReports()
	{
		$this->db->select('r.*, e.id as employee_id, u.username as employee_username');
		$this->db->from('reports r');
		$this->db->join('employees e', 'r.employee_id = e.id', 'left');
		$this->db->join('users u', 'e.user_id = u.id', 'left');
		$this->db->where_in('r.status', ['Approved', 'Evaluated']);
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
		$this->db->select('r.*, e.id as employee_id, u.username as employee_username');
		$this->db->from('reports r');
		$this->db->join('employees e', 'r.employee_id = e.id', 'left');
		$this->db->join('users u', 'e.user_id = u.id', 'left');
		$this->db->where('r.employee_id', $employee_id);
		$this->db->order_by('r.submitted_at', 'DESC');
		return $this->db->get()->result();
	}

}
