<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->load->model('MensajesMod');
		$this->load->model('EventosMod');
		$this->load->model('ModGallery');
		$this->load->helper('array');
		$this->load->helper('text');

		$data['albumes'] = $albumes = $this->ModGallery->get_album('all','2',array('fecha','DESC'));
		$data['eventos'] = $this->EventosMod->get_event(array(), 3, array('publishdate', 'DESC'));
		$data['count_mensajes'] = $this->MensajesMod->count_mensajes();
		$data['mensajes'] = $this->MensajesMod->get_mensaje(array('estatus' => 'No leido'), '');
		$data['username'] = $this->session->userdata('username');
		$data['title'] = "Admin";
		$data['h1'] = "";
		$data['header'] = "";

		$this->load->view('admin/head', $data);
		$this->load->view('admin/navbar', $data);
		$this->load->view('admin/bodypage', $data);
		$this->load->view('admin/footer', $data);
		
	}

}
