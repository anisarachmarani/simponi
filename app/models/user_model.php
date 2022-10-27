<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
    private $_table = "t_user";

    public $id;
    public $name;
    public $email;
    public $login;
    public $password;
    public $department_id;
    public $application_id;
    public $role;
    public $status;

    public function rules()
    {
        return [
            ['field' => 'name',
            'label' => 'Name',
            'rules' => 'required'],

            ['field' => 'email',
            'label' => 'Email',
            'rules' => 'required']
        ];
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
        $data = $this->db->get($this->_table)->result();
        $insertid = '';
        foreach ($data as $value) {
            $insertid = $value->id + 1;
        }
        $post = $this->input->post();
        $this->id = $insertid;
        $this->name = $post["name"];
        $this->email = $post["email"];
        $this->login = $post["login"];
        $this->password = password_hash($post["password"], PASSWORD_DEFAULT);
        $this->department_id = $post["department_id"];
        $this->application_id = $post["application_id"];
        $this->role = $post["role"];
        $this->status = $post["status"];
        // var_dump($this);
        // die();
        return $this->db->insert($this->_table, $this);
    }

    public function update()
    {
        $post = $this->input->post();
        $this->id = $post["id"];
        $this->name = $post["name"];
        $this->email = $post["email"];
        $this->login = $post["login"];
        $this->department_id = $post["department_id"];
        $this->application_id = $post["application_id"];
        $this->role = $post["role"];
        $this->status = $post["status"];
        if ($post["password"] == "") {
            $this->password = $post["password_old"];    
        } else {
            $this->password = password_hash($post["password"], PASSWORD_DEFAULT);    
        }
        // var_dump($this);
        // var_dump($post["password"]);
        // var_dump($post["password"]);
        // die();
        return $this->db->update($this->_table, $this, array('id' => $post['id']));
    }

    public function delete($id)
    {
        return $this->db->delete($this->_table, array("id" => $id));
    }
}