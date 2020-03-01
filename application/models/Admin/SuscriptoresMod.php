<?php 
class SuscriptoresMod extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	
	public function setSuscriptorData()
	{
		$this->load->helper('date');
			$datestring = '%Y-%m-%d';
			$time = time();
			$fecha = mdate($datestring, $time);
		
		$this->load->helper('strbefore');
		$recibir = "Ambos";
		if ($this->input->post('ambos')) {
			$recibir = $this->input->post('ambos');
		}else if($this->input->post('conferencias')){
				$recibir = $this->input->post('conferencias');
		}else{
			$recibir = $this->input->post('entretenimiento');
		}

		$data = array(
			'nombre' => $this->input->post('nombre'),
			'email' => $this->input->post('email'),
			'recibir' => $recibir,
			'codigo' => getRandomCode(),
			'fecha' => $fecha
		);
		if (!$this->db->insert('suscriptores', $data)) {
			return false;
		}

	}
	public function get_all_suscriptores()
	{
		$query = $this->db->get('suscriptores');
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}else{
			return false;
		}
	}
	public function get_existMail($email='')
	{
		$query = $this->db->get_where('suscriptores', array('email' => $email));
		if ($query->num_rows() > 0)
		{
			return true;
		}else{
			return false;
		}

	}
	public function count_suscriptores($data = array())
	{
		$this->db->where($data);
		$this->db->from('suscriptores');
		return $this->db->count_all_results();
	}
	public function update_state($codigo = '')
	{
		if(!$codigo){
			return false;
		}
		$query = $this->db->get_where('suscriptores', array('codigo' => $codigo));
		if ($query->num_rows() > 0)
		{
			$data = array('estatus' => 'Verificado');
			$this->db->where('codigo', $codigo);
			$this->db->update('suscriptores', $data);
			return true;

		}else{
			return false;
		}
	}
	public function getCode($email = '')
	{
		$query = $this->db->get_where('suscriptores', array('email' => $email));
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}else{
			return false;
		}
	}
	public function deleteSuscriptorData($id)
	{
		if (!$id) {
			return false;
		}
		$this->db->where('id', $id);
		if ($this->db->delete('suscriptores')) {
			return true;
		}
		return false;
	}
}
?> 