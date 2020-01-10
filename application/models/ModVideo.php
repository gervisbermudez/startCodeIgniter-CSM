<?php 
class ModVideo extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	public function get_video($data = 'all', $limit = '', $order = array('id', 'DESC'))
	{	
		$this->db->limit($limit);
		if ($order!=='') {
			$this->db->order_by($order[0], $order[1]);
		}
		if ($data === 'all') {
			$query = $this->db->get('video');	
			if ($query->num_rows() > 0)
			{
			   return $query->result_array();
			}
		}else{
			$query = $this->db->get_where('video', $data);
			if ($query->num_rows() > 0){
				return $query->result_array();
			}
		}
		return false;
	}
	
	public function delete_video($where)
	{	
		if (!$where) {
			return false;
		}
		$this->db->where($where);
		return $this->db->delete('video');
	}
	public function delete_categorias($where)
	{	
		if (!$where) {
			return false;
		}
		$this->db->where($where);
		return $this->db->delete('categorias');
	}
	public function update_video($where,$data)
	{
		$this->db->where($where);
		return $this->db->update('video', $data);
	}

	public function set_video($data)
	{
		return $this->db->insert('video', $data);
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

	public function get_video_categoria($data = 'all', $limit = '', $order = array('id', 'DESC'))
	{
		$this->db->limit($limit);
		if ($order!=='') {
			$this->db->order_by($order[0], $order[1]);
		}
		if ($data === 'all') {
			$query = $this->db->get('video-categoria');	
			if ($query->num_rows() > 0)
			{
			   return $query->result_array();
			}
		}else{
			$query = $this->db->get_where('video-categoria', $data);
			if ($query->num_rows() > 0){
				return $query->result_array();
			}
		}
		return false;
	}

	public function get_video_categoria_rel($data = 'all', $limit = '', $order = array('`video-categoria`.`id_video`', 'ASC'))
	{
		$this->db->limit($limit);
		if ($order!=='') {
			$this->db->order_by($order[0], $order[1]);
		}
		$this->db->select('`video-categoria`.`id_video` AS `video`, `categorias`.nombre AS `categoria`');
		$this->db->join('`categorias`', '`video-categoria`.`id_categoria`= `categorias`.`id`');

		if ($data === 'all') {
			$query = $this->db->get('video-categoria');	
			if ($query->num_rows() > 0)
			{
			   return $query->result_array();
			}
		}else{
			$query = $this->db->get_where('video-categoria', $data);
			if ($query->num_rows() > 0){
				return $query->result_array();
			}
		}
		return false;
	}

	public function delete_video_categoria($where)
	{
		if (!$where) {
			return false;
		}
		$this->db->where($where);
		return $this->db->delete('video-categoria');
	}

	public function set_video_categoria($data)
	{
		return $this->db->insert('video-categoria', $data);
	}
}
?> 