<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Suscriptores extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	
	public function index()
	{
		$data['base_url'] = $this->config->base_url();
		$data['username'] = $this->session->userdata('username');
		$data['title'] = "Admin | Suscriptores";
		$data['h1'] = "Suscritos";
		$data['header'] = $this->load->view('admin/header', $data, TRUE);
		$this->load->model('SuscriptoresMod');
		$data['Suscriptores'] = $this->SuscriptoresMod->get_all_suscriptores();

		$this->load->view('admin/head', $data);
		$this->load->view('admin/navbar', $data);
		$this->load->view('admin/suscriptores/todos', $data);
		$this->load->view('admin/footer', $data);
	}

	public function suscribir()
	{
		$data['base_url'] = $this->config->base_url();
		if($this->input->post('email')){
			$this->load->model('SuscriptoresMod');
			if(!$this->SuscriptoresMod->get_existMail($this->input->post('email'))){
				if (!$this->SuscriptoresMod->setSuscriptorData()) {
					
					$this->load->library('email');
					$this->email->set_mailtype("html");
					 $this->email->set_newline("\r\n");
					$this->email->from('info@promovida.com.ve', 'Promovida');
					$this->email->to($this->input->post('email'));
					$this->email->cc('');
					$this->email->bcc('');
					$this->email->subject('Suscripcion: Verificar email');
					$code = $this->SuscriptoresMod->getCode($this->input->post('email'));
					$data['nombre'] = $this->input->post('nombre');
					$data['codigo'] = $code[0]['codigo'];
					$message = $this->load->view('email/email', $data, TRUE);
					$this->email->message($message);
					$this->email->send();
										
					$data['eventos'] = false;
					$data['eventos_pasados'] = false;
					// Cargar vistas ->
					// 
					$data['navbar'] = $this->load->view('home/navbar_interno', $data, TRUE);

					$data['title'] = "Promovida Producciones";
					$data['titulo']  = 'Suscripción';

					$data['content'] = "Suscrito correctamente! Te enviamos un correo para que verifiques tu email. <a href=".$data['base_url'].">Volver</a>";
					$data['header_img'] = 'descarga.png';
					$data['link_all_events'] = false;
					$data['mensaje'] = "Suscrito correctamente";
					
					$data['sections'] = array('header' =>  $this->load->view('home/header_interno', $data, TRUE),
						'section_blank' =>  $this->load->view('home/section_blank', $data, TRUE));
					$data['footer'] = $this->load->view('home/footer', $data, TRUE);
					
					$this->load->view('home/head', $data);
					$this->load->view('home/cuerpo', $data);
				
				}else{
					echo "Ocurrio un error";
				}
			}else{
				
					$data['base_url'] = $this->config->base_url();
					$this->load->model('EventosMod');
					$data['eventos'] = false;
					$data['eventos_pasados'] = false;
					// Cargar vistas ->
					$data['navbar'] = $this->load->view('home/navbar_interno', $data, TRUE);
					$data['title'] = "Promovida Producciones";
					$data['titulo']  = 'Suscripción';
					$data['content'] = "Email ya suscrito! <a href=".$data['base_url'].">Volver</a>";
					$data['header_img'] = 'descarga.png';
					$data['link_all_events'] = false;
					$data['mensaje'] = "Suscrito correctamente";
					$data['sections'] = array('header' =>  $this->load->view('home/header_interno', $data, TRUE),
						'section_blank' =>  $this->load->view('home/section_blank', $data, TRUE));
					$data['footer'] = $this->load->view('home/footer', $data, TRUE);
					$this->load->view('home/head', $data);
					$this->load->view('home/cuerpo', $data);
					
				}
		}else{
					echo "Ningun dato recibido";
				}
	}
	public function Verificar($codigo='')
	{
		if($codigo){
				$this->load->model('SuscriptoresMod');
				if($this->SuscriptoresMod->update_state($codigo)){
					$data['base_url'] = $this->config->base_url();
					$data['eventos'] = false;
					$data['eventos_pasados'] = false;
					// Cargar vistas ->
					$data['navbar'] = $this->load->view('home/navbar_interno', $data, TRUE);
					$data['title'] = "Promovida Producciones";
					$data['titulo']  = 'Suscripción';
					$data['content'] = "Email verificado correctamente! <a href=".$data['base_url'].">Volver</a>";
					$data['header_img'] = 'descarga.png';
					$data['link_all_events'] = false;
					$data['sections'] = array('header' =>  $this->load->view('home/header_interno', $data, TRUE),
						'section_blank' =>  $this->load->view('home/section_blank', $data, TRUE));
					$data['footer'] = $this->load->view('home/footer', $data, TRUE);
					$this->load->view('home/head', $data);
					$this->load->view('home/cuerpo', $data);
				}else{
					$data['base_url'] = $this->config->base_url();
					
					$data['eventos'] = false;
					$data['eventos_pasados'] = false;
					// Cargar vistas ->
					$data['navbar'] = $this->load->view('home/navbar_interno', $data, TRUE);
					$data['title'] = "Promovida Producciones";
					$data['titulo']  = 'Suscripción';
					$data['content'] = "Este codigo no es valido <a href=".$data['base_url'].">Volver</a>";
					$data['header_img'] = 'descarga.png';
					$data['link_all_events'] = false;
					$data['sections'] = array('header' =>  $this->load->view('home/header_interno', $data, TRUE),
						'section_blank' =>  $this->load->view('home/section_blank', $data, TRUE));
					$data['footer'] = $this->load->view('home/footer', $data, TRUE);
					$this->load->view('home/head', $data);
					$this->load->view('home/cuerpo', $data);
				}
		}else{
					$data['base_url'] = $this->config->base_url();
					
					$data['eventos'] = false;
					$data['eventos_pasados'] = false;
					// Cargar vistas ->
					$data['navbar'] = $this->load->view('home/navbar_interno', $data, TRUE);
					$data['title'] = "Promovida Producciones";
					$data['titulo']  = 'Suscripción';
					$data['content'] = "Ocurrio un error! <a href=".$data['base_url'].">Volver</a>";
					$data['header_img'] = 'descarga.png';
					$data['link_all_events'] = false;
					$data['sections'] = array('header' =>  $this->load->view('home/header_interno', $data, TRUE),
						'section_blank' =>  $this->load->view('home/section_blank', $data, TRUE));
					$data['footer'] = $this->load->view('home/footer', $data, TRUE);
					$this->load->view('home/head', $data);
					$this->load->view('home/cuerpo', $data);
		}
	}
	public function unsuscribe($id = "")
	{
		$data['base_url'] = $this->config->base_url();
		if ($id == "") {
			header('Location: '.$data['base_url']);
		}else{
			$this->load->model('SuscriptoresMod');		
			if ($this->SuscriptoresMod->deleteSuscriptorData($id)) {
				$data['base_url'] = $this->config->base_url();
					// Cargar vistas ->
					$data['navbar'] = $this->load->view('home/navbar_interno', $data, TRUE);
					$data['title'] = "Promovida Producciones";
					$data['titulo']  = 'Suscripción';
					$data['content'] = "Unsuscrito correctamente! <a href=".$data['base_url'].">Volver</a>";
					$data['header_img'] = 'descarga.png';
					$data['link_all_events'] = false;
					$data['sections'] = array('header' =>  $this->load->view('home/header_interno', $data, TRUE),
						'section_blank' =>  $this->load->view('home/section_blank', $data, TRUE));
					$data['footer'] = $this->load->view('home/footer', $data, TRUE);
					$this->load->view('home/head', $data);
					$this->load->view('home/cuerpo', $data);
			}else{
				$data['base_url'] = $this->config->base_url();
					$this->load->model('EventosMod');
					$data['eventos'] = $this->EventosMod->get_events();
					$data['eventos_pasados'] = $this->EventosMod->get_eventsDefeated();
					// Cargar vistas ->
					$data['navbar'] = $this->load->view('home/navbar_interno', $data, TRUE);
					$data['title'] = "Promovida Producciones";
					$data['titulo']  = 'Suscripción';
					$data['content'] = "Ocurrio un error! <a href=".$data['base_url'].">Volver</a>";
					$data['header_img'] = 'descarga.png';
					$data['link_all_events'] = false;
					$data['sections'] = array('header' =>  $this->load->view('home/header_interno', $data, TRUE),
						'section_blank' =>  $this->load->view('home/section_blank', $data, TRUE));
					$data['footer'] = $this->load->view('home/footer', $data, TRUE);
					$this->load->view('home/head', $data);
					$this->load->view('home/cuerpo', $data);
			}
		}
	}

}