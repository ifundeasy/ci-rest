<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Controller {
	var $_ALLOWED = array(1);
	var $view_folder = "user";
	public function get($user_id = null) {
		$this->load->library('Authorization');
		$this->load->model('sec_user');
		if ($user_id == null) {
			$params = $this->parse_query_string($this->input->get());
			//var_dump($this->input->get());
			//exit;
			$result['data'] = $this->sec_user->get($params);
			$recordsTotal = $this->db->last_query();
			$result['recordsTotal'] = $this->sec_user->get_total_record($recordsTotal, $params->limit);
			$result['recordsFiltered'] = $result['recordsTotal'];
		} else {
			// get by user_id
			$result = $this->sec_user->get_by_id($user_id);
		}
		header('Content-Type: application/json');
		echo json_encode($result);
	}
	/*
	Header : Authorization Basic <token>
	Body : {
		"username": <username>,
		"password": <password>,
		"email": <email>,
		"fullname" : <fullname>,
		"roles" : [<roles#1>,<roles#2>,...,<roles#n>]
	}
	*/
	public function create() { //todo: please test this
		$data = json_decode(file_get_contents('php://input'));
		$this->load->library('Authorization');
		$this->authorize();
		$this->load->model("sec_user");
		if ($this->sec_user->create($data)) {
			$result['message'] = 'User has been created';
		} else {
			http_response_code(444);
			$result['error'] = $this->sec_user->error;
		}
		header('Content-Type: application/json');
		echo json_encode($result);
	}
	/*
	Header : Authorization Basic <token>
	Body : {
		"username": <username>,
		"password": <password>,
		"email": <email>,
		"fullname" : <fullname>,
		"roles" : [<roles#1>,<roles#2>,...,<roles#n>]
	}
	*/
	public function update($user_id) { //todo: please test this
		$data = json_decode(file_get_contents('php://input'));
		$this->load->library('Authorization');
		$this->authorize();
		$this->load->model("sec_user");
		if ($this->sec_user->update($user_id, $data)) {
			$result['message'] = 'User has been updated';
		} else {
			http_response_code(444);
			$result['error'] = $this->sec_user->error;
		}
		header('Content-Type: application/json');
		echo json_encode($result);
	}
	/*
	Header : Authorization Basic <token>
	*/
	public function delete($user_id) { //todo: please test this
		$this->load->library('Authorization');
		$this->authorize();
		$this->load->model("sec_user");
		$this->sec_user->delete($user_id);
	}
	public function get_public($user_id = null) { //todo: please test this
		$this->load->model('sec_user');
		if ($user_id == null) {
			$params = $this->parse_query_string($this->input->get());
			//echo json_encode($params);
			//get all
			$result['data'] = $this->sec_user->get($params);
		} else {
			// get by user_id
			$result['data'] = $this->sec_user->get_by_id($user_id);
		}
		header('Content-Type: application/json');
		echo json_encode($result);
	}
	/*
	Body : {
		"fullname" : <fullname>,
		"email": <email>,
		"password": <password>
	}
	*/
	public function create_android() { //todo: please test this
		$data = json_decode(file_get_contents('php://input'));
		$data->username = $data->email;
		$data->roles = [2];
		$data->created_by = 'system';
		$data->status = 1;
		$this->load->model("sec_user");
		if ($this->sec_user->create($data)) {
			$result['message'] = 'User has been created';
			header('Content-Type: application/json');
		} else {
			http_response_code(444);
			$result['error'] = $this->sec_user->error;
		}
		echo json_encode($result);
	}
	public function verification() { //todo: please test this
		$oldCode = $this->input->post('code');
		$newCode = sha1(rand(0, 1000));
		if (!isset($oldCode) || $oldCode == null) {
			http_response_code(444);
			$result['error'] = "Email Verification Failed";
			header('Content-Type: application/json');
			echo json_encode($result);
			exit;
		}
		$this->load->model("sec_user");
		try {
			$this->db->trans_begin();
			$userVerification = $this->sec_user->user_activating_by_code($oldCode, $newCode);
			$total = $this->db->affected_rows();
			if ($total > 0) {
				$this->db->trans_commit();
				http_response_code(200);
				$result['message'] = "Email Verification Successed";
			} else {
				$this->db->trans_rollback();
				http_response_code(444);
				$result['error'] = "Email Verification Failed";
			}
			header('Content-Type: application/json');
			echo json_encode($result);
		} catch (Exception $e) {
			http_response_code(444);
			$result['error'] = $e->getMessage();
			header('Content-Type: application/json');
			echo json_encode($result);
		}
	}
	public function check_code() { //todo: please test this
		$code = $this->input->post('code');
		if (!isset($code) || $code == null) {
			http_response_code(444);
			$result['error'] = "Code Not Found";
			header('Content-Type: application/json');
			echo json_encode($result);
			exit;
		}
		$this->load->model("sec_user");
		try {
			$getUserbyCode = $this->sec_user->get_by_code("username", $code);
			if (isset($getUserbyCode->username)) {
				http_response_code(200);
				$result['message'] = "Code Found";
				$result['data'] = $getUserbyCode;
			} else {
				$this->db->trans_rollback();
				http_response_code(444);
				$result['error'] = "Code Not Found";
			}
			header('Content-Type: application/json');
			echo json_encode($result);
		} catch (Exception $e) {
			http_response_code(444);
			$result['error'] = $e->getMessage();
			header('Content-Type: application/json');
			echo json_encode($result);
		}
	}
	public function forgot_password() { //todo: please test this
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$oldCode = $this->input->post('code');
		$newCode = sha1(rand(0, 1000));
		if (!isset($oldCode) || $oldCode == null) {
			http_response_code(444);
			$result['error'] = "Email Activation Failed";
			header('Content-Type: application/json');
			echo json_encode($result);
			exit;
		}
		$this->load->model("sec_user");
		try {
			$this->db->trans_begin();
			$resetPassword = $this->sec_user->reset_password_by_code($oldCode, $newCode, $username, $password);
			$total = $this->db->affected_rows();
			if ($total > 0) {
				$this->db->trans_commit();
				http_response_code(200);
				$result['message'] = "Reset Password Successed";
			} else {
				$this->db->trans_rollback();
				http_response_code(444);
				$result['error'] = "Reset Password Failed";
			}
			header('Content-Type: application/json');
			echo json_encode($result);
		} catch (Exception $e) {
			http_response_code(444);
			$result['error'] = $e->getMessage();
			header('Content-Type: application/json');
			echo json_encode($result);
		}
	}
	public function activation_code() {
		return sha1(time());
	}
	public function registration() { //todo: please test this
		$data = json_decode(file_get_contents('php://input'));
		//var_dump($data);
		//exit;
		$email = $data->email;
		$link = $data->link;
		unset($data->link);
		$data->roles = array(2);
		$this->load->model('sec_user');
		$data->activation_code = $this->activation_code();
		//var_dump($data);
		//exit;
		$id = $this->sec_user->create($data);
		//var_dump($this->sec_user->error);
		if ($id == false) {
			http_response_code(444);
			$result['error'] = "Username / Email Exist!";
			header('Content-Type: application/json');
			echo json_encode($result);
		} else {
			// $encrypted_id = md5($id);
			$encrypted_id = $data->activation_code;
			$this->load->library('email');
			$pengirim = $this->email->smtp_user;
			$this->email->from($pengirim);
			$this->email->to($email);
			$this->email->subject("Verifikasi Akun");
			$this->email->message(
				"Silahkan klik tautan <a href=" . $link . $encrypted_id . ">ini</a> untuk memverifikasi"
			);
			if ($this->email->send()) {
				$result['message'] = 'User has been created, check your email to verify';
			} else {
				$result['error'] = $this->email->print_debugger();
			}
			header('Content-Type: application/json');
			echo json_encode($result);
		}
	}
	public function verify() { //todo: please test this
		$data = json_decode(file_get_contents('php://input'));
		$this->load->model("sec_user");
		date_default_timezone_set('Asia/Jakarta');
		$now = date('Y-m-d H:i:s');
		$masuk = array(
			'status' => 1,
			'updated_date' => $now,
			'updated_by' => 'system'
		);
		$id = $this->sec_user->verify_user($data->key, $masuk);
		if ($id == true) {
			$result['message'] = 'User has been verified';
		} else {
			$result['error'] = 'Failed to verify';
		}
	}
	public function get_DT_data() { //todo: please test this
		$data = json_decode(file_get_contents('php://input'));
		//var_dump($data);
		//exit;
		//$this->load->library('Authorization');
		//$this->authorize();
		$this->load->model('sec_user');
		$result = $this->sec_user->get_DT_data($data);
		header('Content-Type: application/json');
		echo json_encode($result);
	}
	public function get_by_username() { //todo: please test this
		$data = json_decode(file_get_contents('php://input'));
		$this->load->model('sec_user');
		$result = $this->sec_user->get_by_username($data->username, $data->password);
		header('Content-Type: application/json');
		echo json_encode($result);
	}
}
