<?php
defined('BASEPATH') OR exit ('No direct script access allowed');

class Log_process extends MY_Model {
	var $tablename = "log_process";
	var $error;
	var $pkey = "process_name";
	function getAll($limit, $offset, $arrWhere = null, $arrLike = null) {
		$data = (object)[];
		$this->db->select('*');
		$this->db->from($this->tablename);
		if ($arrWhere) {
			$this->db->where($arrWhere);
		}
		if ($arrLike) {
			$this->db->like($arrLike);
		}
		$data->total = $this->db->count_all_results();
		$this->db->select('*');
		$this->db->from($this->tablename);
		if ($arrWhere) {
			$this->db->where($arrWhere);
		}
		if ($arrLike) {
			$this->db->like($arrLike);
		}
		$this->db->limit($limit, $offset);
		$this->db->order_by("seq_num", "asc");
		$result = $this->db->get();
		$data->data = $result->result();
		$data->count = $result->num_rows();

		return $data;
	}
	public function total($arrWhere = null) {
		$this->db->from($this->tablename);
		if ($arrWhere) {
			$this->db->where($arrWhere);
		}

		return $this->db->count_all_results();
	}
	public function create($data) {
		if (parent::create($data)) {
			return $this->db->insert_id();
		} else {
			$this->error = $this->db->error();

			return false;
		}
	}
	public function update($process_name, $data) {
		parent::update($process_name, $data);
		if ($this->db->error()['code'] == 0) {
			return true;
		} else {
			$this->error = $this->db->error();

			return false;
		}
	}
	public function deleteWhere($arrWhere) {
		return $this->db->delete($this->tablename, $arrWhere);
	}
}
