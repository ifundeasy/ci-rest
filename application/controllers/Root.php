<?php
/**
 * Created by IntelliJ IDEA.
 * User: rappresent
 * Date: 26/06/19
 * Time: 6.44 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Root extends CI_Controller {
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 *        http://example.com/index.php/welcome
	 *    - or -
	 *        http://example.com/index.php/welcome/index
	 *    - or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index() {
		$this->load->library('Authorization');
		$logged = $this->authorization->user;

		if (empty($logged)) $logged = false;

		$result = array(
			"text" => "Welcome",
			"logged" => $logged,
			"time" => new DateTime(null)
		);

		header('Content-type: application/json');
		echo json_encode($result);
	}
}
