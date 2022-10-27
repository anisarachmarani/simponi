<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('auth_model');
	}

	public function index()
	{
		$this->load->view('login_form');
	}

	public function proses()
	{
		$name = $this->input->post('name');
		$password = $this->input->post('password');
		$query = $this->db->get_where('t_user',array('login'=>$name))->row();
		if($this->auth_model->login_user($name,$password) && $query->status == 'US01')
		{
			if ($query->role == "RL01") {
				redirect(site_url('admin/Dashboard'));
			} else if ($query->role == "RL03") {
				redirect(site_url('client/Dashboard'));
			}
			
		}
		else
		{
			// var_dump($this);
			// die();
			$this->session->set_flashdata('error','Username & Password salah / Status akun tidak aktif');
			redirect(site_url('Login'));
		}
	}

	public function logout()
	{
		$this->session->sess_destroy('username');
		$this->session->sess_destroy('nama');
		$this->session->sess_destroy('is_login');
		redirect(site_url('Login'));
	}

	

}