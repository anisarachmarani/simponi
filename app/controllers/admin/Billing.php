<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Billing extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("billing_model");
        $this->load->model("department_model");
        $this->load->model("reff_model");
        $this->load->model("app_model");
        $this->load->model("user_model");
        $this->load->model("pnbp_model");
        // $this->load->library('form_validation');
        $this->load->model('auth_model');
        $this->auth_model->cek_login();
    }

    public function index()
    {
        $data["department"] = $this->department_model->getAll();
        $data["status"] = $this->reff_model->status_billing();
        $this->load->view("admin/billing/index", $data);
    }

    public function get_datatables_query()
    {
        $this->billing_model->_get_datatables_query();
    }

    public function ajax_list()
    {
        $list = $this->billing_model->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {

            if ($item->error !== null) {
                $error = $item->error;
            } else {
                $error = '-';
            }
            
            if ($item->error_pay !== null) {
                $error_pay = $item->error_pay;
            } else {
                $error_pay = '-';
            }
            

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<a href="javascript:void(0)" onclick="return theFunction('.$item->id.');" data-bs-toggle="modal" class="view_data" data-bs-target=".bs-example-modal-lg" >'.$item->billing_id.'</a>';
            $row[] = date('d M Y, H:i', strtotime($item->date_register));
            $row[] = date('d M Y, H:i', strtotime($item->date_simponi));
            $row[] = '<span>'.$item->department_name.'</span>';
            $row[] = number_format($item->total);
            $row[] = '<span>'.$item->status_name.'</span>';
            $row[] = '<span>'.$error.'</span>';
            $row[] = '<span>'.$error_pay.'</span>';
            $row[] = date('d M Y, H:i', strtotime($item->date_expired));
            $row[] = date('d M Y, H:i', strtotime($item->date_response));

            $data[] = $row;
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->billing_model->count_all(),
            "recordsFiltered" => $this->billing_model->count_filtered(),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    public function detail_billing()
    {
        $id = $this->input->post('id');
        
        $data["department"] = $this->department_model->getAll();
        $data["application"] = $this->app_model->getAll();
        $data["user"] = $this->user_model->getAll();
        $data["status"] = $this->reff_model->status_billing();
        $data["pnbp"] = $this->pnbp_model->getAll();

        $data["billing"] = $this->billing_model->getById($id);
        $data["billing_detail"] = $this->billing_model->detail_billing($id);
        
        echo $this->load->view("admin/billing/detail", $data);
    }
}
