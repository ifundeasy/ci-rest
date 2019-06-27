<?php
/**
 * Created by IntelliJ IDEA.
 * User: rappresent
 * Date: 25/06/19
 * Time: 4.26 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Curl {
	private function call($url, $data, $header, $method = "POST") {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		if ($method == "POST") {
			curl_setopt($ch, CURLOPT_POST, true);
		} else if ($method == "PUT") {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		} else if ($method == "DELETE") {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		}
		if ($data != null) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}
		if ($header != null) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		$output = curl_exec($ch);
		curl_close($ch);

		return $output;
	}
	public function get($url, $query = null, $header = null) {
		if ($query != null) {
			$query = http_build_query($query);
			$url = $url . '?' . $query;
		}

		return $this->call($url, null, $header, "GET");
	}
	public function post($url, $data = null, $query = null, $header = null, $format = "form") {
		if ($data != null && $format == 'json') {
			$data = json_encode($data);
		}
		if ($query != null) {
			$query = http_build_query($query);
			$url = $url . '?' . $query;
		}

		return $this->call($url, $data, $header, "POST");
	}
	public function update($url, $data = null, $query = null, $header = null, $format = "form") {
		if ($data != null && $format == 'json') {
			$data = json_encode($data);
		}
		if ($query != null) {
			$query = http_build_query($query);
			$url = $url . '?' . $query;
		}

		return $this->call($url, $data, $header, "PUT");
	}
	public function delete($url, $query = null, $header = null, $data = null, $format = "form") {
		if ($data != null && $format == 'json') {
			$data = json_encode($data);
		}
		if ($query != null) {
			$query = http_build_query($query);
			$url = $url . '?' . $query;
		}

		return $this->call($url, $data, $header, "DELETE");
	}
	function sendToApiAsJson($url, $data) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}
}

?>
