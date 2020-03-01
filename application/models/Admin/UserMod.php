<?php 
class UserMod extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	public function get_user($data = 'all', $limit = '', $order = array('id', 'ASC')){
		$this->db->select('user.*, usergroup.name, usergroup.level');
		$this->db->join('usergroup', 'user.usergroup = usergroup.id');
		if ($data !== "all") {
			
			$query = $this->db->get_where('user', $data);
			if ($query->num_rows() > 0)
			{
				return $query->result_array(); 
			}
				return false; 
		}else{
			$query = $this->db->get('user');
			if ($query->num_rows() > 0)
			{
				return $query->result_array(); 
			}
				return false; 
		}
	}

	public function set_user($data)
	{
		return $this->db->insert('user', $data);
	}
	public function update_user($data, $id)
	{
		$this->db->where('id', $id);
		return $this->db->update('user', $data);
	}

	public function get_is_block_user($id = false){
		if ($id) {
			$query = $this->db->get_where('user', array('id' => $id));
			if ($query->num_rows() > 0){
				foreach ($query->result_array() as  $data) {
					if ($data['status'] == 1) {
						return true;
					}
				} 
				return false;
			}else{
				return false;
			}
		}
		return false;
	}

	public function set_block_user($id = false, $value = false){
		//El parametro es nulo
		if ($id == false) {
			return false;
		}
		// Es el usuario actual
		if ($id == $this->session->userdata('id')) {
			return false;
		}
		// Exite el usuario solicitado
		if ($this->get_is_user_exist($id)){			
			if ($value) {
				$data = array('act' => 1 );
			}else{
				$data = array('act' => 0 );
			}
			
			$this->db->where('id', $id);
			$this->db->update('user', $data);

			return true;
		}
		return false;
	}

	public function set_datauserstorage($data){
		
		if (!$data) {
			return false;
		}
		if (!$this->db->insert('datauserstorage', $data)) {
			return false;
		}		
		return true;
	}

	public function get_datauserstorage($data)
	{
		if ($data === 'all') {
			$query = $this->db->get('datauserstorage');	
			if ($query->num_rows() > 0)
			{
			   return $query->result_array();
			}
		}else{
			$query = $this->db->get_where('datauserstorage', $data);
			if ($query->num_rows() > 0){
				return $query->result_array();
			}
		}
		return false;
	}
	
	public function update_datauserstorage($data, $where)
	{
		$this->db->where($where);
		return $this->db->update('datauserstorage', $data);
	}

	public function delete_datauserstorage($data)
	{
		if (!$data) {
			return false;
		}
		$this->db->where($data);
		return $this->db->delete('datauserstorage');
	}

	public function get_is_user_exist($id=false){
		if ($id) {
			$query = $this->db->get_where('user', array('id' => $id));
			if ($query->num_rows() > 0){
				return true;
			}
		}
		return false;
	}
	
	public function deleteUser($id='')
	{
		if (!$id) {
			return false;
		}
		$this->db->where('id', $id);
		if (!$this->db->delete('user')) {
			return false;
		}
		return true;
	}
	public function get_usergroup($data = 'all', $limit = '', $order = array('id', 'ASC'))
	{
		$limit ? $this->db->limit($limit) : null;
		if ($order!=='') {
			$this->db->order_by($order[0], $order[1]);
		}
		if ($data === 'all') {
			$query = $this->db->get('usergroup');	
			if ($query->num_rows() > 0)
			{
			   return $query->result_array();
			}
		}else{
			$query = $this->db->get_where('usergroup', $data);
			if ($query->num_rows() > 0){
				return $query->result_array();
			}
		}
		return false;
	}
}
?>