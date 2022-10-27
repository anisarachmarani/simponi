<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_model extends CI_Model
{
    private $_tpayment = "t_payment";
    private $_tdepartment = "m_department";
    private $_treff = "m_reff";
    
    public $id;
    public $department_id;
    public $billing_id;
    public $application_id;
    public $transaction_id;
    public $user_id;
    public $status;

    public function getAll($department_id, $billing_id, $application_id, $transaction_id, $user_id, $status)
    {
      $this->db->where("date_register != '' ");
      $this->db->where("department_id like '%$department_id%' ");
      $this->db->where("billing_id like '%$billing_id%' ");
      $this->db->where("application_id like '%$application_id%' ");
      $this->db->where("transaction_id like '%$transaction_id%' ");
      $this->db->where("user_id like '%$user_id%' ");
      $this->db->where("status like '%$status%' ");
      $this->db->from("$this->_tpayment");
      $this->db->order_by("date_register", "desc");
      $query = $this->db->get();
      return $query->result();
    }

    public function get_payment_client($billing_id, $application_id, $transaction_id, $user_id, $status)
    {
      $department_id = $this->session->userdata('department_id');
      $this->db->where("date_register != '' ");
      $this->db->where("department_id like '%$department_id%' ");
      $this->db->where("billing_id like '%$billing_id%' ");
      $this->db->where("application_id like '%$application_id%' ");
      $this->db->where("transaction_id like '%$transaction_id%' ");
      $this->db->where("user_id like '%$user_id%' ");
      $this->db->where("status like '%$status%' ");
      $this->db->from("$this->_tpayment");
      $this->db->order_by("date_register", "desc");
      $query = $this->db->get();
      return $query->result();
    }

    public function getFive()
    {
      $this->db->select("
        $this->_tpayment.*, 
        $this->_tdepartment.id AS department_id, $this->_tdepartment.name AS department_name, 
        $this->_treff.id AS status, $this->_treff.name AS status_name"
      );
      $this->db->order_by("date_register", "desc");
      $this->db->limit(5);
      $this->db->from("$this->_tpayment");
      $this->db->join("$this->_tdepartment","$this->_tpayment.department_id = $this->_tdepartment.id"); 
      $this->db->join("$this->_treff","$this->_tpayment.status = $this->_treff.id"); 
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