<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Department extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model("admin/department_model");
        // $this->load->library('form_validation');
        $this->load->model('admin/auth_model');
		$this->auth_model->cek_login();
    }

	public function index()
    {
        $data["departement"] = $this->department_model->getAll();
        $this->load->view("admin/department/index", $data);
    }

    public function create()
    {
        $this->load->view("admin/department/create");
    }

    public function store()
    {
        $department = $this->department_model;
        // $validation = $this->form_validation;
        // $validation->set_rules($department->rules());

        if ($department) {
            $department->save();
            // var_dump($department);
            // die();
            redirect(site_url('index.php/admin/Department'));
        } else {
            redirect(site_url('index.php/admin/Department/create'));
        }
    }

    public function edit($id = null)
    {       
        $department = $this->department_model;

        $data["department"] = $department->getById($id);
        if (!$data["department"]) show_404();
        
        $this->load->view("admin/department/edit", $data);
    }

    public function update()
    {       
        $department = $this->department_model;

        if ($department) {
            $department->update();
        }
        
        redirect(site_url('index.php/admin/Department'));
    }

    public function delete($id=null)
    {
        if (!isset($id)) show_404();
            
        if ($this->department_model->delete($id)) {
            redirect(site_url('index.php/admin/Department'));
        }
    }
}