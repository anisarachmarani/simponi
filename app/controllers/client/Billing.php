<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Billing extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model("billing_model");
        $this->load->model("reff_model");
        // $this->load->library('form_validation');
        $this->load->model('auth_model');
		$this->auth_model->cek_login();
    }

	public function index()
    {
        $data["status"] = $this->reff_model->status_billing();
        $this->load->view("client/billing/index", $data);
    }

    public function data_billing()
    {
        $billing_id = $this->input->post('billing_id'); // $_POST['billing_id']
        $date_register = $this->input->post('date_register'); // $_POST['date_register']
        $date_simponi = $this->input->post('date_simponi'); // $_POST['date_simponi']
        $status = $this->input->post('status'); // $_POST['status']
        $data["billing"] = $this->billing_model->get_billing_client($billing_id, $date_register, $date_simponi, $status);
        echo $this->load->view("admin/billing/table", $data);
    }
}