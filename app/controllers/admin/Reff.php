<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Reff extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model("admin/reff_model");
        // $this->load->library('form_validation');
        $this->load->model('admin/auth_model');
		$this->auth_model->cek_login();
    }

	public function index()
    {
        $data["reff"] = $this->reff_model->getAll();
        $this->load->view("admin/reff/index", $data);
    }

    public function create()
    {
        $this->load->view("admin/reff/create");
    }

    public function store()
    {
        $reff = $this->reff_model;
        // $validation = $this->form_validation;
        // $validation->set_rules($reff->rules());

        if ($reff) {
            $reff->save();
            // var_dump($reff);
            // die();
            redirect(site_url('index.php/admin/Reff'));
        } else {
            redirect(site_url('index.php/admin/Reff/create'));
        }
    }

    public function edit($id = null)
    {       
        $reff = $this->reff_model;

        $data["reff"] = $reff->getById($id);
        if (!$data["reff"]) show_404();
        
        $this->load->view("admin/reff/edit", $data);
    }

    public function update()
    {       
        $reff = $this->reff_model;

        if ($reff) {
            $reff->update();
        }
        
        redirect(site_url('index.php/admin/Reff'));
    }

    public function delete($id=null)
    {
        if (!isset($id)) show_404();
            
        if ($this->reff_model->delete($id)) {
            redirect(site_url('index.php/admin/Reff'));
        }
    }
}