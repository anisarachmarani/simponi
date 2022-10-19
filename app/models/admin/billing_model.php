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

    public function getAll()
    {
        
      $detail = $this->input->post('detail'); // $_POST['detail']
      $billing_id = $this->input->post('billing_id'); // $_POST['billing_id']
      $date_register = $this->input->post('date_register'); // $_POST['date_register']
      $date_simponi = $this->input->post('date_simponi'); // $_POST['date_simponi']
      $status = $this->input->post('status'); // $_POST['status']
    
      $this->session->set_userdata('detail',$detail);
      $this->session->set_userdata('billing_id',$billing_id);
      $this->session->set_userdata('date_register',$date_register);
      $this->session->set_userdata('date_simponi',$date_simponi);
      $this->session->set_userdata('status',$status);

      $this->db->select('
        t_billing.*, m_reff.id AS status, m_reff.name, 
      ');
      $this->db->where("date_register != '' ");
      $this->db->where("detail like '%$detail%' ");
      $this->db->where("billing_id like '%$billing_id%' ");
      $this->db->where("date_register like '%$date_register%' ");
      $this->db->where("date_simponi like '%$date_simponi%' ");
      $this->db->where("name like '%$status%' ");
      $this->db->join('m_reff', 't_billing.status = m_reff.id'); // join table m_reff
      $this->db->from($this->_tbilling);
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