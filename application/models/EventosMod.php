<?php 
class EventosMod extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	public function get_events(){
		$sql = "SELECT * FROM `eventos` WHERE `publishdate`>CURDATE() AND `eventos`.`estatus`='publicado' ORDER BY `eventos`.`publishdate` DESC LIMIT 0, 6";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
		{
		   return $query->result_array();
		}
		return FALSE; 
	}
	public function get_eventsDefeated(){
		$sql = "SELECT * FROM `eventos` WHERE `publishdate`<CURDATE() AND `eventos`.`estatus`='publicado' ORDER BY `eventos`.`publishdate` DESC LIMIT 0, 6";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
		{
		   return $query->result_array();
		}
		return FALSE; 
	}
	public function get_all_events(){
		$query = $this->db->get('eventos');	
		
		if ($query->num_rows() > 0)
		{
		   return $query->result_array();
		}
		return FALSE; 
	}
	public function get_event($data = 'all', $limit = '', $order = array('id', 'DESC'))
	{	
		$this->db->limit($limit);
		if ($order!=='') {
			$this->db->order_by($order[0], $order[1]);
		}
		if ($data === 'all') {
			$query = $this->db->get('eventos');	
			if ($query->num_rows() > 0)
			{
			   return $query->result_array();
			}
		}else{
			$query = $this->db->get_where('eventos', $data);
			if ($query->num_rows() > 0){
				return $query->result_array();
			}
		}
		return false;
	}
	public function get_publish_event($id = '')
	{
		if ($id) {
			$query = $this->db->get_where('eventos', array('id' => $id, 'estatus' => 'Publicado'));
			if ($query->num_rows() > 0){
				return $query->result_array();
			}else{
				return false;
			}
		}
		return false;
	}
	public function get_eventgallery($event_id='')
	{
		if (!$id) {
			return false;
		}
		$query = $this->db->get_where('album', array('evento' => $event_id));
		if ($query->num_rows() > 0){
			return $query->result_array();
		}else{
			return false;
		}
	}
	public function deleteEvent($where)
	{
		if (!$where) {
			return false;
		}
		$this->db->where($where);
		return $this->db->delete('eventos');
	}
	public function updateEvent($id = '')
	{
		if (!$id) {
			return false;
		}

		if(!$this->input->post('nombre')){
			return false;
		}
		$estatus = "0";
		if($this->input->post('status')=="on"){
			$estatus = "1";
		}
		$fecha = DateTime::createFromFormat('j F, Y',$this->input->post('publishdate'));
		$data = array(
			'nombre' => $this->input->post('nombre'),
			'titulo' => $this->input->post('titulo'),
			'texto' => $this->input->post('texto'),
			'imagen' => $this->input->post('imagen'), 
			'thumb' => $this->input->post('thumb'),
			'ciudad' => $this->input->post('ciudad'),
			'publishdate' => $fecha->format('Y-m-d H:i:s'),
			'fecha' => $this->input->post('fecha'),
			'lugar' => $this->input->post('lugar'),
			'status' => $estatus
		);

		$this->db->where('id', $id);

		if (!$this->db->update('eventos', $data)) {
			return false;
		}
		
		return true;
	}
	public function setEventData()
	{
		$estatus = "0";
		if($this->input->post('status')=="on"){
			$estatus = "1";
		}
		$fecha = DateTime::createFromFormat('j F, Y',$this->input->post('publishdate'));
		$data = array(
			'nombre' => $this->input->post('nombre'),
			'titulo' => $this->input->post('titulo'),
			'texto' => $this->input->post('texto'),
			'imagen' => $this->input->post('imagen'), 
			'thumb' => $this->input->post('thumb'),
			'ciudad' => $this->input->post('ciudad'),
			'publishdate' => $fecha->format('Y-m-d H:i:s'),
			'fecha' => $this->input->post('fecha'),
			'lugar' => $this->input->post('lugar'),
			'status' => $estatus 
		);

		if (!$this->db->insert('eventos', $data)) {
			return false;
		}

	}
}
?> 