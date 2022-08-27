<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Error extends CI_Controller {
	function Error(){
		parent::__construct();
	}
	
	function index(){
		
	}
	
	function p404(){
		echo "<html><head><title>e-Payment SIMPONI</title></head><body><h2>Error 404</h2></body></html>";
	}
}