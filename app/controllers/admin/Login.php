<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('admin/auth_model');
	}

	public function index()
	{
		$this->load->view('admin/login_form');
	}

	public function proses()
	{
		$name = $this->input->post('name');
		$password = $this->input->post('password');
		// var_dump($name);
		if($this->auth_model->login_user($name,$password))
		{
			redirect(site_url('index.php/admin/Dashboard'));
		}
		else
		{
			// var_dump($this);
			// die();
			$this->session->set_flashdata('error','Username & Password salah');
			redirect(site_url('index.php/admin/Login'));
		}
	}

	public function logout()
	{
		$this->session->sess_destroy('username');
		$this->session->sess_destroy('nama');
		$this->session->sess_destroy('is_login');
		redirect(site_url('index.php/admin/Login'));
	}

	

}