<?php
defined('BASEPATH') OR exit ('No direct script access allowed');

class Log extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model('Log_process');
	}
	public function total() { //todo: please test this
		$total = $this->Log_process->total();
		$result = array();
		$result["total"] = $total;
		echo json_encode($result);
	}
	public function list() { //todo: please test this
		$limit = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$arrWhere = array();
		$arrLike = array();
		$sql = $this->Log_process->getAll($limit, $offset, $arrWhere, $arrLike);
		$result['data'] = $sql->data;
		$result['total'] = $sql->total;
		echo json_encode($result);
	}
	public function update_status() { //todo: please test this
		$data = json_decode(file_get_contents('php://input'));
		$process_name = $data->process_name;
		unset($data->process_name);
		$this->db->trans_begin();
		try {

			$updateLog = $this->Log_process->update($process_name, $data);
			if ($updateLog) {
				$this->db->trans_commit();
				$result['message'] = 'Log has been updated';
			} else {
				$this->db->trans_rollback();
				http_response_code(444);
				$result['error'] = $this->Log_process->error;
			}
		} catch (Exception $e) {
			$this->db->trans_rollback();
			http_response_code(444);
			$result['error'] = $e->getMessage();
		}
		header('Content-Type: application/json');
		echo json_encode($result);
	}
}
