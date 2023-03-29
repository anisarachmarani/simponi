<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Dashboard extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model("bank_model");
        $this->load->model("billing_model");
        $this->load->model("department_model");
        // $this->load->library('form_validation');
        $this->load->model("payment_model");
        $this->load->model('auth_model');
		$this->auth_model->cek_login();
    }

	public function index()
    {
        $data["bank"] = $this->bank_model->getFive();
        $data["billing"] = $this->billing_model->getFive();
        $data["department"] = $this->department_model->getFive();
        $data["grafik_payment"] = $this->payment_model->graphPayment();
        $data["grafik_payment_month"] = $this->payment_model->graphPaymentMonth();
        $data["bank_stats"] = $this->payment_model->bank_stats();
        $this->load->view("admin/index", $data);
    }
}