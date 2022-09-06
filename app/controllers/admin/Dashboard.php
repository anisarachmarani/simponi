<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Dashboard extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model("admin/bank_model");
        $this->load->model("admin/billing_model");
        $this->load->model("admin/department_model");
        // $this->load->library('form_validation');
        $this->load->model('admin/auth_model');
		$this->auth_model->cek_login();
    }

	public function index()
    {
        $data["bank"] = $this->bank_model->getFive();
        $data["billing"] = $this->billing_model->getFive();
        $data["department"] = $this->department_model->getFive();
        $this->load->view("admin/index", $data);
    }
}