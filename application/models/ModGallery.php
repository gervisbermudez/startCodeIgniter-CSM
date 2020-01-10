<?php 
class ModGallery extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	public function get_album($data = 'all', $limit = '', $order = array('id','DESC'))
	{
		$this->db->limit($limit);
		if ($order!=='') {
			$this->db->order_by($order[0], $order[1]);
		}
		if ($data === 'all') {
			$query = $this->db->get('album');
			if ($query->num_rows() > 0)
			{
			   return $query->result_array();
			}
			return false;
		}else{
			$query = $this->db->get_where('album',$data);
			if ($query->num_rows() > 0){
				return $query->result_array();
			}
			return false;
		}
	return false;
	}

	public function get_albumitems($id_album ='', $limit = '')
	{
		if (!$id_album) {
			return false;
		}
		$this->db->limit($limit);
		$data = array('id_album' => $id_album);
			$query = $this->db->get_where('album_items',$data);
			if ($query->num_rows() > 0){
				return $query->result_array();
			}
			return false;
	}

	public function get_album_from_event($id_event=false)
	{
		if (!$id_event === false) {	
			$query = $this->db->get_where('album', array('evento' => $id_event));
			if ($query->num_rows() > 0){
				return $query->result_array();
			}
		}
		return false;
	}
	
	public function delete_item($item_name='' , $id_album = '')
	{
		if (!$item_name) {
			return false;
		}
		if ($item_name === 'all') {
			$this->db->where('id_album', $id_album);
			if (!$this->db->delete('album_items')) {
				return false;
			}
			return true;
		}else{
			$this->db->where('nombre', $item_name);
			if (!$this->db->delete('album_items')) {
				return false;
			}
			return true;
		}
		return false;
	}
	public function update_album($albumid='')
	{
		if (!$albumid) {
			return false;
		}

		$estatus = "0";
		if($this->input->post('status')=="on"){
			$estatus = "1";
		}
		$fecha = DateTime::createFromFormat('j F, Y',$this->input->post('fecha'));
		$data = array(
			'nombre' => $this->input->post('nombre'),
			'descripcion' => $this->input->post('descripcion'),
			'evento' => $this->input->post('evento'),
			'fecha' => $fecha->format('Y-m-d H:i:s'), 
			'estatus' => $estatus
		);
		$this->db->where('id', $albumid);

		if ($this->db->update('album', $data)) {
			return true;
		}
		return false;
	}
	public function set_item($idalbum='' , $item_name='')
	{
		if (!$idalbum) {
			return false;
		}

		$data = array(
			'nombre' => $item_name,
			'id_album' => $idalbum,
		);

		if (!$this->db->insert('album_items', $data)) {
			return false;
		}

		return true;
	}
	public function setTitle($file, $title)		
	{
		if(!$file){
			return false;
		}
		$data = array('titulo' => $title);
		$this->db->where('nombre', $file);
		if($this->db->update('album_items', $data)){
			return true;
		}else{
			return false;
		}
	}
	public function set_album($path='')
	{
		if (!$path) {
			return false;
		}
		$fecha = DateTime::createFromFormat('j F, Y',$this->input->post('fecha'));
		$estatus = '0';
		if($this->input->post('status')=="on"){
			$estatus = "1";
		}
		$data = array(
			'nombre' => $this->input->post('nombre'),
			'descripcion' => $this->input->post('descripcion'),
			'fecha' => $fecha->format('Y-m-d H:i:s'),
			'path' => $path,
			'estatus' => $estatus
		);

		if (!$this->db->insert('album', $data)) {
			return false;
		}

		return true;
	}

	public function delete_album($data = array())
	{
		if(!$data){
			return false;
		}
		$this->db->where($data[0],$data[1]);
		if($this->db->delete('album')){
			return true;
		}
		return false;
	}
	public function set_estatus_album($albumid = '', $data = array('estatus' => 'No publicado' ))
	{
		if ($albumid === '') {
			return false;
		}
		$this->db->where('id', $albumid);
		if($this->db->update('album', $data)){
			return true;
		}
	}

}