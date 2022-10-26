<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class User extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model("admin/user_model");
        $this->load->model("admin/reff_model");
        $this->load->model("admin/app_model");
        $this->load->model("admin/department_model");
        // $this->load->library('form_validation');
        $this->load->model('admin/auth_model');
		$this->auth_model->cek_login();
    }

	public function index()
    {
        $data["user"] = $this->user_model->getAll();
        $data["reff"] = $this->reff_model->getAll();
        $data["apps"] = $this->app_model->getAll();
        $data["departments"] = $this->department_model->getAll();
        $this->load->view("admin/user/index", $data);
    }

    public function create()
    {
        $data["roles"] = $this->reff_model->role();
        $data["department"] = $this->department_model->getAll();
        $data["app"] = $this->app_model->getAll();
        $this->load->view("admin/user/create", $data);
    }

    public function store()
    {
        $user = $this->user_model;
        // $validation = $this->form_validation;
        // $validation->set_rules($user->rules());

        if ($user) {
            $user->save();
            // $this->session->set_flashdata('success', 'Berhasil disimpan');
        }

        redirect(site_url('admin/User'));
    }

    public function edit($id=null)
    {  
        $user = $this->user_model;
        
        $data["roles"] = $this->reff_model->role();
        $data["department"] = $this->department_model->getAll();
        $data["app"] = $this->app_model->getAll();
        $data["user"] = $user->getById($id);
        if (!$data["user"]) show_404();
        $this->load->view("admin/user/edit", $data);
    }

    public function update()
    {       
        $user = $this->user_model;

        if ($user) {
            $user->update();
        }
        
        redirect(site_url('admin/User'));
    }

    public function delete($id=null)
    {
        if (!isset($id)) show_404();
            
        if ($this->user_model->delete($id)) {
            redirect(site_url('admin/User'));
        }
    }
}