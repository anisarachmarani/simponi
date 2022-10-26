<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Bank_model extends CI_Model
{
    private $_table = "m_bank";

    public $id;
    public $code;
    public $name;

    public function rules()
    {
        return [
            ['field' => 'name',
            'label' => 'Name',
            'rules' => 'required'],

            ['field' => 'code',
            'label' => 'Code',
            'rules' => 'numeric']
        ];
    }

    public function getAll()
    {
        return $this->db->get($this->_table)->result();
    }

    public function getFive()
    {
      $this->db->order_by("name", "asc");
      $this->db->limit(5);
      $this->db->from($this->_table);
      return $this->db->get()->result();
    }
    
    public function getById($id)
    {
        return $this->db->get_where($this->_table, ["id" => $id])->row();
    }

    public function save()
    {
        $data = $this->db->get($this->_table)->result();
        $insertid = '';
        foreach ($data as $value) {
            $insertid = $value->id + 1;
        }
        $post = $this->input->post();
        $this->id = $insertid;
        $this->name = $post["name"];
        $this->code = $post["code"];
        return $this->db->insert($this->_table, $this);
    }

    public function update()
    {
        $post = $this->input->post();
        $this->id = $post["id"];
        $this->name = $post["name"];
        $this->code = $post["code"];
        return $this->db->update($this->_table, $this, array('id' => $post['id']));
    }

    public function delete($id)
    {
        return $this->db->delete($this->_table, array("id" => $id));
    }
}