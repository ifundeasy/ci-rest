<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sec_user extends MY_Model {
	var $tablename = "sec_user";
	var $pkey = "user_id";
	var $error;
	private function get_password($username, $password) {
		return sha1($username . $password);
	}
	public function get_by_username($username, $password) {
		$result = $this->db
			->select('user_id, username, email, fullname, roles')
			->from($this->tablename)
			->where('username', $username)
			->where('password', $this->get_password($username, $password))
			->where('status', 1)
			->get();
		$user = $result->row();
		if ($user == null)
			return null;

		return $user;
	}
	public function get_by_code($select, $code) {
		$result = $this->db
			->select($select)
			->from($this->tablename)
			->where('activation_code', $code)
			->get();
		$user = $result->row();
		if ($user == null)
			return null;

		return $user;
	}
	public function user_activating_by_code($old, $new) {
		return $this->db->update($this->tablename, array("status" => 1, "activation_code" => $new), array("activation_code" => $old));
	}
	public function reset_password_by_code($old, $new, $user, $pass) {
		$password = $this->get_password($user, $pass);

		return $this->db->update($this->tablename, array("activation_code" => $new, "password" => $password), array("activation_code" => $old));
	}
	public function get_by_id($user_id) {
		$result = $this->db
			->select('user_id, username, fullname, email, roles, status')
			->from($this->tablename)
			->where('user_id', $user_id)
			->get();
		$user = $result->row();

		return $user;
	}
	public function get($params = null) {
		$query = $this->db
			->select('user_id, username, email, fullname, roles, status')
			->from($this->tablename);
		if ($params != null) {
			if (isset($params->offset)) {
				$query = $query->limit($params->limit, $params->offset);
				// $query = $query->limit(1,0);
			}
			if (isset($params->filter)) {
				foreach ($params->filter as $field => $value) {
					switch ($field) {
						case 'username':
							$query = $query->like($field, $value);
							break;
						default :
							$query = $query->where($field, $value);
					}
				}
			}
			if (isset($params->order)) {
				foreach ($params->order as $value) {
					$query = $query->order_by($value);
				}
			}
		}
		$result = $query->get();
		//var_dump($this->db->last_query());
		//exit;
		$users = $result->result();
		if ($users == null)
			return null;

		return $users;
	}
	public function get_total_record($query, $limit) {
		$q = str_replace('LIMIT ' . $limit, '', $query);
		//var_dump($q);
		//exit;
		$total_sql = "SELECT COUNT(*) AS cnt FROM ( " . $q . " ) t";
		$rs_total = $this->db->query($total_sql)->row();
		if ($rs_total != null) {
			$total = $rs_total->cnt;
		} else {
			$total = 0;
		}

		return $total;
	}
	public function create($data) {

		if ($this->is_username_exists($data->username)) {
			$this->error['code'] = 11;
			$this->error['message'] = "Username existed";

			return false;
		}
		$data->password = $this->get_password($data->username, $data->password);
		$data->roles = json_encode($data->roles);
		if (parent::create($data)) {
			return $this->db->insert_id();
		} else {
			$this->error = $this->db->error();

			return false;
		}
	}
	public function update($user_id, $data) {
		if (!empty($data->password)) {
			$data->password = $this->get_password($data->username, $data->password);
		}
		$data->roles = json_encode($data->roles);
		parent::update($user_id, $data);
		if ($this->db->error()['code'] == 0) {
			return true;
		} else {
			$this->error = $this->db->error();

			return false;
		}
	}
	public function delete($user_id) {
		//$this->load->model('sec_user');
		//$this->sec_user->delete($user_id);
		$this->db->where($this->pkey, $user_id);
		$this->db->delete($this->tablename);
	}
	public function is_username_exists($username) {
		$result = $this->db
			->from($this->tablename)
			->where("username", $username)
			->get();

		//var_dump($this->db->last_query());
		return !empty($result->row());
	}
	public function get_DT_data($dt) {
		$select = "
			SELECT user_id, username, fullname, email, roles, status
			FROM sec_user
		";
		$where = $dt->filter;
		$where_clause = '';
		if ($where != null) {
			$where_clause = " where user_id LIKE '%{$where}%'";
		}
		$sql = $select . $where_clause;
		$total_sql = "SELECT COUNT(*) AS cnt FROM ( " . $sql . " ) t";
		$rs_total = $this->db->query($total_sql)->row();
		if ($rs_total != null) {
			$total = $rs_total->cnt;
		} else {
			$total = 0;
		}
		$orderBy = "";
		if ($dt->order_by != '') {
			$orderBy = $orderBy . " ORDER BY " . $dt->order_by . " " . $dt->order_dir;
		}
		$sql = $sql . $orderBy;
		$sql .= " LIMIT {$dt->start}, {$dt->length}";
		$rs = $this->db->query($sql)->result();
		$result = (object)[];
		$result->draw = $dt->draw;
		$result->recordsTotal = $total;
		$result->recordsFiltered = $total;
		$result->data = $rs;

		return $result;
	}
	public function get_total($month = null, $year = null) {
		if ($month != null) {
			$this->db->where('MONTH(sec_user.created_date) =', $month);
		}
		if ($year != null) {
			$this->db->where('YEAR(sec_user.created_date) =', $year);
		}
		$this->db->from($this->tablename);

		return $this->db->count_all_results();
		// return $this->db->count_all($this->tablename);
	}
	public function verify_user($data, $masuk) {
		$this->db->where('activation_code', $data);
		$q = $this->db->update($this->tablename, $masuk);
		if ($q) {
			return true;
		} else {
			return false;
		}
	}
}
