<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Application extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model("app_model");
        // $this->load->library('form_validation');
        $this->load->model('auth_model');
		$this->auth_model->cek_login();
    }

	public function index()
    {
        $data["app"] = $this->app_model->getAll();
        $this->load->view("admin/application/index", $data);
    }

    public function create()
    {
        $this->load->view("admin/application/create");
    }

    public function store()
    {
        $app = $this->app_model;
        // $validation = $this->form_validation;
        // $validation->set_rules($app->rules());

        if ($app) {
            $app->save();
            // $this->session->set_flashdata('success', 'Berhasil disimpan');
        }

        redirect(site_url('admin/Application'));
    }

    public function edit($id = null)
    {       
        $app = $this->app_model;

        $data["app"] = $app->getById($id);
        if (!$data["app"]) show_404();
        
        $this->load->view("admin/application/edit", $data);
    }

    public function update()
    {       
        $app = $this->app_model;

        if ($app) {
            $app->update();
        }
        
        redirect(site_url('admin/Application'));
    }

    public function delete($id=null)
    {
        if (!isset($id)) show_404();
            
        if ($this->app_model->delete($id)) {
            redirect(site_url('admin/Application'));
        }
    }
}