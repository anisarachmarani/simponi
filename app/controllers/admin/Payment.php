<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Payment extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model("admin/payment_model");
        // $this->load->library('form_validation');
        $this->load->model('admin/auth_model');
		$this->auth_model->cek_login();
    }

	public function index()
    {
        $data["payment"] = $this->payment_model->getAll();
        $this->load->view("admin/payment/index", $data);
    }
}