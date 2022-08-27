<?php
require_once 'libraries/Nusoap.php';

$wsdl = 'http://10.1.19.44/simponi/services?wsdl';
$ip = $_SERVER['REMOTE_ADDR'];
$user_id = 'eska';
$password = '7273838320ce61ad929d8ea5f7e6df55';

$transaction_id = '0963150141707173042';
$billing_id =  $_POST['billing'];
$client = new nusoap_client($wsdl);
$param = array('UserID' => $user_id, 
			   'Password' => $password, 
			   'Data' => $transaction_id, 
			   'Billing' => $billing_id, 
			   'Param' => "", 
			   'IP' => $ip, 
			   'Repost' => 0, 
			   'Format' => 'xml', );
$message = $client->call('GetPaymentTest', $param);
$xml = simplexml_load_string($message);
echo $json = json_encode($xml);
?>