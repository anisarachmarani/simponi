<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Dashboard extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model("admin/bank_model");
        // $this->load->library('form_validation');
    }

	public function index()
    {
        $data["bank"] = $this->bank_model->getAll();
        $this->load->view("admin/index", $data);
    }
}