<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Bank extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model("bank_model");
        // $this->load->library('form_validation');
        $this->load->model('auth_model');
		$this->auth_model->cek_login();
    }

	public function index()
    {
        $data["bank"] = $this->bank_model->getAll();
        $this->load->view("client/bank/index", $data);
    }
}