<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Billing extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model("admin/billing_model");
        // $this->load->library('form_validation');
    }

	public function index()
    {
        $data["billing"] = $this->billing_model->getAll();
        $this->load->view("admin/billing/index", $data);
    }
}