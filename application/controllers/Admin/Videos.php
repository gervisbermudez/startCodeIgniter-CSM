<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Videos extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('ModVideo');
	}

	public function index()
	{
		$data['title'] = "Admin | Videos";
		$data['h1'] = "Videos";
		$data['header'] = $this->load->view('admin/header', $data, TRUE);
		$data['videos'] = $this->ModVideo->get_video('all');

		$this->load->view('admin/head', $data);
		$this->load->view('admin/navbar', $data);
		$this->load->view('admin/videos/todos', $data);
		$this->load->view('admin/footer', $data);
	}
	
	public function categorias($categoria='all')
	{	
		$this->load->helper('string');
		$data['title'] = "Admin | Videos";
		$data['h1'] = "Categorias de Videos";
		$data['header'] = $this->load->view('admin/header', $data, TRUE);
		$data['categorias'] = $this->ModVideo->get_categoria(array('tipo' => 'video' ));
		
		$this->load->view('admin/head', $data);
		$this->load->view('admin/navbar', $data);
		$this->load->view('admin/videos/categorias', $data);
		$this->load->view('admin/footer', $data);
	}

	public function ver($id = 'all')
	{
		if ($id == 'all') {
			$this->index();
		}else{
					
			$data['video'] = $this->ModVideo->get_video(array('id' => $id))[0];

			if ($data['video']) {

				$data['title'] = "Admin | Videos";
				$data['h1'] = $data['video']['nombre'];
				$data['header'] = $this->load->view('admin/header', $data, TRUE);
				$data['categorias'] = $this->ModVideo->get_video_categoria_rel(array('`video-categoria`.`id_video`'=>$id));
				$data['modalid'] = random_string('alnum', 16);
				$data['itemid'] = random_string('alnum', 16);
				$this->load->library('menu');
				$this->menu->char = "'";
				$this->menu->set_ul_properties($properties = array('class' => 'dropdown-content', 'role' => 'menu', 'id' => 'options'));
				$links = array('Ver en la web' => array('target' => '_blank', 'href' => base_url('videos/ver/'.$id)));
				if (5 > $this->session->userdata('level')) {
						 $links['Editar'] = array('href' => base_url('admin/videos/editar/'.$id) );
				}
				if (3 > $this->session->userdata('level')) {
						 $links['Eliminar'] = array('href' => '#'.$data['modalid'], 
						 	'class' => 'modal-trigger',
						 	'data-action-param'=>'{"id":"'.$id.'", "table":"video"}',
						 	'data-url'=>'admin/videos/fn_ajax_delete_data',
						 	'data-url-redirect'=>'admin/videos/',
						 	'data-ajax-action'=>'inactive',
						 	);
				}
				$data['options'] = $this->menu->get_menu($links);

				$this->load->view('admin/head', $data);
				$this->load->view('admin/navbar', $data);
				$this->load->view('admin/videos/ver', $data);
				$this->load->view('admin/footer', $data);
			}else{
				$this->showError('El video al que intenta acceder no existe :(');
			}
		}
	}
	
	public function nuevo()
	{
		$this->load->helper('array');

		$data['title'] = "Admin | Videos - Nuevo";
		$data['h1'] = "Videos";
		$data['header'] = $this->load->view('admin/header', $data, TRUE);
		$data['video'] = array();
		$data['action'] = 'admin/videos/save';
		$data['footer_includes'] = array(
				'tinymce'=>'<script src="'.base_url('js/tinymce/js/tinymce/tinymce.min.js').'"></script>',
				'tinymceinit' => "<script>tinymce.init({ selector:'textarea',  plugins : ['link table'] });</script>");
		$data['videocategoria'] = array();
		$data['categorias'] = $this->ModVideo->get_categoria(array('tipo' => 'video' ));

		$this->load->view('admin/head', $data);
		$this->load->view('admin/navbar', $data);
		$this->load->view('admin/videos/crear', $data);
		$this->load->view('admin/footer', $data);
	}
	public function save()
	{
		$status = 0;
		if ($this->input->post('status')==='1') {
			$status = 1;
		}
		$fecha = new DateTime;
		$data = array(
			'id' => 'NULL', 
			'nombre' =>  $this->input->post('nombre'),
			'description' => $this->input->post('description'),
			'duration' => $this->input->post('duration'),
			'youtubeid' => $this->input->post('youtubeid'),
			'preview' => $this->input->post('preview'),
			'payinfo' => $this->input->post('paypal'),
			'fecha' => $fecha->format('Y-m-d H:i:s'),
			'status' => $status
		);
		if($this->ModVideo->set_video($data)){
			unset($data['id']);
			$video = $this->ModVideo->get_video($data);
			if ($this->input->post('categorias')) {
			foreach ($this->input->post('categorias') as $key => $value) {
				$this->ModVideo->set_video_categoria(array('id' => 'NULL', 'id_video' => $video[0]['id'], 'id_categoria' => $value));
			}
			}
			$this->load->model('ModRelations');
			$relations = array('id_user' => $this->session->userdata('id'), 'tablename' => 'video', 'id_row' => $video[0]['id'], 'action' => 'crear');
			$this->ModRelations->set_relation($relations);

			redirect('admin/videos/ver/'.$video[0]['id']);
		}else{
			$this->showError();
		}
	}

	public function editar($id)
	{

		$data['video'] = $this->ModVideo->get_video(array('id' => $id))[0];
		if ($data['video']) {
			$this->load->helper('array');

			$data['title'] = "Admin | Videos";
			$data['h1'] = "Videos";
			$data['header'] = $this->load->view('admin/header', $data, TRUE);
			$data['action'] = 'admin/videos/update';
			$data['video'] = $this->ModVideo->get_video(array('id' => $id))[0];
			$data['categorias'] = $this->StModel->get_data(array('tipo' => 'Video'), 'categorias');

			$videocategoria = $this->ModVideo->get_video_categoria(array('`id_video`'=>$id));
			$data['videocategoria'] = array();
			if ($videocategoria) {
				foreach ($videocategoria as $key => $value) {
					$data['videocategoria'][$value['id_categoria']]=TRUE;
				}
			}
			$data['footer_includes'] = array(
				'tinymce'=>'<script src="'.base_url('js/tinymce/js/tinymce/tinymce.min.js').'"></script>',
				'tinymceinit' => "<script>tinymce.init({ selector:'textarea',  plugins : ['link table'] });</script>");

			$this->load->view('admin/head', $data);
			$this->load->view('admin/navbar', $data);
			$this->load->view('admin/videos/crear', $data);
			$this->load->view('admin/footer', $data);
		}else{
			$this->showError();
		}
	}

	public function update()
	{
		$status = 0;
		if ($this->input->post('status')==='1') {
			$status = 1;
		}
		$where = array('id' => $this->input->post('id'));
		$data = array(
			'nombre' =>  $this->input->post('nombre'),
			'description' => $this->input->post('description'),
			'duration' => $this->input->post('duration'),
			'youtubeid' => $this->input->post('youtubeid'),
			'preview' => $this->input->post('preview'),
			'payinfo' => $this->input->post('paypal'),
			'status' => $status
		);
		if($this->ModVideo->update_video($where, $data)){
			$this->ModVideo->delete_video_categoria(array('id_video'=>$this->input->post('id')));
			foreach ($this->input->post('categorias') as $key => $value) {
				$this->ModVideo->set_video_categoria(array('id' => 'NULL', 'id_video' => $this->input->post('id'), 'id_categoria' => $value));
			}
			redirect('admin/videos/ver/'.$this->input->post('id'));
		}else{
			$this->showError();
		}
	}

}