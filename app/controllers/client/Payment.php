<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Payment extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model("payment_model");
        $this->load->model("reff_model");
        $this->load->model("app_model");
        $this->load->model("user_model");
        $this->load->model("bank_model");
        $this->load->model("department_model");
        // $this->load->library('form_validation');
        $this->load->model('auth_model');
		$this->auth_model->cek_login();
    }

	public function index()
    {
        $data["reff"] = $this->reff_model->getAll();
        $data["status"] = $this->reff_model->status_billing();
        $data["apps"] = $this->app_model->getAll();
        $data["user"] = $this->user_model->getAll();
        $this->load->view("client/payment/index", $data);
    }

    public function data_payment()
    {
        $billing_id = $this->input->post('billing_id'); // $_POST['billing_id']
        $application_id = $this->input->post('application_id'); // $_POST['application_id']
        $transaction_id = $this->input->post('transaction_id'); // $_POST['transaction_id']
        $user_id = $this->input->post('user_id'); // $_POST['user_id']
        $status = $this->input->post('status'); // $_POST['status']
        $data["payment"] = $this->payment_model->get_payment_client($billing_id, $application_id, $transaction_id, $user_id, $status);
        $data["reff"] = $this->reff_model->getAll();
        $data["status"] = $this->reff_model->status_billing();
        $data["apps"] = $this->app_model->getAll();
        $data["user"] = $this->user_model->getAll();
        $data["department"] = $this->department_model->getAll();
        $data["bank"] = $this->bank_model->getAll();
        echo $this->load->view("client/payment/table", $data);
    }
}