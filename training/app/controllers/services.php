<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Services extends CI_Controller {
	function Services(){
		parent::__construct();
	}
	
	function index(){
		$this->load->library('nusoap');
		$server = $this->nusoap;
		$server->configureWSDL('e-Payment', 'urn:e-Payment');
		$server->register('GetBilling', array('UserID' => 'xsd:string', 'Password' => 'xsd:string', 'Data' => 'xsd:string', 'Param' => 'xsd:string', 'IP' => 'xsd:string', 'Format' => 'xsd:string'), array('return' => 'xsd:string'), 'urn:GetBilling', 'urn:GetBilling#', 'rpc', 'encoded', 'Request Billing ID');
		$server->register('GetBillingTest', array('UserID' => 'xsd:string', 'Password' => 'xsd:string', 'Data' => 'xsd:string', 'Param' => 'xsd:string', 'IP' => 'xsd:string', 'Format' => 'xsd:string'), array('return' => 'xsd:string'), 'urn:GetBillingTest', 'urn:GetBillingTest#', 'rpc', 'encoded', 'Request Billing ID - Test');
		$server->register('GetPayment', array('UserID' => 'xsd:string', 'Password' => 'xsd:string', 'Data' => 'xsd:string', 'Billing' => 'xsd:string', 'Param' => 'xsd:string', 'IP' => 'xsd:string', 'Repost' => 'xsd:byte', 'Format' => 'xsd:string'), array('return' => 'xsd:string'), 'urn:GetPayment', 'urn:GetPayment#', 'rpc', 'encoded', 'Request Data Pembayaran'); 
		$server->register('GetPaymentTest', array('UserID' => 'xsd:string', 'Password' => 'xsd:string', 'Data' => 'xsd:string', 'Billing' => 'xsd:string', 'Param' => 'xsd:string', 'IP' => 'xsd:string', 'Repost' => 'xsd:byte', 'Format' => 'xsd:string'), array('return' => 'xsd:string'), 'urn:GetPaymentTest', 'urn:GetPaymentTest#', 'rpc', 'encoded', 'Request Data Pembayaran - Test');
		$server->register('GetPaymentBulk', array('UserID' => 'xsd:string', 'Password' => 'xsd:string', 'IP' => 'xsd:string', 'Format' => 'xsd:string'), array('return' => 'xsd:string'), 'urn:GetPaymentBulk', 'urn:GetPaymentBulk#', 'rpc', 'encoded', 'Request Semua Data Pembayaran Baru');
		$server->register('SetPaymentBulk', array('UserID' => 'xsd:string', 'Password' => 'xsd:string', 'Data' => 'xsd:string', 'IP' => 'xsd:string', 'Format' => 'xsd:string'), array('return' => 'xsd:string'), 'urn:SetPaymentBulk', 'urn:SetPaymentBulk#', 'rpc', 'encoded', 'Update Data Pembayaran yang Sudah Diproses');
		$server->register('SetData', array('UserID' => 'xsd:string', 'Password' => 'xsd:string', 'Data' => 'xsd:string', 'Param' => 'xsd:string', 'IP' => 'xsd:string', 'Format' => 'xsd:string'), array('return' => 'xsd:string'), 'urn:SetData', 'urn:SetData#', 'rpc', 'encoded', 'Update Data Billing Yang Tidak Berkaitan Dengan SIMPONI');
		
		function GetBilling($user, $pass, $data, $param, $ip, $format) {
			global $objci;
			$objci->load->model("services/billing_act");
			if($format=="json") $data = $objci->main_act->json_xml($data, '<billing/>');
			$hasil = $objci->billing_act->get($user, $pass, $data, $param, $ip, 1, $format);
			if($format=="json") $hasil = $objci->main_act->xml_json($hasil);
			return $hasil;
		}
		
		function GetBillingTest($user, $pass, $data, $param, $ip, $format) {
			global $objci;
			$objci->load->model("services/billing_act");
			if($format=="json") $data = $objci->main_act->json_xml($data, '<billing/>');
			$hasil = $objci->billing_act->get($user, $pass, $data, $param, $ip, 0, $format);
			if($format=="json") $hasil = $objci->main_act->xml_json($hasil);
			return $hasil;
		}
		
		function GetPayment($user, $pass, $data, $billing, $param, $ip, $repost, $format) {
			global $objci;
			$objci->load->model("services/payment_act");
			$repost = (int)$repost;
			if($repost==0){
				$hasil = $objci->payment_act->get($user, $pass, $data, $billing, $param, $ip, 1, $format);
			}else{
				$hasil = $objci->payment_act->repost($user, $pass, $data, $billing, $param, $ip, 1, $format);
			}
			if($format=="json") $hasil = $objci->main_act->xml_json($hasil);
			return $hasil;
		}
		
		function GetPaymentTest($user, $pass, $data, $billing, $param, $ip, $repost, $format) {
			global $objci;
			$objci->load->model("services/payment_act");
			$repost = (int)$repost;
			if($repost==0){
				$hasil = $objci->payment_act->get($user, $pass, $data, $billing, $param, $ip, 0, $format);
			}else{
				$hasil = $objci->payment_act->repost($user, $pass, $data, $billing, $param, $ip, 0, $format);
			}
			if($format=="json") $hasil = $objci->main_act->xml_json($hasil);
			return $hasil;
		}
		
		function GetPaymentBulk($user, $pass, $ip, $format) {
			global $objci;
			$objci->load->model("services/bulk_act");
			$hasil = $objci->bulk_act->get($user, $pass, $ip, $format);
			if($format=="json") $hasil = $objci->main_act->xml_json($hasil);
			return $hasil;
		}
		
		function SetPaymentBulk($user, $pass, $data, $ip, $format) {
			global $objci;
			$objci->load->model("services/bulk_act");
			if($format=="json") $data = $objci->main_act->json_xml($data, '<billing/>');
			$hasil = $objci->bulk_act->set($user, $pass, $data, $ip, $format);
			if($format=="json") $hasil = $objci->main_act->xml_json($hasil);
			return $hasil;
		}
		
		function SetData($user, $pass, $data, $param, $ip, $format) {
			global $objci;
			$objci->load->model("services/billing_act");
			if($format=="json") $data = $objci->main_act->json_xml($data, '<billing/>');
			$hasil = $objci->billing_act->update($user, $pass, $data, $param, $ip, 1, $format);
			if($format=="json") $hasil = $objci->main_act->xml_json($hasil);
			return $hasil;
		}
		
		ob_clean();
		$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : file_get_contents('php://input');
		$server->service($HTTP_RAW_POST_DATA, $this);
	}
	
	function json($dev=""){
		if($_SERVER['REQUEST_METHOD']=="POST"){
			$this->load->model("services/post_act");
			$message = $this->post_act->set(file_get_contents('php://input'), $dev);
		}else{
			$message = array("response" => array("result" => "ERROR", "message" => "Bad Request"));
		}
		header('Content-Type: application/json');
		echo json_encode($message);
	}
	
	function expired(){
		$cli = true;
		$this->load->model("services/crond_act");
		if(php_sapi_name()!="cli"){
			$cli = false;
			$this->crond_act->auth();
		}
		$this->crond_act->expired($cli);
	}
	
	function simponi($mode=""){
		if($mode=="") $mode = "new";
		$cli = true;
		$this->load->model("services/crond_act");
		if(php_sapi_name()!="cli"){
			$cli = false;
			$this->crond_act->auth();
		}
		$this->crond_act->simponi($cli, $mode);
	}
	
	function old(){
		$cli = true;
		$this->load->model("services/crond_act");
		if(php_sapi_name()!="cli"){
			$cli = false;
			$this->crond_act->auth();
		}
		$this->crond_act->old($cli);
	}
	
	function json2xml(){
		header("Content-type: text/xml");
		$json = '{"transaction_id":"$transaction_id","date_register":"2017-10-10 11:44:50","date_expired":"2017-10-24 23:59:59","department_id":"041","department_code":"1","total":"1100000","npwp":"201019091986100","code_1":"Variasi","code_2":"Suplemen Makanan","code_3":"","detail":"411A PT TESTING INI - ASROT","items":"2","pnbp":[{"serial":"1","trader":"PT TESTING INI","pnbp_id":"1056","pnbp_code":"2","volume":"1","total":"100000","detail":"1d2","code_1":"","code_2":"","code_3":""},{"serial":"2","trader":"PT TESTING INI","pnbp_id":"1057","pnbp_code":"2","volume":"1","total":"1000000","detail":"1d2","code_1":"","code_2":"","code_3":""}]}';
		$json = '{"transaction_id":"$transaction_id","date_register":"2017-10-10 11:44:50","date_expired":"2017-10-24 23:59:59","department_id":"041","department_code":"1","total":"1100000","npwp":"201019091986100","code_1":"Variasi","code_2":"Suplemen Makanan","code_3":{},"detail":"411A PT TESTING INI - ASROT","items":"2","pnbp":[{"serial":"1","trader":"PT TESTING INI","pnbp_id":"1056","pnbp_code":"2","volume":"1","total":"100000","detail":"1d2","code_1":{},"code_2":{},"code_3":{}},{"serial":"2","trader":"PT TESTING INI","pnbp_id":"1057","pnbp_code":"2","volume":"1","total":"1000000","detail":"1d2","code_1":{},"code_2":{},"code_3":{}}]}';
		echo $this->main_act->json_xml($json, '<billing/>');
	}
	
	function xml2json(){
		$xml = '<?xml version="1.0"?>
<billing><transaction_id>$transaction_id</transaction_id><date_register>2017-10-10 11:44:50</date_register><date_expired>2017-10-24 23:59:59</date_expired><department_id>041</department_id><department_code>1</department_code><total>1100000</total><npwp>201019091986100</npwp><code_1>Variasi</code_1><code_2>Suplemen Makanan</code_2><code_3></code_3><detail>411A PT TESTING INI - ASROT</detail><items>2</items><pnbp><item><serial>1</serial><trader>PT TESTING INI</trader><pnbp_id>1056</pnbp_id><pnbp_code>2</pnbp_code><volume>1</volume><total>100000</total><detail>1d2</detail><code_1></code_1><code_2></code_2><code_3></code_3></item><item><serial>2</serial><trader>PT TESTING INI</trader><pnbp_id>1057</pnbp_id><pnbp_code>2</pnbp_code><volume>1</volume><total>1000000</total><detail>1d2</detail><code_1></code_1><code_2></code_2><code_3></code_3></item></pnbp></billing>';
		header('Content-Type: application/json');
		echo $this->main_act->xml_json($xml);
	}
}