<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mensajes extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	
	public function index($folder='Inbox')
	{
		$this->load->model('MensajesMod');
		$this->load->helper('text');
		$data['username'] = $this->session->userdata('username');
		$data['title'] = "Admin | Mensajes";
		$this->load->model('MensajesMod');
		$data['folder'] = $folder;
		$data['h1'] = "";
		$data['header'] = '';
		$data['mensajes'] = $this->MensajesMod->get_mensaje(array('namefolder' => $folder));
		$data['list'] = $this->load->view('admin/mensajes/list', $data, TRUE);
		$data['head_includes'] = array('message.css' => link_tag('css/admin/message.css'));
		$data['footer_includes'] = array('tinymce'=>'<script src="'.base_url('js/chips.js').'"></script>');
		$this->load->view('admin/head', $data);
		$this->load->view('admin/navbar', $data);
		$this->load->view('admin/mensajes/todos', $data);
		$this->load->view('admin/footer', $data);
	}
	public function folder($folder='Inbox')
	{
		$this->load->model('MensajesMod');
		$this->load->helper('text');
		$data['username'] = $this->session->userdata('username');
		$data['title'] = "Admin | Mensajes";
		$this->load->model('MensajesMod');
		$data['h1'] = "";
		$data['header'] = '';
		$data['folder'] = $folder;
		$data['head_includes'] = array('message.css' => link_tag('css/admin/message.css'));
		$data['mensajes'] = $this->MensajesMod->get_mensaje(array('namefolder' => $folder));
		if (!$data['mensajes']) {
			$data['mensajes'] = $this->MensajesMod->get_mensaje(array('namefolder' => 'Inbox'));
			$data['folder'] = 'Inbox';
		}
		$data['list'] = $this->load->view('admin/mensajes/list', $data, TRUE);
		$data['footer_includes'] = array('tinymce'=>'<script src="'.base_url('js/chips.js').'"></script>');
		$this->load->view('admin/head', $data);
		$this->load->view('admin/navbar', $data);
		$this->load->view('admin/mensajes/todos', $data);
		$this->load->view('admin/footer', $data);
	}
	public function ver($id='')
	{
		$this->load->model('MensajesMod');
		$data['mensajes'] = $this->MensajesMod->get_mensaje('all');
		$this->load->helper('array');
		$this->load->library('parser');
		$quotes = array(" indigo", "blue"," cyan",  "green", "pink", "lime", 'orange');
		$deep = array("darken-1", 'accent-4', ''); 
		$mensaje = $this->MensajesMod->get_mensaje(array('mensajes.id' => $id))[0];
		$this->MensajesMod->set_mensaje_as_read($id);
		$data = array('_subject' => $mensaje['_subject'],
			'color'=>random_element($quotes).' '.random_element($deep),
			'initial_letter'=>substr($mensaje['_from'],0,1),
			'_from'=>$mensaje['_from'], '_mensaje'=>$mensaje['_mensaje'],'mensaje-id'=>$id);
		$data['preview'] = $this->parser->parse('admin/mensajes/preview', $data,TRUE);
		$data['mensajes'] = $this->MensajesMod->get_mensaje('all');
		$data['list'] = $this->load->view('admin/mensajes/list', $data, TRUE);
		$data['base_url'] = $this->config->base_url();
		$this->load->helper('text');
		$data['username'] = $this->session->userdata('username');
		$data['title'] = "Admin | Mensajes";
		$this->load->model('MensajesMod');
		$data['h1'] = "";
		$data['header'] = '';//$this->load->view('admin/header', $data, TRUE);
		$this->load->view('admin/head', $data);
		$this->load->view('admin/navbar', $data);
		$this->load->view('admin/mensajes/todos', $data);
		$this->load->view('admin/footer', $data);
	}
	public function getfolder($foldername='Inbox')
	{
		$data['base_url'] = $this->config->base_url();
		$this->load->model('MensajesMod');
		$this->load->helper('text');
		$this->load->model('MensajesMod');
		$data['mensajes'] = $this->MensajesMod->get_mensaje(array('folder' => $foldername));
		$this->load->view('admin/mensajes/list', $data);
	}
	public function showError($errorMsg = 'Ocurrio un error inesperado', $data = array('title' => 'Error', 'h1' => 'Error'))
	{	
		$data['base_url'] = $this->config->base_url();
		$data['conten'] = $errorMsg;
		$data['header'] = $this->load->view('admin/header', $data, TRUE);
		$this->load->view('admin/head', $data);
		$this->load->view('admin/navbar', $data);	
		$this->load->view('admin/template', $data);
		$this->load->view('admin/footer', $data);
	}
	public function get_mensaje_by_ajax()
	{
		$this->load->model('MensajesMod');
		
		$this->load->helper('array');
		$id_mensaje = $this->input->post('id_mensaje');
		$data['mensajes'] = $this->MensajesMod->get_mensaje(array('mensajes.id' => $id_mensaje));
		$this->MensajesMod->set_mensaje_as_read($id_mensaje);
		$preview = $this->load->view('admin/mensajes/preview', $data, TRUE);
		$this->output->set_output($preview);
	}
	public function Send()
	{
		if ($this->input->post('email')) {
			$this->load->helper('date');
			$datestring = '%Y-%m-%d %h:%i:%a';
			$time = time();
			$fecha = mdate($datestring, $time);
			echo "$fecha";
			$this->load->model('MensajesMod');
			$this->MensajesMod->set_Message();
		}
	}
	public function update_messagefolder_byajax()
	{
		$id = $this->input->post('id');
		$folder = $this->input->post('folder');
		$this->load->model('MensajesMod');
		$datawhere = array('id' => $id);
		$arraydata = array('folder' => $folder);
		$data['mensajes'] = $this->MensajesMod->update_mensajefolder($arraydata,$datawhere);	
		$json = array('result' => FALSE);	
		if($data['mensajes']){
			$json = array('result' => TRUE);	
		}
		$this->output->set_content_type('application/json')->set_output(json_encode($json));
	}
}
