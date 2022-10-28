<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Billing extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model("billing_model");
        $this->load->model("department_model");
        $this->load->model("reff_model");
        $this->load->model("app_model");
        $this->load->model("user_model");
        $this->load->model("pnbp_model");
        // $this->load->library('form_validation');
        $this->load->model('auth_model');
		$this->auth_model->cek_login();
    }

	public function index()
    {
        $data["department"] = $this->department_model->getAll();
        $data["status"] = $this->reff_model->status_billing();
        $this->load->view("admin/billing/index", $data);
    }

    public function data_billing()
    {
        $department = $this->input->post('department'); // $_POST['department']
        $billing_id = $this->input->post('billing_id'); // $_POST['billing_id']
        $date_register = $this->input->post('date_register'); // $_POST['date_register']
        $date_simponi = $this->input->post('date_simponi'); // $_POST['date_simponi']
        $status = $this->input->post('status'); // $_POST['status']
        $data["billing"] = $this->billing_model->getAll($department, $billing_id, $date_register, $date_simponi, $status);
        echo $this->load->view("admin/billing/table", $data);
    }

    public function detail_billing()
    {
        $id = $this->input->post('id');
        
        $data["department"] = $this->department_model->getAll();
        $data["application"] = $this->app_model->getAll();
        $data["user"] = $this->user_model->getAll();
        $data["status"] = $this->reff_model->status_billing();
        $data["pnbp"] = $this->pnbp_model->getAll();

        $data["billing"] = $this->billing_model->getById($id);
        $data["billing_detail"] = $this->billing_model->detail_billing($id);
        
        echo $this->load->view("admin/billing/detail", $data);
    }
}