<?php

date_default_timezone_set('Asia/Jakarta');
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

	public function getByDateAndEmployee($employee_id, $tanggal)
	{
		return $this->db->where('employee_id', $employee_id)
			->where('DATE(created_at)', $tanggal)
			->get('presensi')
			->row();
	}

	public function getById($id)
	{
		return $this->db->where('id', $id)->get('presensi')->row();
	}



	public function get_presensi_by_employee($employeeId, $bulan, $tahun)
	{
		$this->db->select('p.*, u.username as employee_name, u.email as employee_email, u.id as user_id');
		$this->db->from('presensi p');
		$this->db->join('employees e', 'p.employee_id = e.id');
		$this->db->join('users u', 'e.user_id = u.id');
		$this->db->where('employee_id', $employeeId);
		$this->db->where('MONTH(p.created_at)', $bulan);
		$this->db->where('YEAR(p.created_at)', $tahun);
		$this->db->order_by('p.created_at');
		return $this->db->get()->result();
	}


	public function get_presensi_all_today($date)
	{
		$this->db->select('p.*, u.username as employee_name, u.email as employee_email');
		$this->db->from('presensi p');
		$this->db->join('employees e', 'p.employee_id = e.id');
		$this->db->join('users u', 'e.user_id = u.id');
		$this->db->where('DATE(p.created_at)', $date);
		$this->db->group_by('p.employee_id');
		return $this->db->get()->result();
	}

	public function getPresensiBulanan($userId, $bulan, $tahun)
	{
		$this->db->where('employee_id', $userId);
		$this->db->where('MONTH(created_at)', $bulan);
		$this->db->where('YEAR(created_at)', $tahun);
		$this->db->order_by('created_at');
		return $this->db->get('presensi')->result();
	}

	public function has_clocked_in_today($employeeId)
	{
		$this->db->where('employee_id', $employeeId);
		$this->db->where('DATE(created_at)', date('Y-m-d'));
		return $this->db->get('presensi')->row();
	}

	public function clock_in($userId, $employee_id, $clock_in_location_latitude, $clock_in_location_longitude)
	{
		$employee = $this->db->get_where('employees', ['user_id' => $userId])->row();
		if (!$employee)
			return false;

		$now = date('Y-m-d H:i:s');
		$time = date('H:i:s');

		$status_in = ($time <= '08:00:00') ? 'ontime' : 'late';

		$data = [
			'employee_id' => $employee_id,
			'clock_in' => $now,
			'status_in' => $status_in,
			'latitude_in' => $clock_in_location_latitude,
			'longitude_in' => $clock_in_location_longitude,
			'created_at' => $now
		];

		$this->db->insert('presensi', $data);
		return $this->db->insert_id();
	}

	public function clock_out($userId, $employee_id, $presensi_id, $clock_out_location_latitude, $clock_out_location_longitude)
	{
		$employee = $this->db->get_where('employees', ['user_id' => $userId])->row();
		if (!$employee)
			return false;

		$now = date('Y-m-d H:i:s');
		$time = date('H:i:s');

		$status_out = ($time < '17:00:00') ? 'early' : 'ontime';

		$this->db->where('employee_id', $employee_id);
		$this->db->where('id', $presensi_id);
		$this->db->where('DATE(created_at)', date('Y-m-d'));
		$lastPresensi = $this->db->get('presensi')->row();

		if ($lastPresensi && $lastPresensi->clock_out === null) {
			$this->db->where('id', $lastPresensi->id);
			return $this->db->update('presensi', [
				'clock_out' => $now,
				'status_out' => $status_out,
				'latitude_out' => $clock_out_location_latitude,
				'longitude_out' => $clock_out_location_longitude
			]);
		}

		return false;
	}


}
