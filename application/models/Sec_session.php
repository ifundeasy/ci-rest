<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sec_session extends MY_Model {
	var $tablename = "sec_session";
	var $pkey = "token";
	private function get_expired_time($format = 'Y-m-d H:i:s') {
		return $this->get_expired_time_object()->format('Y-m-d H:i:s');
	}
	private function get_expired_time_object() {
		$max = $this->config->item('sess_expiration');
		if (empty($max))
			$max = 1200; //in seconds
		$t = new Datetime('now');
		$t->add(new DateInterval('PT' . $max . 'S'));

		return $t;
	}
	public function get_valid($token) {
		$session = $this->get_by_id($token);
		if ($session == null)
			return null;
		$valid_until = new Datetime($session->valid_until);
		$current = new Datetime('now');
		if ($current <= $valid_until) {
			return $session;
		}

		return null;
	}
	public function create($data) {
		$max = $this->get_expired_time();
		$data->valid_until = $max;
		$data->created_by = 'system';
		parent::create($data);
	}
	public function poke($token) {
		$max = $this->get_expired_time();
		$data = (object)[];
		$data->valid_until = $max;
		$this->db->update($this->tablename, $data, array('token' => $token));
	}
}
