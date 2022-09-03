<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Bank extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model("admin/bank_model");
        // $this->load->library('form_validation');
    }

	public function index()
    {
        $data["bank"] = $this->bank_model->getAll();
        $this->load->view("admin/bank/index", $data);
    }

    public function create()
    {
        $this->load->view("admin/bank/create");
    }

    public function store()
    {
        $bank = $this->bank_model;
        // $validation = $this->form_validation;
        // $validation->set_rules($bank->rules());

        if ($bank) {
            $bank->save();
            // $this->session->set_flashdata('success', 'Berhasil disimpan');
        }

        redirect(site_url('index.php/admin/Bank'));
    }

    public function edit($id = null)
    {       
        $bank = $this->bank_model;

        $data["bank"] = $bank->getById($id);
        if (!$data["bank"]) show_404();
        
        $this->load->view("admin/bank/edit", $data);
    }

    public function update()
    {       
        $bank = $this->bank_model;

        if ($bank) {
            $bank->update();
        }
        
        redirect(site_url('index.php/admin/Bank'));
    }

    public function delete($id=null)
    {
        if (!isset($id)) show_404();
            
        if ($this->bank_model->delete($id)) {
            redirect(site_url('index.php/admin/Bank'));
        }
    }
}