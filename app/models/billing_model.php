<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Billing_model extends CI_Model
{
    private $_tbilling = "t_billing";
    private $_tdepartment = "m_department";
    private $_treff = "m_reff";
    
    public $id;
    public $application_id;
    public $transaction_id;
    public $date_register;
    public $date_expired;
    public $npwp;
    public $total;
    public $detail;
    public $status;
    public $simponi_id;
    public $error;
    public $error_pay;

    public function getAll($department, $billing_id, $date_register, $date_simponi, $status)
    {
      $this->db->select("
        $this->_tbilling.*, 
        $this->_treff.id AS status, $this->_treff.name AS status_name, 
        $this->_tdepartment.id AS department_id, $this->_tdepartment.name AS department_name, 
      ");
      $this->db->where("department_id like '%$department%' ");
      $this->db->where("date_register != '' ");
      $this->db->where("billing_id like '%$billing_id%' ");
      $this->db->where("date_register like '%$date_register%' ");
      $this->db->where("date_simponi like '%$date_simponi%' ");
      $this->db->where("status like '%$status%' ");
      $this->db->from("$this->_tbilling");
      $this->db->order_by("date_register", "desc");
      $this->db->join("$this->_treff", "$this->_tbilling.status = $this->_treff.id"); // join table m_reff
      $this->db->join("$this->_tdepartment","$this->_tbilling.department_id = $this->_tdepartment.id"); // join table m_reff
      $query = $this->db->get();
      return $query->result();
    }

    public function get_billing_client($billing_id, $date_register, $date_simponi, $status)
    {
      $department_id = $this->session->userdata('department_id');
      $this->db->select("
        $this->_tbilling.*, 
        $this->_treff.id AS status, $this->_treff.name AS status_name, 
        $this->_tdepartment.id AS department_id, $this->_tdepartment.name AS department_name, 
      ");
      $this->db->where("department_id like '$department_id' ");
      $this->db->where("date_register != '' ");
      $this->db->where("billing_id like '%$billing_id%' ");
      $this->db->where("date_register like '%$date_register%' ");
      $this->db->where("date_simponi like '%$date_simponi%' ");
      $this->db->where("status like '%$status%' ");
      $this->db->from("$this->_tbilling");
      $this->db->order_by("date_register", "desc");
      $this->db->join("$this->_treff", "$this->_tbilling.status = $this->_treff.id"); // join table m_reff
      $this->db->join("$this->_tdepartment","$this->_tbilling.department_id = $this->_tdepartment.id"); // join table m_reff
      $query = $this->db->get();
      return $query->result();
    }

    public function getFive()
    {
      $this->db->select("
        $this->_tbilling.*, 
        $this->_tdepartment.id AS department_id, $this->_tdepartment.name AS department_name, 
        $this->_treff.id AS status, $this->_treff.name AS status_name"
      );
      $this->db->order_by("date_register", "desc");
      $this->db->limit(5);
      $this->db->from("$this->_tbilling");
      $this->db->join("$this->_tdepartment","$this->_tbilling.department_id = $this->_tdepartment.id"); 
      $this->db->join("$this->_treff","$this->_tbilling.status = $this->_treff.id"); 
      $query = $this->db->get();
      return $query->result();
    }
    
    // public function getById($id)
    // {
    //     return $this->db->get_where($this->_tbilling, ["product_id" => $id])->row();
    // }

    // public function save()
    // {
    //     $post = $this->input->post();
    //     $this->product_id = uniqid();
    //     $this->name = $post["name"];
    //     $this->price = $post["price"];
    //     $this->description = $post["description"];
    //     return $this->db->insert($this->_tbilling, $this);
    // }

    // public function update()
    // {
    //     $post = $this->input->post();
    //     $this->product_id = $post["id"];
    //     $this->name = $post["name"];
    //     $this->price = $post["price"];
    //     $this->description = $post["description"];
    //     return $this->db->update($this->_tbilling, $this, array('product_id' => $post['id']));
    // }

    // public function delete($id)
    // {
    //     return $this->db->delete($this->_tbilling, array("product_id" => $id));
    // }
}