<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
error_reporting(E_ERROR);
class Bulk_act extends CI_Model{
	function get($userid, $password, $ip){
		if($ip=="") return "<response><result>ERROR</result><code>99</code><message>Alamat IP Tidak Valid</message></response>";
		$ip = $this->main_act->get_ip($ip);
		$process_id = $this->main_act->get_process_id();
		$query = "SET GLOBAL sql_mode = 'ALLOW_INVALID_DATES'";
		$this->db->simple_query($query);
		
		// Proses XML - 01
		$update = $query = "CALL GetPaymentBulk('$userid', '$password', '$ip')"; 
		$hasil = $this->main_act->get_result($query);
		if(!$hasil){
			$this->main_act->set_user_log($userid, $ip, "GetPaymentBulk", "Gagal, Fatal Error Proses Generate Data Pembayaran Baru (01)", $update, "", $process_id);
			return "<response><result>ERROR</result><code>01</code><message>Fatal Error Proses Generate Data Pembayaran Baru</message></response>";
		}
		$row = $query->row_array();
		if($row["result"]!="OK"){
			$this->main_act->set_user_log($userid, $ip, "GetPaymentBulk", "Tidak Ada Data Pembayaran Baru (01)", $update, "", $process_id);
			return "<response><result>".$row["result"]."</result><code>01</code><message>".$row["message"]."</message></response>";
		}
		$total = $query->num_rows();
		$xml = "<response><result>OK</result><code>01</code><message>";
		$xml .= "<items>$total</items>";
		$xml .= "<payment>";
		foreach($query->result_array() as $row){
			$simponi_id = $row['simponi_id'];
			$payment_id = $row['payment_id'];
			$billing_id = $row['billing_id'];
			$transaction_id = $row['transaction_id'];
			$ntb = $row['ntb'];
			$ntpn = $row['ntpn'];
			$date_paid = $row['date_paid'];
			$bank_code = $row['bank_code'];
			$bank = $row['bank'];
			$channel_code = $row['channel_code'];
			$channel = $row['channel'];
			$xml .= "<billing><billing_id>$billing_id</billing_id><transaction_id>$transaction_id</transaction_id><simponi_id>$simponi_id</simponi_id><ntb>$ntb</ntb><ntpn>$ntpn</ntpn><date_paid>$date_paid</date_paid><bank_code>$bank_code</bank_code><bank>$bank</bank><channel_code>$channel_code</channel_code><channel>$channel</channel></billing>";
		}
		$xml .= "</payment>";
		$xml .= "</message></response>";
		$this->main_act->set_user_log($userid, $ip, "GetPaymentBulk", "Berhasil, Download $total Data Pembayaran Baru (01)", $xml, "", $process_id);
		return $xml;
	}
	
	function set($userid, $password, $data, $ip){
		if($ip=="") return "<response><result>ERROR</result><code>99</code><message>Alamat IP Tidak Valid</message></response>";
		else if($data=="") return "<response><result>ERROR</result><code>99</code><message>Parameter Tidak Valid</message></response>";
		$ip = $this->main_act->get_ip($ip);
		$process_id = $this->main_act->get_process_id();
		
		$query = "SET GLOBAL sql_mode = 'ALLOW_INVALID_DATES'";
		$this->db->simple_query($query);
		
		// Proses XML - 01
		$update = $query = "CALL SetPayment('$data', '$userid', '$password', '$ip')"; 
		$hasil = $this->main_act->get_result($query);
		if(!$hasil){
			$this->main_act->set_user_log($userid, $ip, "SetPaymentBulk", "Gagal, Fatal Error Proses Update Data Pembayaran (01)", $update, "", $process_id);
			return "<response><result>ERROR</result><code>01</code><message>Fatal Error Proses Update Data Pembayaran</message></response>";
		}
		$row = $query->row_array();
		$message = $row["message"];
		if($row["result"]!="OK"){
			$this->main_act->set_user_log($userid, $ip, "SetPaymentBulk", "Gagal, Proses Update Data Pembayaran - $message (01)", $update, "", $process_id);
			return "<response><result>".$row["result"]."</result><code>01</code><message>$message</message></response>";
		}
		$this->main_act->set_user_log($userid, $ip, "SetPaymentBulk", "Berhasil, Proses Update Data Pembayaran (01)", $message, "", $process_id);
		$billing_id = $row["billing_id"];
		$payment_id = $row["payment_id"];
		$transaction_id = $row["transaction_id"];
		$date_paid = $row["date_paid"];
		$simponi_id = $row["simponi_id"];
		$channel = $row["channel"];
		$bank = $row["bank"];
		$ntb = $row["ntb"];
		$ntpn = $row["ntpn"];
		return "<response><result>OK</result><code>02</code><message>Proses Berhasil</message></response>";
	}
}