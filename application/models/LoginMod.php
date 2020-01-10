<?php 
class LoginMod extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	public function isLoged($username, $password){
		$data = array('username' => $username, 'password'=> $password, 'user.status' => 1);
		$this->db->select('user.*, usergroup.name, usergroup.level');
		$this->db->join('usergroup', 'user.usergroup = usergroup.id');
		$query = $this->db->get_where('user', $data); 	
		
		if ($query->num_rows() > 0)
		{
		   foreach ($query->result() as $row)
		   {
				$newdata = array(
					'id' => $row->id,
                	'username'  => $row->username,
                	'type'  => $row->name,
                	'lastseen'  => $row->lastseen,
                	'level' => $row->level,
                	'logged_in' => TRUE
               );

		   }

		  $this->session->set_userdata($newdata);
		  $query = $this->db->get_where('datauserstorage', array('_key' => 'avatar', 'id_user'=>$newdata['id']));
			if ($query->num_rows() > 0){
				$query = $query->result_array();
		  		$this->session->set_userdata('avatar',$query[0]['_value']);
				
			}
		   	return TRUE;		   
		}
		return FALSE; 
	}
}
?>