<?php
/**
 * Created by IntelliJ IDEA.
 * User: rappresent
 * Date: 25/06/19
 * Time: 4.26 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
	var $_ALLOWED = null;
	var $body = null;
	public function __construct() {
		parent::__construct();
	}
	protected function authorize($req_role = null) {
		if (empty($req_role)) {
			$req_role = $this->_ALLOWED;
		}
		if (empty($req_role)) {
			return true;
		} else {
			if ($this->authorization->user == null) {
				$this->return_error(401);
			}
			foreach ($this->authorization->user->roles as $role) {
				if (array_search($role, $req_role) !== false) {
					return true;
				}
			}
			$this->return_error(401);
		}
	}
	protected function parse_query_string($data) {
		foreach ($data as $key => $item) {
			switch ($key) {
				case 'order':
					$data['order'] = $this->parse_order($item);
				case 'offset' :
				case 'limit' :
					break;
				default :
					$data['filter'][$key] = $item;
					unset($data[$key]);
					break;
			}
		}

		return (object)$data;
	}
	protected function parse_order($data) {
		$items = explode(',', $data);
		$order = array();
		foreach ($items as $item) {
			$first = substr($item, 0, 1);
			if ($first == '-') {
				$order[] = substr($item, 1) . ' DESC';
			} else {
				$order[] = $item . ' ASC';
			}
		}

		return $order;
	}
	private function return_error($code, $msg = null) {
		http_response_code($code);
		$result['error']['code'] = $code;
		$result['error']['message'] = $this->get_message($code);
		header('Content-Type: application/json');
		echo json_encode($result);
		exit;
	}
	private function get_message($code, $msg = null) {
		if ($msg != null)
			return $msg;
		switch ($code) {
			case 401 :
				return 'User not authorized';
		}
	}
	public function view($view, $data = null) {
		$this->load->view("templates/header", $data);
		$this->load->view($view, $data);
		$this->load->view("templates/footer", $data);
	}
	public function formatNumber($number, $precision = 0) {
		$units = array('', 'K', 'M', 'T', 'B', 'Q');
		$number = max($number, 0);
		$pow = floor(($number ? log($number) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);
		// Uncomment one of the following alternatives
		// $number /= pow(1024, $pow);
		// $number /= (1 << (10 * $pow));
		switch ($pow) {
			case 5:
				$number = $number / 1000000000000000;
				break;
			case 4:
				$number = $number / 1000000000000;
				break;
			case 3:
				$number = $number / 1000000000;
				break;
			case 2:
				$number = $number / 1000000;
				break;
			case 1:
				$number = $number / 1000;
				break;
			default:
				$number = $number;
		}

		return number_format(round($number, $precision)) . ' ' . $units[$pow];
	}
	public function clean($string) {
		$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
		$string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

		return $string; // Replaces multiple hyphens with single one.
	}
}
