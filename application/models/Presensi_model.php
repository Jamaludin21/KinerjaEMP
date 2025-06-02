<?php

class Presensi_model extends CI_Model
{
	protected $table = 'presensi';
	protected $primaryKey = 'id';

	protected $allowedFields = [
		'user_id',
		'clock_in',
		'clock_out',
		'status_in',
		'status_out',
		'latitude_in',
		'longitude_in',
		'latitude_out',
		'longitude_out',
		'created_at'
	];

	public function count_absensi()
	{
		return $this->db->count_all('presensi'); // or your attendance table name
	}


	public function get_presensi_by_employee($employeeId, $bulan, $tahun)
	{
		$this->db->where('employee_id', $employeeId);
		$this->db->where('MONTH(created_at)', $bulan);
		$this->db->where('YEAR(created_at)', $tahun);
		return $this->db->get('presensi')->result();
	}

	public function get_presensi_by_supervisor($supervisorUserId, $date)
	{
		$this->db->select('p.*, u.username as employee_name, u.email as employee_email, u.id as user_id');
		$this->db->from('presensi p');
		$this->db->join('employees e', 'p.employee_id = e.id');
		$this->db->join('users u', 'e.user_id = u.id');
		$this->db->where("e.supervisor_id", $supervisorUserId);
		$this->db->where('DATE(p.created_at)', $date);

		return $this->db->get()->result();
	}


	public function get_presensi_all_today($date)
	{
		$this->db->select('p.*, u.username as employee_name, u.email as employee_email');
		$this->db->from('presensi p');
		$this->db->join('employees e', 'p.employee_id = e.id');
		$this->db->join('users u', 'e.user_id = u.id');
		$this->db->where('DATE(p.created_at)', $date);
		return $this->db->get()->result();
	}

	public function getPresensiBulanan($userId, $bulan, $tahun)
	{
		$this->db->where('employee_id', $userId);
		$this->db->where('MONTH(created_at)', $bulan);
		$this->db->where('YEAR(created_at)', $tahun);
		return $this->db->get('presensi')->result();
	}

}
