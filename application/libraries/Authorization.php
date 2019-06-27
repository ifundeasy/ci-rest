<?php
/**
 * Created by IntelliJ IDEA.
 * User: rappresent
 * Date: 25/06/19
 * Time: 4.26 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Authorization {
	private $CI;
	var $user = null;
	public function __construct() {
		$this->CI =& get_instance();
		$this->CI->load->model('sec_user');
		$this->CI->load->model('sec_session');
		$this->init();
	}
	private function init() {
		$headers = getallheaders();
		if (empty($headers['Authorization'])) {
			return;
		}
		$auth = explode(' ', $headers['Authorization']);
		if (count($auth) < 2) {
			return;
		} else {
			$token = $auth[1];
			$session = $this->CI->sec_session->get_valid($token);
			if ($session) {
				$user = $this->CI->sec_user->get_by_id($session->user_id);
				$user->roles = json_decode($user->roles);
				$user->token = $token;
				if ($user) {
					$this->user = $user;
					$this->CI->sec_session->poke($token);    // updating valid until
				} else {
					return;
				}
			}
		}
	}
}

?>
