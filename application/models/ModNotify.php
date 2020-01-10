<?php 
class ModNotify extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	public function set_notify($data){
		if (!$data) {
			return FALSE; 
		}
		$date = new DateTime();
		$data['date'] = $date->format('Y-m-d H:i:s');
		return $this->insert('notificaciones', $data); 
	}
	public function get_notify($data = 'all', $limit, $order = array('id', 'DESC'))
	{
		$this->db->limit($limit);
		if ($order!=='') {
			$this->db->order_by($order[0], $order[1]);
		}
		if ($data === 'all') {
			$query = $this->db->get('notificaciones');	
			if ($query->num_rows() > 0)
			{
			   return $query->result_array();
			}
		}else{
			$query = $this->db->get_where('notificaciones', $data);
			if ($query->num_rows() > 0){
				return $query->result_array();
			}
		}
		return false;
	}
}
?>