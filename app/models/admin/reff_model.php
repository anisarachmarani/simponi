<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Reff_model extends CI_Model
{
    private $_table = "m_reff";

    public $id;
    public $type;
    public $name;
    public $code;

    public function rules()
    {
        // return [
        //     ['field' => 'name',
        //     'label' => 'Name',
        //     'rules' => 'required'],

        //     ['field' => 'password',
        //     'label' => 'Password',
        //     'rules' => 'required']
        // ];
    }

    public function getAll()
    {
        return $this->db->get($this->_table)->result();
    }
    
    public function getById($id)
    {
        return $this->db->get_where($this->_table, ["id" => $id])->row();
    }

    public function save()
    {
        $post = $this->input->post();
        $this->id = $post["id"];
        $this->type = $post["type"];
        $this->name = $post["name"];
        $this->code = $post["code"];
        return $this->db->insert($this->_table, $this);
    }

    public function update()
    {
        $post = $this->input->post();
        $this->id = $post["id"];
        $this->type = $post["type"];
        $this->name = $post["name"];
        $this->code = $post["code"];
        return $this->db->update($this->_table, $this, array('id' => $post['id']));
    }

    public function delete($id)
    {
        return $this->db->delete($this->_table, array("id" => $id));
    }
}