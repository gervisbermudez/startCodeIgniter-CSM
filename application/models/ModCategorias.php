<?php 
class ModCategorias extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	public function set_categoria($data){
		if (!$data) {
			return FALSE; 
		}
		return $this->db->insert('categorias', $data); 
	}
	public function get_categoria($data = 'all', $limit = '', $order = array('id', 'DESC'))
	{
		$this->db->limit($limit);
		if ($order!=='') {
			$this->db->order_by($order[0], $order[1]);
		}
		if ($data === 'all') {
			$query = $this->db->get('categorias');	
			if ($query->num_rows() > 0)
			{
			   return $query->result_array();
			}
		}else{
			$query = $this->db->get_where('categorias', $data);
			if ($query->num_rows() > 0){
				return $query->result_array();
			}
		}
		return false;
	}
	public function update_categoria($where,$data)
	{
		$this->db->where($where);
		return $this->db->update('categorias', $data);
	}
}
?>