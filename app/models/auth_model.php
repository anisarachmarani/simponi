<?php 
class Auth_model extends CI_Model 
{

	public function __construct()
	{
        parent::__construct();
	}

	// function register($name,$password,$nama)
	// {
	// 	$data_user = array(
	// 		'name'=>$name,
	// 		'password'=>password_hash($password,PASSWORD_DEFAULT),
	// 		'nama'=>$nama
	// 	);
	// 	$this->db->insert('t_user',$data_user);
	// }

	function login_user($name,$password)
	{
        $query = $this->db->get_where('t_user',array('login'=>$name));
        if($query->num_rows() > 0)
        {
            $data_user = $query->row();
			// var_dump($data_user->name);
			// die();
            if (password_verify($password, $data_user->password)) {
                $this->session->set_userdata('name',$name);
				$this->session->set_userdata('nama',$data_user->name);
				$this->session->set_userdata('department_id',$data_user->department_id);
				$this->session->set_userdata('is_login',TRUE);
                return TRUE;
            } else {
                return FALSE;
            }
        }
        else
        {
            return FALSE;
        }
	}
	
    function cek_login()
    {
        if(empty($this->session->userdata('is_login')))
        {
			redirect('index.php/Login');
		}
    }
}
?>