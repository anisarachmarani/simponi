<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Billing_model extends CI_Model
{
    private $_table = "t_billing";
    
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
        $this->db->from('t_billing');
        $query = $this->db->get();
        return $query->result();
    }
    
    // public function getById($id)
    // {
    //     return $this->db->get_where($this->_table, ["product_id" => $id])->row();
    // }

    // public function save()
    // {
    //     $post = $this->input->post();
    //     $this->product_id = uniqid();
    //     $this->name = $post["name"];
    //     $this->price = $post["price"];
    //     $this->description = $post["description"];
    //     return $this->db->insert($this->_table, $this);
    // }

    // public function update()
    // {
    //     $post = $this->input->post();
    //     $this->product_id = $post["id"];
    //     $this->name = $post["name"];
    //     $this->price = $post["price"];
    //     $this->description = $post["description"];
    //     return $this->db->update($this->_table, $this, array('product_id' => $post['id']));
    // }

    // public function delete($id)
    // {
    //     return $this->db->delete($this->_table, array("product_id" => $id));
    // }
}