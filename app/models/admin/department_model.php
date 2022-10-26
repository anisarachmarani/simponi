<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Department_model extends CI_Model
{
    private $_table = "m_department";

    public $id;
    public $user_id;
    public $name;
    public $currency;
    public $pnbp;
    public $password;
    public $code_unit;
    public $code_ga;
    public $code_echelon_1;
    public $code_1;
    public $code_2;
    public $code_3;

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

    public function getFive()
    {
      $this->db->order_by("id", "desc");
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
        $length = 5;
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $user_id = '';
        for ($i = 0; $i < $length; $i++) {
            $user_id .= $characters[rand(0, $charactersLength - 1)];
        }

        $data = $this->db->get($this->_table)->result();
        $insertid = '';
        foreach ($data as $value) {
            $insertid = $value->id + 1;
        }

        $post = $this->input->post();
        $this->id = $insertid;
        $this->user_id = $user_id;
        $this->name = $post["name"];
        $this->currency = $post["currency"];
        $this->pnbp = $post["pnbp"];
        $this->password = $post["password"];
        $this->code_unit = $post["code_unit"];
        $this->code_ga = $post["code_ga"];
        $this->code_echelon_1 = $post["code_echelon_1"];
        $this->code_1 = $post["code_1"];
        $this->code_2 = $post["code_2"];
        $this->code_3 = $post["code_3"];
        return $this->db->insert($this->_table, $this);
    }

    public function update()
    {
        $post = $this->input->post();
        $this->id = $post["id"];
        $this->user_id = $post["user_id"];
        $this->name = $post["name"];
        $this->currency = $post["currency"];
        $this->pnbp = $post["pnbp"];
        $this->password = $post["password"];
        $this->code_unit = $post["code_unit"];
        $this->code_ga = $post["code_ga"];
        $this->code_echelon_1 = $post["code_echelon_1"];
        $this->code_1 = $post["code_1"];
        $this->code_2 = $post["code_2"];
        $this->code_3 = $post["code_3"];
        return $this->db->update($this->_table, $this, array('id' => $post['id']));
    }

    public function delete($id)
    {
        return $this->db->delete($this->_table, array("id" => $id));
    }
}