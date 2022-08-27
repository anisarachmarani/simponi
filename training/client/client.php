<?php
require_once 'libraries/Nusoap.php';
$wsdl = 'http://10.1.19.44/simponi/services?wsdl';
$ip = $_SERVER['REMOTE_ADDR'];
$user_id = 'eska';
$password = '7273838320ce61ad929d8ea5f7e6df55';

/*$client = new nusoap_client($wsdl);
$param = array('UserID' => $user_id, 
			   'Password' => $password, 
			   'Data' => $_GET['data'], 
			   'Billing' => $_POST['billing'], 
			   'Param' => "", 
			   'IP' => $ip, 
			   'Repost' => 0, 
			   'Format' => 'json', );
$message = $client->call('GetPaymentTest', $param);

$client = new nusoap_client($wsdl);
$param = array('UserID' => $user_id, 
			   'Password' => $password, 
			   'Data' => $_GET['data'], 
			   'IP' => $ip, 
			   'Format' => 'json', );
$message = $client->call('SetPaymentBulk', $param);*/

$param = '';
$transaction_id = '0963150141707173041';
$date_register = date('Y-m-d H:i:s');
$date_expired = date('Y-m-25 H:i:s');
$npwp = '100001000010000';
$total = 50000;
$total = 100000;
$code_1 = 'PT Kota Jakarta Utara';
$code_11 = 'Form A';
$code_12 = 'Form B';
$detail = 'TEST - PT EDI Indonesia';
$trader = 'PT EDI Indonesia';
$data = "
<billing>
	<transaction_id>$transaction_id</transaction_id>
	<date_register>$date_register</date_register>
	<date_expired>$date_expired</date_expired>
	<department_id>X</department_id>
	<department_code>1</department_code>
	<total>$total</total>
	<npwp>$npwp</npwp>
	<code_1>$code_1</code_1>
	<code_2></code_2>
	<code_3></code_3>
	<detail>$detail</detail>
	<items>1</items>
	<pnbp>
		<item>
			<serial>1</serial>
			<trader>$trader</trader>
			<pnbp_id>X</pnbp_id>
			<pnbp_code>1</pnbp_code>
			<volume>1</volume>
			<total>$total</total>
			<detail>$code_11</detail>
			<code_1>$code_11</code_1>
			<code_2></code_2>
			<code_3></code_3>
		</item>
	</pnbp>
</billing>";
$client = new nusoap_client($wsdl);
$param = array('UserID' => $user_id, 
			   'Password' => $password, 
			   'Data' => $data, 
			   'Param' => $param, 
			   'IP' => $ip, );
$message = $client->call('GetBillingTest', $param);
$xml = simplexml_load_string($message);
echo $json = json_encode($xml);

?>