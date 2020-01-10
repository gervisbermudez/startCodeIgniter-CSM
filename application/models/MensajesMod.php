<?php 
class MensajesMod extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}
	public function get_mensaje($data = array(), $limit='')
	{
		$this->db->limit($limit);
		$this->db->select('mensajes.*, mailfolder.namefolder');
		$this->db->join('mailfolder', 'mensajes.folder = mailfolder.id');
		$query = false;
		if ($data === 'all') {
			$query = $this->db->get('mensajes');
		}else{
			$query = $this->db->get_where('mensajes' , $data);
		}
		
		if ($query->num_rows() > 0)
		{
		   return $query->result_array();
		}
		return FALSE; 
	}
	public function update_mensajefolder($data, $datawhere)
	{
		if (!$data) {
			return false;
		}
		$this->db->where($datawhere);
		return $this->db->update('mensajes',$data);
	}
	public function delete_message($id='')
	{
		if (!$id) {
			return false;
		}
		if ($id == 'all') {
			if (!$this->db->empty_table('mensajes')) {
				return false;
			}
			return true;
		}else{
			$this->db->where('id', $id);
			if (!$this->db->delete('mensajes')) {
				return false;
			}
			return true;
		}
		return false;
	}
	public function set_Message()
	{
		
		$data = array(
			'nombre' => $this->input->post('nombre'),
			'telefono' => $this->input->post('telefono'),
			'email' => $this->input->post('email'),
			'mensaje' => $this->input->post('mensaje')
			);
		$date = new DateTime();
		$data['fecha'] = $date->format('Y-m-d H:i:s');
		return $this->db->insert('mensajes', $data);
		
	}
	public function count_mensajes($data = array('estatus' => 'No leido'))
	{
		$this->db->where($data);
		$this->db->from('mensajes');
		return $this->db->count_all_results();
	}

	public function set_mensaje_as_read($id='')
	{
		if (!$id) {
			return false;
		}
		if ($id == 'all') {
			$this->db->set('estatus', 'Leido');
			if (!$this->db->update('mensajes')) {
				return false;
			}
			return true;
		}else{
			$this->db->where('id', $id);
			$this->db->set('estatus', '1');
			if (!$this->db->update('mensajes')) {
				return false;
			}
			return true;
		}
		return false;
	}
}
?> 