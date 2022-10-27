<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Payment extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model("payment_model");
        $this->load->model("reff_model");
        // $this->load->library('form_validation');
        $this->load->model('auth_model');
		$this->auth_model->cek_login();
    }

	public function index()
    {
        $data["payment"] = $this->payment_model->getAll();
        $data["reff"] = $this->reff_model->getAll();
        $this->load->view("client/payment/index", $data);
    }
}