<?php defined('BASEPATH') or exit('No direct script access allowed');

class Billing_model extends CI_Model
{
	private $_tbilling = "t_billing";
	private $_tbillingDetail = "t_billing_detail";
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

	var $column_order = array(null, 'billing_id', 'date_register', 'date_simponi', 'department_name', 'total', 'status_name', 'error', 'error_pay', 'date_expired', 'date_response'); //set column field database for datatable orderable 
	var $column_search = array('billing_id', 'date_register', 'date_simponi', 'department_id', 'total', 'status', 'error', 'error_pay', 'date_expired', 'date_response'); //set column field database for datatable searchable 
	var $order = array('date_register' => 'desc'); // default order 

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		$this->db->select("
		b.*, 
		dp.id AS department_id, dp.name AS department_name, 
		r.id AS status, r.name AS status_name"
		);
		
		$this->db->from("$this->_tbilling b");
		$this->db->join("$this->_tdepartment dp","b.department_id = dp.id"); 
		$this->db->join("$this->_treff r","b.status = r.id"); 
		$this->db->where('b.billing_id is NOT NULL', NULL, FALSE);
		// $this->db->where('b.department_id', 1);

		// add custom filter here
		if($this->input->post('department'))
		{
			$this->db->where('b.department_id', $this->input->post('department'));
		}

		if($this->input->post('billing_id'))
		{
			$this->db->where('b.billing_id', $this->input->post('billing_id'));
		}

		if($this->input->post('date_register'))
		{
			$this->db->where('b.date_register', $this->input->post('date_register'));
		}

		if($this->input->post('date_register'))
		{
			$this->db->where('b.date_register', $this->input->post('date_register'));
		}

		if($this->input->post('status'))
		{
			$this->db->where('b.status', $this->input->post('status'));
		}
		
		if($this->input->post('date_simponi'))
		{
			$this->db->where('b.date_simponi', $this->input->post('date_simponi'));
		}

		$i = 0;
		
		foreach ($this->column_search as $item) {
			$search = $_POST['search']['value'];
			if ($search) {
				if ($i == 0) {
					// $this->db->group_start();
					$this->db->where("$item like '%$search%' ");
				} else {
					$this->db->or_where("$item like '%$search%' ");
				}

				if (count($this->column_search) - 1 == $i) {
				// 	$this->db->group_end();
				}
			}
			$i++;
		}

		if (isset($_POST['order'])) {
			$column = $this->column_order[$_POST['order']['0']['column']];
			$dir = $_POST['order']['0']['dir'];
			$this->db->order_by("$column", "$dir");
		} else if (isset($this->order)) {
			$order = $this->order;
			$key = key($order);
			$dir = $order[key($order)];
			$this->db->order_by("$key", "$dir");
		}
	}

	function get_datatables()
	{
		$this->_get_datatables_query();
		if ($_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all()
	{
		$this->db->from($this->_tbilling);
		return $this->db->count_all_results();
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

    public function getFiveClient()
    {
      $department_id = $this->session->userdata('department_id');
      $this->db->select("
        $this->_tbilling.*, 
        $this->_tdepartment.id AS department_id, $this->_tdepartment.name AS department_name, 
        $this->_treff.id AS status, $this->_treff.name AS status_name"
      );
      $this->db->where("department_id like '$department_id' ");
      $this->db->order_by("date_register", "desc");
      $this->db->limit(5);
      $this->db->from("$this->_tbilling");
      $this->db->join("$this->_tdepartment","$this->_tbilling.department_id = $this->_tdepartment.id"); 
      $this->db->join("$this->_treff","$this->_tbilling.status = $this->_treff.id"); 
      $query = $this->db->get();
      return $query->result();
    }
    
    public function getById($id)
    {
      return $this->db->get_where($this->_tbilling, ["id" => $id])->row();
    }

    public function detail_billing($id)
    {
      return $this->db->get_where($this->_tbillingDetail, ["billing_id" => $id])->row();
    }
}
