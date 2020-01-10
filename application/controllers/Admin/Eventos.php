<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Eventos extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$data['base_url'] = $this->config->base_url();
		$data['username'] = $this->session->userdata('username');
		$data['title'] = "Admin | Eventos";
		$data['h1'] = "Eventos";
		$data['header'] = $this->load->view('admin/header', $data, TRUE);
		$this->load->view('admin/head', $data);
		$this->load->view('admin/navbar', $data);
		$this->load->model('EventosMod');
		$data['arrayEventos'] = $this->EventosMod->get_event('all');
		$this->load->view('admin/eventos/todos', $data);
		$this->load->view('admin/footer', $data);
	}
	
	public function Ver($id='')
	{
				$this->load->model('EventosMod');
				$array = $this->EventosMod->get_event(array('id' => $id), '1', '');
			if($array){
				$data['modalid'] = random_string('alnum', 16);
				$this->load->library('menu');
				$this->menu->char = "'";
				$this->menu->set_ul_properties($properties = array('class' => 'dropdown-content', 'role' => 'menu', 'id' => 'options'));
				$links = array('Ver en la web' => array('target' => '_blank', 'href' => base_url('index.php/Eventos/ver/'.$id) ));
				if (5 > $this->session->userdata('level')) {
						 $links['Editar'] = array('href' => base_url('index.php/Admin/Eventos/Editar/'.$id) );
				}
				if (3 > $this->session->userdata('level')) {
						 $links['Eliminar'] = array( 
						 	'class' => 'modal-trigger',
						 	'href' => "#".$data['modalid'],
						 	'data-action-param' => '{"id":"'.$id.'", "table":"eventos"}', 
						 	'data-url' => "admin/eventos/fn_ajax_delete_data/" ,
						 	'data-url-redirect' => "admin/eventos/",
						 	'data-ajax-action' => "inactive" 
						 	);
				}
				$data['options'] = $this->menu->get_menu($links);
				$data['evento'] = $array[0];
				$data['title'] = "Admin | Evento";
				$data['h1'] = "Detalles de un evento";
				$data['header'] = $this->load->view('admin/header', $data, TRUE);
				// Load the views
				$this->load->view('admin/head', $data);
				$this->load->view('admin/navbar', $data);
				$this->load->view('admin/eventos/ver', $data);
				$this->load->view('admin/footer', $data);
			}else{
				$this->showError();
			}
	}

	public function Agregar()
	{
		$data['base_url'] = $this->config->base_url();
		$this->load->helper('url');
		$data['title'] = "Admin | Nuevo Evento";
		$data['h1'] = "Agregar nuevo Evento";
		$data['header'] = $this->load->view('admin/header', $data, TRUE);
		$data['footer_includes'] = array('tinymce'=>'<script src="'.base_url('js/tinymce/js/tinymce/tinymce.min.js').'"></script>',
			'tinymceinit' => "<script>tinymce.init({ selector:'textarea',  plugins : ['advlist autolink lists link image charmap print preview anchor','searchreplace visualblocks code fullscreen','insertdatetime media table contextmenu imagetools'] });</script>");
		$this->load->view('admin/head', $data);
		$this->load->view('admin/navbar', $data);
		$this->load->view('admin/eventos/crear', $data);
		$this->load->view('admin/footer', $data);
	}

	public function save()
	{
		// set the url base
		$data['base_url'] = $this->config->base_url();
			if($this->input->post('nombre')){
				$this->load->model('EventosMod');
				if(!$this->EventosMod->setEventData()){
					$event = $this->EventosMod->get_event(array('nombre'=>$this->input->post('nombre')),'','');
					$id = $event[0]['id'];
					$this->load->model('ModRelations');
					$relations = array('id_user' => $this->session->userdata('id'), 'tablename' => 'eventos', 'id_row' => $id, 'action' => 'crear');
					$this->ModRelations->set_relation($relations);
					redirect('admin/eventos/ver/'.$id);
				}else{
					$this->showError();
				}
			}
	}

	public function Editar($id = "")
	{

		
			if($id != ""){
				$this->load->model('EventosMod');
				$array = $this->EventosMod->get_event(array('id' =>$id),'');
				if($array){
				$data['eventdata'] = $array[0];
				$data['title'] = "Admin | Editar Evento";
				$data['h1'] = "Editar un evento";
				$data['header'] = $this->load->view('admin/header', $data, TRUE);
				// Load the views
				$this->load->helper('url');
				$this->load->view('admin/head', $data);
				$this->load->view('admin/navbar', $data);
				$this->load->view('admin/eventos/editar', $data);
				$this->load->helper('url');
				$data['footer_includes'] = array('tinymce'=>'<script src="'.base_url('js/tinymce/js/tinymce/tinymce.min.js').'"></script>',
				'tinymceinit' => "<script>tinymce.init({ selector:'textarea',  plugins : ['advlist autolink lists link image charmap print preview anchor','searchreplace visualblocks code fullscreen','insertdatetime media table contextmenu imagetools'] });</script>");
				
				$this->load->view('admin/footer', $data);
			}else{
				$this->showError();
			}
		}else{
			$this->index();
		}
	}

	public function update($id = "")
	{
		if($id != ""){
			$this->load->model('EventosMod');
			$array = $this->EventosMod->get_event(array('id' => $id),'');
			if($array){
			if ($this->EventosMod->updateEvent($id)) {
					$this->Ver($id);
			}else{
				$this->showError();
			}
			}else{
					$this->showError();
			}
		}else{
			$this->index();
		}
		
	}
}
