<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Departement extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model("admin/departement_model");
        // $this->load->library('form_validation');
    }

	public function index()
    {
        $data["departement"] = $this->departement_model->getAll();
        $this->load->view("admin/departement/index", $data);
    }
}