<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
error_reporting(E_ERROR);
class Crond_act extends CI_Model{
	var $user = "root";
	var $password = "Bismillah";
	
	function auth(){
		if($this->newsession->userdata('IN')!="LOGGED_IN"){
			if($_SERVER['PHP_AUTH_USER']==$this->user && $_SERVER['PHP_AUTH_PW']==$this->password){
				$sess['IN'] = "LOGGED_IN";
				$this->newsession->set_userdata($sess);
			}else{
				header('WWW-Authenticate: Basic realm="e-Payment SIMPONI - User Authentication"');
				header('HTTP/1.0 401 Unauthorized');
				header('status: 401 Unauthorized');
				header('Content-Type: text/html;');
				echo '<html><head><title>e-Payment SIMPONI</title></head><body><center><div style="font:15px verdana; font-weight:bold;"><span style="color:#F00;">Warning!</span> Authentication Required</div></center></body></html>';
				die();
			}
		}
	}
	
	function expired($cli){
		$ip = $this->main_act->get_ip();
		$query = "CALL SetExpired()"; 
		$process_id = $this->main_act->get_process_id();
		if(!$this->main_act->get_result($query)){
			$this->main_act->set_user_log("sys", $ip, "SetExpired", "Gagal, Fatal Error", "", "", $process_id);
			if(!$cli)
				echo "<html><head><title>e-Payment SIMPONI</title></head><body><!-- Fatal Error --></body></html>";
			else
				echo "Fatal Error".PHP_EOL;
			die();
		}
		$row = $query->row_array();
		$result = $row['result'];
		$message = $row['message'];
		$expired = $row['expired'];
		if($result=="OK") $message = "Berhasil, $message";
		$this->main_act->set_user_log("sys", $ip, "SetExpired", $message, $expired, "", $process_id);
		if(!$cli)
			echo "<html><head><title>e-Payment SIMPONI</title></head><body><!-- $message --></body></html>";
		else
			echo $message.PHP_EOL;
		die();
	}
	
	function simponi($cli, $mode){
		$ip = $this->main_act->get_ip();
		$process_id = $this->main_act->get_process_id();
		if($mode=="expired" || $mode=="paid")
			$query = "SELECT * FROM t_simponi_payment WHERE status IN ('SP05', 'SP06') LIMIT 5";
		else
			$query = "SELECT * FROM t_simponi_payment WHERE status = 'SP01' LIMIT 5";
		if(!$this->main_act->get_result($query)){
			// $this->main_act->set_user_log("sys", $ip, "SetSIMPONI", "Data Pembayaran Tidak Tersedia", "", "", $process_id);
			if(!$cli)
				echo "<html><head><title>e-Payment SIMPONI</title></head><body><!-- Data Not Available --></body></html>";
			else
				echo "Data Not Available".PHP_EOL;
			die();
		}
		$i = 0;
		$total = $query->num_rows();
		foreach($query->result_array() as $row){
			$billing_id = $row['billing_id'];
			$simponi_id = $row['simponi_id'];
			$ntb = $row['ntb'];
			$ntpn = $row['ntpn'];
			$date_paid = $row['date_paid'];
			$bank = $row['bank_code'];
			$channel = $row['channel_code'];
			if($mode=="expired"){
				$update = $command = "CALL SetRepostJSON('expired_id', 0, 0, '$simponi_id', '$ntb', '$ntpn', '$date_paid', '$bank', '$channel', '', '', '$billing_id')";
			}elseif($mode=="paid"){
				$update = $command = "CALL SetRepostJSON('payment_id', 0, 0, '$simponi_id', '$ntb', '$ntpn', '$date_paid', '$bank', '$channel', '', '', '$billing_id')";
			}else{
				$update = $command = "CALL SetPaymentJSON(0, 0, '$simponi_id', '$ntb', '$ntpn', '$date_paid', '$bank', '$channel', '', '', '$billing_id')";
			}
			if(!$this->main_act->get_result($command)){
				$this->main_act->set_user_log('sys', $ip, "SetSIMPONI", "Gagal, Fatal Error Proses Data Pembayaran $billing_id - $ntpn", $update, "", $process_id);
			}else{
				$rows = $command->row_array();
				if($rows["result"]=="ERROR"){
					$attach = $rows["result"];
					$message = $rows["message"];
					if($message!="") $message = " - $message";
					$this->main_act->set_user_log('sys', $ip, "SetSIMPONI", "Gagal, Proses Data Pembayaran $billing_id - $ntpn".$message, $attach." - ".$update, "", $process_id);
				}else{
					$payment_id = $rows['payment_id'];
					$spb_number = $rows['transaction_id'];
					$bank = $rows['bank'];
					$channel = $rows['channel'];
					$this->main_act->set_user_log('sys', $ip, "SetSIMPONI", "Berhasil, Proses Data Pembayaran $billing_id - $ntpn", "", $payment_id, $process_id);
					$update = $query = "exec dbo.PaySimponi @SPB_NUMBER = '$spb_number', @BILLING_ID = '$billing_id', @NTB = '$ntb', @NTPN = '$ntpn', @BANK = '$bank', @PAYMENT_TYPE = '$channel', @PAYMENT_TRANS = '$simponi_id', @PAID_DATE = '$date_paid'";
					$mssql = $this->load->database("epayment", TRUE);
					$hasil = $this->main_act->get_result($query, $mssql);
					if(!$hasil) $hasil = $this->main_act->get_result($query, $mssql);
					if(!$hasil) $hasil = $this->main_act->get_result($query, $mssql);
					if(!$hasil){
						$this->main_act->set_user_log('sys', $ip, "SetSIMPONI", "Gagal, Fatal Error Proses Update Billing Server $billing_id - $ntpn", $update, $payment_id, $process_id);
					}else{
						$row = $query->row_array();
						if($row['HASIL']!='UPDATED'){
							$error = $row['PESAN'];
							$this->main_act->set_user_log('sys', $ip, "SetSIMPONI", "Gagal, Proses Update Billing Server $billing_id - $ntpn", $update, $payment_id, $process_id);
						}else{
							$this->main_act->set_user_log('sys', $ip, "SetSIMPONI", "Berhasil, Proses Update Billing Server $billing_id - $ntpn", $update, $payment_id, $process_id);
						}
					}
					$i++;
				}
			}
		}
		if(!$cli)
			echo "<html><head><title>e-Payment SIMPONI</title></head><body><!-- $i of $total done --></body></html>";
		else
			echo "$i of $total done".PHP_EOL;
		die();
	}
	
	function old($cli){
		$ip = $this->main_act->get_ip();
		$query = "CALL MoveOld()"; 
		$process_id = $this->main_act->get_process_id();
		if(!$this->main_act->get_result($query)){
			$this->main_act->set_user_log("sys", $ip, "MoveOld", "Gagal, Fatal Error", "", "", $process_id);
			if(!$cli)
				echo "<html><head><title>e-Payment SIMPONI</title></head><body><!-- Fatal Error --></body></html>";
			else
				echo "Fatal Error".PHP_EOL;
			die();
		}
		$row = $query->row_array();
		$result = $row['result'];
		$message = $row['message'];
		$expired = $row['expired'];
		if($result=="OK") $message = "Berhasil, $message";
		$this->main_act->set_user_log("sys", $ip, "MoveOld", $message, $expired, "", $process_id);
		if(!$cli)
			echo "<html><head><title>e-Payment SIMPONI</title></head><body><!-- $message --></body></html>";
		else
			echo $message.PHP_EOL;
		die();
	}
}