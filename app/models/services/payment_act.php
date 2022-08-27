<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
error_reporting(E_ERROR);
class Payment_act extends CI_Model{
	function get($userid, $password, $data, $billing, $param, $ip, $env){
		if($ip=="") return "<response><result>ERROR</result><code>99</code><message>Alamat IP Tidak Valid</message></response>";
		else if($data=="" || $billing=="") return "<response><result>ERROR</result><code>99</code><message>Parameter Tidak Valid</message></response>";
		$ip = $this->main_act->get_ip($ip);
		$process_id = $this->main_act->get_process_id();
		$query = "SET GLOBAL sql_mode = 'ALLOW_INVALID_DATES'";
		$this->db->simple_query($query);
		
		// Proses JSON - 01
		$update = $query = "CALL GetPaymentJSON('$data', '$billing', '$userid', '$password', '$ip', $env)"; 
		$hasil = $this->main_act->get_result($query);
		if(!$hasil){
			$this->main_act->set_user_log($userid, $ip, "GetPayment", "Gagal, Fatal Error Proses Generate JSON (01)", $update, "", $process_id);
			return "<response><result>ERROR</result><code>01</code><message>Fatal Error Proses Generate JSON</message></response>";
		}
		$row = $query->row_array();
		if($row["result"]=="ERROR"){
			$message = $row["message"];
			$expired_id = $row["expired_id"];
			$this->main_act->set_user_log($userid, $ip, "GetPayment", "Gagal, Proses Generate JSON - $message (01)", $update, $expired_id, $process_id);
			return "<response><result>".$row["result"]."</result><code>01</code><message>Proses Generate JSON Gagal ($message)</message></response>";
		}else if($row["result"]=="OK" && $row["json"]!=""){
			$billing_id = $row['billing_id'];
			$serial = $row['serial'];
			$json = $row['json'];
			$json = array('simponi_req' => $json);
			$this->main_act->set_user_log($userid, $ip, "GetPayment", "Berhasil, Proses Generate JSON (01)", $update, $billing_id, $process_id);
			
			// Proses SIMPONI - 02
			$hasil = $this->send($json, $simponi_id, $ntb, $ntpn, $bank, $channel, $date_paid, $error, $receive, $env);
			if(!$hasil){
				$query = "CALL SetPaymentJSON($billing_id, $serial, '', '', '', '', '', '', '$error', '', '')";
				$hasil = $this->main_act->get_result($query);
				$this->main_act->set_user_log($userid, $ip, "GetPayment", "Gagal, Fatal Error Proses Request Data Pembayaran - $error (02)", join($json, ""), $billing_id, $process_id);
				return "<response><result>ERROR</result><code>02</code><message>Fatal Error Proses Request Data Pembayaran</message></response>";
			}
			if($error!=""){
				$query = "CALL SetPaymentJSON($billing_id, $serial, '', '', '', '', '', '', '$error', '$receive', '')";
				$hasil = $this->main_act->get_result($query);
				$this->main_act->set_user_log($userid, $ip, "GetPayment", "Gagal, Proses Request Data Pembayaran - $error (02)", join($json, ""), $billing_id, $process_id);
				return "<response><result>ERROR</result><code>02</code><message>Proses Request Data Pembayaran Gagal ($error)</message></response>";
			}
			$this->main_act->set_user_log($userid, $ip, "GetPayment", "Berhasil, Proses Request Data Pembayaran (02)", join($json, ""), $billing_id, $process_id);
			
			// Proses Update Data Pembayaran - 03
			$update = $query = "CALL SetPaymentJSON($billing_id, $serial, '$simponi_id', '$ntb', '$ntpn', '$date_paid', '$bank', '$channel', '', '$receive', '')";
			$hasil = $this->main_act->get_result($query);
			if(!$hasil){
				$this->main_act->set_user_log($userid, $ip, "GetPayment", "Gagal, Fatal Error Proses Update Data Pembayaran (03)", $update, $billing_id, $process_id);
				return "<response><result>ERROR</result><code>03</code><message>Fatal Error Proses Update Data Pembayaran</message></response>";
			}
			$row = $query->row_array();
			if($row["result"]=="ERROR"){
				$attach = $row["result"];
				$message = $row["message"];
				$this->main_act->set_user_log($userid, $ip, "GetPayment", "Gagal, Proses Update Data Pembayaran - $message (03)", $attach." - ".$update, $billing_id, $process_id);
				return "<response><result>ERROR</result><code>03</code><message>Proses Update Data Pembayaran Gagal ($message)</message></response>";
			}
			$paid = 0;
		}else{
			$paid = 1;
		}
		$simponi_id = $row['simponi_id'];
		$payment_id = $row['payment_id'];
		$serial = $row['serial'];
		$ntb = $row['ntb'];
		$ntpn = $row['ntpn'];
		$date_paid = $row['date_paid'];
		$bank_code = $row['bank_code'];
		$bank = $row['bank'];
		$channel_code = $row['channel_code'];
		$channel = $row['channel'];
		if($paid==1){
			$this->main_act->set_user_log($userid, $ip, "GetPayment", "Berhasil, Proses Generate JSON - Sudah Terbayar (01)", "Log ID: $serial - Billing ID: $billing - NTPN: $ntpn", $payment_id, $process_id);
			$code = "01";
		}else{
			$this->main_act->set_user_log($userid, $ip, "GetPayment", "Berhasil, Proses Update Data Pembayaran $billing - $ntpn (03)", $receive, $payment_id, $process_id);
			$code = "03";
		}
		
		return "<response><result>OK</result><code>03</code><message>Proses Berhasil</message><simponi_id>$simponi_id</simponi_id><ntb>$ntb</ntb><ntpn>$ntpn</ntpn><date_paid>$date_paid</date_paid><bank_code>$bank_code</bank_code><bank>$bank</bank><channel_code>$channel_code</channel_code><channel>$channel</channel></response>";
	}
	
	function repost($userid, $password, $data, $billing, $param, $ip, $env){
		if($ip=="") return "<response><result>ERROR</result><code>99</code><message>Alamat IP Tidak Valid</message></response>";
		else if($data=="" || $billing=="") return "<response><result>ERROR</result><code>99</code><message>Parameter Tidak Valid</message></response>";
		$ip = $this->main_act->get_ip($ip);
		$process_id = $this->main_act->get_process_id();
		
		$query = "SET GLOBAL sql_mode = 'ALLOW_INVALID_DATES'";
		$this->db->simple_query($query);
		
		// Proses JSON - 01
		$update = $query = "CALL GetRepostJSON('$data', '$billing', '$userid', '$password', '$ip', $env)"; 
		$hasil = $this->main_act->get_result($query);
		if(!$hasil){
			$this->main_act->set_user_log($userid, $ip, "GetPayment (Repost)", "Gagal, Fatal Error Proses Generate JSON (01)", $update, "", $process_id);
			return "<response><result>ERROR</result><code>01</code><message>Fatal Error Proses Generate JSON</message></response>";
		}
		$row = $query->row_array();
		if($row["result"]=="ERROR"){
			$message = $row["message"];
			$this->main_act->set_user_log($userid, $ip, "GetPayment (Repost)", "Gagal, Proses Generate JSON - $message (01)", $update, "", $process_id);
			return "<response><result>".$row["result"]."</result><code>01</code><message>Proses Generate JSON Gagal ($message)</message></response>";
		}
		$payment_id = $row['payment_id'];
		$expired_id = $row['expired_id'];
		if($expired_id!=""){
			$type_id = "'expired_id'";
			$billing_id = $expired_id;
		}else{
			$type_id = "'payment_id'";
			$billing_id = $payment_id;
		}
		$serial = $row['serial'];
		$json = $row['json'];
		$json = array('simponi_req' => $json);
		$this->main_act->set_user_log($userid, $ip, "GetPayment (Repost)", "Berhasil, Proses Generate JSON (01)", $update, $billing_id, $process_id);
		
		// Proses SIMPONI - 02
		$hasil = $this->send($json, $simponi_id, $ntb, $ntpn, $bank, $channel, $date_paid, $error, $receive, $env);
		if($hasil==false){
			$query = "CALL SetRepostJSON($type_id, $billing_id, $serial, '', '', '', '', '', '', '$error', '', '')";
			$hasil = $this->main_act->get_result($query);
			$this->main_act->set_user_log($userid, $ip, "GetPayment (Repost)", "Gagal, Fatal Error Proses Request Data Pembayaran - $error (02)", join($json, ""), $billing_id, $process_id);
			return "<response><result>ERROR</result><code>02</code><message>Fatal Error Proses Request Data Pembayaran</message></response>";
		}
		if($error!=""){
			$query = "CALL SetRepostJSON($type_id, $billing_id, $serial, '', '', '', '', '', '', '$error', '$receive', '')";
			$hasil = $this->main_act->get_result($query);
			$this->main_act->set_user_log($userid, $ip, "GetPayment (Repost)", "Gagal, Proses Request Data Pembayaran - $error (02)", join($json, ""), $billing_id, $process_id);
			return "<response><result>ERROR</result><code>02</code><message>Proses Request Data Pembayaran Gagal ($error)</message></response>";
		}
		$this->main_act->set_user_log($userid, $ip, "GetPayment (Repost)", "Berhasil, Proses Request Data Pembayaran (02)", join($json, ""), $billing_id, $process_id);
		
		// Proses Update Data Pembayaran - 03
		$update = $query = "CALL SetRepostJSON($type_id, $billing_id, $serial, '$simponi_id', '$ntb', '$ntpn', '$date_paid', '$bank', '$channel', '', '$receive', '')";
		$hasil = $this->main_act->get_result($query);
		if(!$hasil){
			$this->main_act->set_user_log($userid, $ip, "GetPayment (Repost)", "Gagal, Fatal Error Proses Update Data Pembayaran (03)", $update, $billing_id, $process_id);
			return "<response><result>ERROR</result><code>03</code><message>Fatal Error Proses Update Data Pembayaran</message></response>";
		}
		$row = $query->row_array();
		if($row["result"]=="ERROR"){
			$attach = $row["result"];
			$message = $row["message"];
			$this->main_act->set_user_log($userid, $ip, "GetPayment (Repost)", "Gagal, Proses Update Data Pembayaran - $message (03)", $attach." - ".$update, $billing_id, $process_id);
			return "<response><result>ERROR</result><code>03</code><message>Proses Update Data Pembayaran Gagal ($message)</message></response>";
		}
		$simponi_id = $row['simponi_id'];
		$payment_id = $row['payment_id'];
		$serial = $row['serial'];
		$ntb = $row['ntb'];
		$ntpn = $row['ntpn'];
		$date_paid = $row['date_paid'];
		$bank_code = $row['bank_code'];
		$bank = $row['bank'];
		$channel_code = $row['channel_code'];
		$channel = $row['channel'];
		$this->main_act->set_user_log($userid, $ip, "GetPayment (Repost)", "Berhasil, Proses Update Data Pembayaran $billing - $ntpn (03)", $receive, $payment_id, $process_id);
		
		return "<response><result>OK</result><code>03</code><message>Proses Berhasil</message><simponi_id>$simponi_id</simponi_id><ntb>$ntb</ntb><ntpn>$ntpn</ntpn><date_paid>$date_paid</date_paid><bank_code>$bank_code</bank_code><bank>$bank</bank><channel_code>$channel_code</channel_code><channel>$channel</channel></response>";
	}
	
	function send($json, &$simponi_id, &$ntb, &$ntpn, &$bank, &$channel, &$date_paid, &$error, &$receive, $env){
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
			$ntb = $json->response->data[1];
			$ntpn = $json->response->data[2];
			$date_paid = $json->response->data[3];
			$bank = $json->response->data[4];
			$channel = $json->response->data[5];
		}else{
			$error = '('.$json->response->code.') '.$json->response->message;
		}
		return true;
	}
}