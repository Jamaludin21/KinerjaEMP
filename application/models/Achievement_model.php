<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Achievement_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function get_rekap_by_employee($employee_id)
	{
		return $this->db->get_where('achievements', [
			'employee_id' => $employee_id
		])->result();
	}


	public function get_by_employee($employee_id, $month, $year)
	{
		return $this->db->get_where('achievements', [
			'employee_id' => $employee_id,
			'month' => $month,
			'year' => $year
		])->row();
	}

	public function get_by_supervisor($supervisor_id, $month, $year)
	{
		$this->db->select('a.*, u.username as employee_username, e.id as employee_id');
		$this->db->from('achievements a');
		$this->db->join('employees e', 'a.employee_id = e.id');
		$this->db->join('users u', 'e.user_id = u.id');
		$this->db->where('e.supervisor_id', $supervisor_id);
		$this->db->where('a.month', $month);
		$this->db->where('a.year', $year);
		return $this->db->get()->result();
	}

	public function upsert($data)
	{
		$existing = $this->get_by_employee($data['employee_id'], $data['month'], $data['year']);
		if ($existing) {
			$this->db->where('id', $existing->id)->update('achievements', $data);
		} else {
			$this->db->insert('achievements', $data);
		}
	}
}
