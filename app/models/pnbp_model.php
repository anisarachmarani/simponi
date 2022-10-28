<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pnbp_model extends CI_Model
{
    private $_table = "m_pnbp";

    public function getAll()
    {
      return $this->db->get($this->_table)->result();
    }
}