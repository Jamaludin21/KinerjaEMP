<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function count_users()
	{
		return $this->db->count_all('users');
	}

	public function getEmployeeByUserId($userId)
	{
		return $this->db->where('user_id', $userId)->get('employees')->row();
	}

	// Fetch all users
	public function get_all_users($currentUserId, $currentRoleId)
	{
		if ($currentRoleId == 1) {
			// Lurah can see all users
			$this->db->select('id, role, username, email, created_at');
			$this->db->from('users');
			$this->db->order_by('role', 'ASC');
			$this->db->order_by('created_at', 'DESC');
			$this->db->where('role != 6');
		} elseif (in_array($currentRoleId, [2, 3, 4, 5])) {
			// Kepala Seksi/Divisi: show themselves + their employees
			$this->db->select('u.id, u.role, u.username, u.email, u.created_at');
			$this->db->from('users u');
			$this->db->join('employees e', 'u.id = e.user_id', 'left');
			$this->db->where("(u.id = $currentUserId OR e.supervisor_id = $currentUserId)");
		}

		$query = $this->db->get();
		return $query->result_array();
	}

	public function getStaffBySupervisor($supervisor_id)
	{
		$this->db->select('e.id, u.username');
		$this->db->from('employees e');
		$this->db->join('users u', 'e.user_id = u.id');
		$this->db->where('e.supervisor_id', $supervisor_id);
		return $this->db->get()->result();
	}


	public function get_role()
	{
		$this->db->select('*');
		$this->db->from('role');
		$query = $this->db->get();
		return $query->result_array();
	}

	// Insert user
	public function insert_user($data)
	{
		return $this->db->insert('users', $data);
	}

	public function update_user($data)
	{
		return $this->db->update('users', $data);
	}
}
