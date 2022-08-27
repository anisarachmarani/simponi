<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
error_reporting(E_ERROR);
class Billing_act extends CI_Model{
	function get($userid, $password, $data, $param, $ip, $env) {
		if($ip=="") return "<response><result>ERROR</result><code>99</code><message>Alamat IP Tidak Valid</message></response>";
		else if($data=="") return "<response><result>ERROR</result><code>99</code><message>Parameter Tidak Valid</message></response>";
		$ip = $this->main_act->get_ip($ip);
		$process_id = $this->main_act->get_process_id();
		$query = "SET GLOBAL sql_mode = 'ALLOW_INVALID_DATES'";
		$this->db->simple_query($query);
		
		// Proses AddOns - 00
		
		// Proses Billing - 01
		$update = $query = "CALL SetBilling('$data', '$userid', '$password', '$ip', $env)";
		$hasil = $this->main_act->get_result($query);
		if(!$hasil){
			$this->main_act->set_user_log($userid, $ip, "GetBilling", "Gagal, Fatal Error Proses Billing (01)", $update, "", $process_id);
			return "<response><result>ERROR</result><code>01</code><message>Fatal Error Proses Billing</message></response>";
		}
		$row = $query->row_array();
		if($row["result"]=="ERROR"){
			$message = $row["message"];
			$attach = $row["data"];
			if($attach=="") $attach = $data;
			$this->main_act->set_user_log($userid, $ip, "GetBilling", "Gagal, Proses Billing - $message (01)", $attach." - ".$update, "", $process_id);
			return "<response><result>".$row["result"]."</result><code>01</code><message>Proses Billing Gagal ($message)</message></response>";
		}
		$billing_id = $row['billing_id'];
		$serial = $row['serial'];
		$this->main_act->set_user_log($userid, $ip, "GetBilling", "Berhasil, Proses Billing (01)", "Log ID: $serial; $update", $billing_id, $process_id);
		
		// Proses JSON - 02
		$update = $query = "CALL GetBillingJSON($billing_id, $serial, $env)";
		$hasil = $this->main_act->get_result($query);
		if(!$hasil){
			$this->main_act->set_user_log($userid, $ip, "GetBilling", "Gagal, Fatal Error Proses Generate JSON (02)", $update, $billing_id, $process_id);
			return "<response><result>ERROR</result><code>02</code><message>Fatal Error Proses Generate JSON</message></response>";
		}
		$row = $query->row_array();
		if($row["result"]=="ERROR"){
			$message = $row["message"];
			$attach = $row["data"];
			if($attach=="") $attach = $data;
			$this->main_act->set_user_log($userid, $ip, "GetBilling", "Gagal, Proses Generate JSON - $message (02)", $attach." - ".$update, $process_id);
			return "<response><result>".$row["result"]."</result><code>02</code><message>Proses Generate JSON Gagal ($message)</message></response>";
		}
		$json = $row['json'];
		$json = array('simponi_req' => $json);
		$this->main_act->set_user_log($userid, $ip, "GetBilling", "Berhasil, Proses Generate JSON (02)", join($json, ""), $billing_id, $process_id);
		
		// Proses SIMPONI - 03
		$hasil = $this->send($json, $simponi_id, $simponi_bill, $date_simponi, $error, $receive, $env);
		if(!$hasil){
			$query = "CALL SetBillingJSON($billing_id, $serial, '', '', '', '$error', '')";
			$hasil = $this->main_act->get_result($query);
			$this->main_act->set_user_log($userid, $ip, "GetBilling", "Gagal, Fatal Error Proses Kirim Data Billing - $error(03)", join($json, ""), $billing_id, $process_id);
			return "<response><result>ERROR</result><code>03</code><message>Fatal Error Proses Kirim Data Billing</message></response>";
		}
		if($error!=""){
			$query = "CALL SetBillingJSON($billing_id, $serial, '', '', '', '$error', '$receive')";
			$hasil = $this->main_act->get_result($query);
			$this->main_act->set_user_log($userid, $ip, "GetBilling", "Gagal, Proses Kirim Data Billing - $error (03)", join($json, ""), $billing_id, $process_id);
			return "<response><result>ERROR</result><code>03</code><message>Proses Kirim Data Billing Gagal ($error)</message></response>";
		}
		$this->main_act->set_user_log($userid, $ip, "GetBilling", "Berhasil, Proses Kirim Data Billing (03)", join($json, ""), $billing_id, $process_id);
		
		// Proses SIMPONI - 04
		$update = $query = "CALL SetBillingJSON($billing_id, $serial, '$simponi_id', '$simponi_bill', '$date_simponi', '', '$receive')";
		$hasil = $this->main_act->get_result($query);
		if(!$hasil){
			$this->main_act->set_user_log($userid, $ip, "GetBilling", "Gagal, Fatal Error Proses Update Data Billing (04)", $update, $billing_id, $process_id);
			return "<response><result>ERROR</result><code>04</code><message>Fatal Error Proses Update Data Billing</message></response>";
		}
		$row = $query->row_array();
		if($row["result"]!="OK"){
			$this->main_act->set_user_log($userid, $ip, "GetBilling", "Gagal, Berhasil, Proses Update Data Billing $simponi_bill - $message (04)", $update, $billing_id, $process_id);
			return "<response><result>ERROR</result><code>04</code><message>Proses Update Data Billing Gagal ($message)</message></response>";
		}
		$this->main_act->set_user_log($userid, $ip, "GetBilling", "Berhasil, Proses Update Data Billing $simponi_bill (04)", $receive, $billing_id, $process_id);
		
		return "<response><result>OK</result><code>04</code><message>Proses Berhasil</message><simponi_id>$simponi_id</simponi_id><billing_id>$simponi_bill</billing_id><spb>$spb</spb><billing_server>$billing_server</billing_server></response>";
	}
	
	function update($userid, $password, $data, $param, $ip, $env) {
		if($ip=="") return "<response><result>ERROR</result><code>99</code><message>Alamat IP Tidak Valid</message></response>";
		else if($data=="") return "<response><result>ERROR</result><code>99</code><message>Parameter Tidak Valid</message></response>";
		$ip = $this->main_act->get_ip($ip);
		
		$query = "SET GLOBAL sql_mode = 'ALLOW_INVALID_DATES'";
		$this->db->simple_query($query);
		
		// Proses Billing - 01
		$update = $query = "CALL SetData('$data', '$userid', '$password', '$ip', $env)";
		$hasil = $this->main_act->get_result($query);
		if(!$hasil){
			$this->main_act->set_user_log($userid, $ip, "SetData", "Gagal, Fatal Error Proses Data Billing (01)", $update, $billing_id, $process_id);
			return "<response><result>ERROR</result><code>01</code><message>Fatal Error Proses Data Billing</message></response>";
		}
		$row = $query->row_array();
		if($row["result"]!="OK"){
			$this->main_act->set_user_log($userid, $ip, "SetData", "Gagal, Berhasil, Proses Data Billing $simponi_bill - $message (01)", $update, $billing_id, $process_id);
			return "<response><result>ERROR</result><code>01</code><message>Proses Data Billing Gagal ($message)</message></response>";
		}
		$this->main_act->set_user_log($userid, $ip, "SetData", "Berhasil, Proses Data Billing $simponi_bill (01)", $receive, $billing_id, $process_id);
		return "<response><result>OK</result><code>01</code><message>Proses Berhasil</message></response>";
	}
	
	function send($json, &$simponi_id, &$simponi_bill, &$date_simponi, &$error, &$receive, $env){
		$result = $this->main_act->send_json($json, $env);
		if($result=="Time Out"){
			$error = "Time Out";
			return false;
		}else{
			$json = json_decode($result);
		}
		$receive = $result;
		if($json->response->code == '00'){
			$simponi_id = $json->response->data[0];
			$simponi_bill = $json->response->data[1];
			$date_simponi = $json->response->data[2];
		}else{
			$error = '('.$json->response->code.') '.$json->response->message;
		}
		return true;
	}
}