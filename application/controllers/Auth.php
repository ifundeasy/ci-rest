<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MY_Controller {
	var $_ALLOWED = array();
	public function login() {
		$data = json_decode(file_get_contents('php://input'));
		$username = empty($data->username) ? '' : $data->username;
		$password = empty($data->password) ? '' : $data->password;
		if ($username == '' || $password == '') {
			http_response_code(401);
			$result['error']['code'] = 401;
			$result['error']['message'] = 'Incorrect username or password';
		} else {
			$this->load->model('sec_user');
			$user = $this->sec_user->get_by_username($username, $password);
			if ($user == null) {
				http_response_code(401);
				$result['error']['code'] = 401;
				$result['error']['message'] = 'Incorrect username or password';
			} else {
				$this->load->model('sec_session');
				$token = $this->generate_token($username);
				$result = $user;
				$result->token = $token;
				$this->sec_session->create((object)array('user_id' => $user->user_id, 'token' => $token));
			}
		}
		header('Content-Type: application/json');
		echo json_encode($result);
	}
	public function logout() {
		$this->load->library('Authorization');
		if (empty($this->authorization->user)) {
			$result['message'] = 'Failed logging out';
		} else {
			$this->load->model('sec_session');
			$this->sec_session->delete($this->authorization->user->token);
			$result['message'] = 'Logout success';
		}
		header('Content-Type: application/json');
		echo json_encode($result);
	}
	private function generate($username, $password) {
		echo sha1($username . $password);
	}
	private function generate_token($username) {
		return sha1($username . time());
	}
	/*
	Body : {
		"username": <username>,
		"password": <password>
	}
	*/
	public function login_android() { //todo: please test this
		$data = json_decode(file_get_contents('php://input'));
		$username = empty($data->username) ? '' : $data->username;
		$password = empty($data->password) ? '' : $data->password;
		if ($username == '' || $password == '') {
			http_response_code(401);
			$result['error']['code'] = 401;
			$result['error']['message'] = 'Incorrect username or password';
		} else {
			$this->load->model('sec_user');
			$user = $this->sec_user->get_by_username($username, $password);
			if ($user == null) {
				http_response_code(401);
				$result['error']['code'] = 401;
				$result['error']['message'] = 'Incorrect username or password';
			} else {
				$this->load->model('sec_session');
				$token = $this->generate_token($username);
				$user->token = $token;
				$result['user'] = $user;
				$this->sec_session->create((object)array('user_id' => $user->user_id, 'token' => $token));
			}
		}
		header('Content-Type: application/json');
		echo json_encode($result);
	}
}
