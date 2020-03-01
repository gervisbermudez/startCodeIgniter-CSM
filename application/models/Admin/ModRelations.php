<?php 
class ModRelations extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	public function set_relation($data)
	{
		if (!$data) {
			return false;
		}
		$date = new DateTime();
		$data['date'] = $date->format('Y-m-d H:i:s');
		return $this->db->insert('relations', $data);	
	}
	
	public function get_relation($data = 'all', $limit = '', $order = array('id','DESC'))
	{
		$limit ? $this->db->limit($limit) : null;
		if ($order!=='') {
			$this->db->order_by($order[0], $order[1]);
		}
		if ($data === 'all') {
			$query = $this->db->get('relations');
			if ($query->num_rows() > 0)
			{
			   return $query->result_array();
			}
			return false;
		}else{
			$query = $this->db->get_where('relations',$data);
			if ($query->num_rows() > 0){
				return $query->result_array();
			}
			return false;
		}
	return false;
	}

	public function delete_relation($where)
	{
		if (!$where) {
			return false;
		}
		$this->db->where($where);
		return $this->db->delete('relations');
	}
}
?>