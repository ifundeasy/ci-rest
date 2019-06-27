<?php
/**
 * Created by IntelliJ IDEA.
 * User: rappresent
 * Date: 25/06/19
 * Time: 4.26 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model {
	var $tablename;
	var $pkey;
	var $error;
	public function __construct() {
		parent::__construct();
	}
	public function get($params = null) {
		$query = $this->db->from($this->tablename);
		if ($params == null) {
		} else {
			if (isset($params->offset)) {
				$query = $query->limit($params->offset, $params->limit);
			}
			if (isset($params->filter)) {
				foreach ($params->filter as $field => $value) {
					$query = $query->where($field, $value);
				}
			}
			if (isset($params->order)) {
				foreach ($params->order as $field => $value) {
					$query = $query->order_by($field, $value);
				}
			}
		}

		return $query->get()->result();
	}
	public function get_by_id($id) {
		$query = $this->db
			->from($this->tablename)
			->where($this->pkey, $id)
			->get();

		return $query->row();
	}
	public function insert($data) {

		return $this->create($data);
	}
	public function create($data) {
		if (empty($data->created_by)) {
			if (!isset(get_instance()->authorization) || get_instance()->authorization->user == null) {
				$data->created_by = 'system';
			} else {
				$data->created_by = get_instance()->authorization->user->username;
			}
		}
		$data->created_date = date("Y-m-d H:i:s");
		if ($this->db->insert($this->tablename, $data)) {
			return true;
		} else {
			$this->error = $this->db->error();

			return false;
		}
	}
	public function update($id, $data) {
		if (empty($data->updated_by)) {
			if (!isset(get_instance()->authorization) || get_instance()->authorization->user == null) {
				$data->updated_by = 'system';
			} else {
				$data->updated_by = get_instance()->authorization->user->username;
			}
		}
		$data->updated_date = date("Y-m-d H:i:s");
		$this->db->update($this->tablename, $data, array($this->pkey => $id));
	}
	public function delete($id) {
		$this->db->delete($this->tablename, array($this->pkey => $id));
	}
}
