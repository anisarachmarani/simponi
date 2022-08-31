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

    public function rules()
    {
        return [
            ['field' => 'npwp',
            'label' => 'NPWP',
            'rules' => 'required | numeric'],

            ['field' => 'code',
            'label' => 'Code',
            'rules' => 'numeric']
        ];
    }

    public function getAll()
    {
        $where = "transaction_id != ''";
        return $this->db->where($where)->get($this->_table)->result();
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