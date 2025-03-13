<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	// Fetch all users
	public function get_all_users()
	{
		$this->db->select('id, role, username, email, created_at');
		$this->db->from('users');
		$this->db->order_by('role', 'ASC'); // Order by role first (ascending)
		$this->db->order_by('created_at', 'DESC'); // Order by latest users
		$query = $this->db->get();
		return $query->result_array();
	}
}
