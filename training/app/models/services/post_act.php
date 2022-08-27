<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
error_reporting(E_ERROR);
class Post_act extends CI_Model{
	function set($simponi_req, $dev) {
		$message = array("response" => array("result" => "ERROR", "message" => "Bad Request"));
		$message = "hello";
		if($dev=="dev") $dev = 1;
		else $dev = 0;
		if($simponi_req!=""){
			$post = json_decode($simponi_req, TRUE);
			if($post = $post["simponi_req"]){
				if($post['method']=="activeinquiry"){
					$data = $post['data'];
					$simponi_id = $data[0];
					$billing_id = $data[1];
					$ntpn = $data[2];
					$ntb = $data[3];
					$date_paid = $data[4];
					$bank_code = $data[5];
					$channel_code = $data[6];
					$simponi_req = str_replace("'","''", $simponi_req);
					$query = "INSERT INTO t_simponi_payment_request (simponi_id, billing_id, message, dummy) VALUES ('$simponi_id', '$billing_id', '$simponi_req', $dev)";
					$this->db->simple_query($query);
					$query = "INSERT INTO t_simponi_payment (simponi_id, billing_id, ntpn, ntb, date_paid, bank_code, channel_code, status, dummy) VALUES ('$simponi_id', '$billing_id', '$ntpn', '$ntb', '$date_paid', '$bank_code', '$channel_code', 'SP01', $dev)";
					$this->db->simple_query($query);
					if($this->db->affected_rows()>0){
						$post['respone_code'] = 3; // Sukses = 3, Gagal = 2
						$message = array("simponi_result" => $post);
						$query = "INSERT INTO t_simponi_payment_response (simponi_id, billing_id, message, dummy) VALUES ('$simponi_id', '$billing_id', '".str_replace("'", "''", json_encode($message))."', $dev)";
						$this->db->simple_query($query);
					}elseif($this->db->_error_number()==1062){
						$post['respone_code'] = 2;
						$message = array("simponi_result" => $post);
						$query = "INSERT INTO t_simponi_payment_response (simponi_id, billing_id, message, dummy) VALUES ('$simponi_id', '$billing_id', '".str_replace("'", "''", json_encode($message))."', $dev)";
						$this->db->simple_query($query);
					}else{
						$query = "INSERT INTO t_simponi_payment_response (simponi_id, billing_id, message, dummy) VALUES ('$simponi_id', '$billing_id', NULL, $dev)";
						$this->db->simple_query($query);
					}
				}else{
					$message = array("response" => array("result" => "ERROR", "message" => "Bad Method"));
				}
			}
		}
		return $message;
	}
}