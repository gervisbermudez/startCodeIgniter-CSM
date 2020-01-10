<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	public function index($continue='')
	{
		$this->session->sess_destroy();
		$data['title'] = "Login";
		if ($continue!=='') {
			$data['continue'] =str_replace('_','/',$continue);
		}
		$data['base_url'] = $this->config->base_url();
		$this->load->view('admin/head', $data);
		$this->load->view('admin/login', $data);
		$this->load->view('admin/footer', $data);
	}

	public function validar()
	{
		if ($this->input->post('username')) {	
			$this->load->model('LoginMod');
			$password = $this->input->post('password');
			$username = $this->input->post('username');
			$isLoged = $this->LoginMod->isLoged($username, $password);
			if ($isLoged) {
				if ($this->input->post('continue')) {
					redirect($this->input->post('continue'));
				}else{
					redirect('admin');
				}
			}else{
				$data['title'] = "Login";
				$data['base_url'] = $this->config->base_url();
				$data['error'] = "Combinacion Password / Username Invalido";
				$this->load->view('admin/head', $data);
				$this->load->view('admin/login', $data);
				$this->load->view('admin/footer', $data);
			}
		}else{
			$this->index();
		}
	}
	public function register()
	{
		$this->load->model('UserMod');
		if ($this->UserMod->get_user()){
			$this->index();
		}else{
			$data['base_url'] = $this->config->base_url();
			$this->load->helper('form');
			$data['action'] = 'Login/registernew';
			$data['title'] = "Admin | Nuevo Usuario";
			$data['h1'] = "Nuevo Usuario";
			$data['header'] = $this->load->view('admin/header', $data, TRUE);
			$data['userdata'] = false;
			$this->load->model('UserMod');
			$data['usergroups'] = $this->UserMod->get_usergroup(array('level ='=>'1'));
			// Load the views
			
			$data['mode'] = 'register';
			$this->load->view('admin/head', $data);
			$this->load->view('admin/user/register', $data);
			$this->load->view('admin/footer', $data);
		}
	}
	public function registernew()
	{	
		$this->load->model('UserMod');
		if (!$this->UserMod->get_user()){
		
			$status = "0";
			if($this->input->post('status')=="on"){
				$status = "1";
			}
			$data = array(
				'username' => $this->input->post('username') ,
				'password' => $this->input->post('password'),
				'email' => $this->input->post('email'),
				'usergroup' =>  $this->input->post('usergroup'),
				'status' =>  $status
			);
			$date = new DateTime();
			$data['lastseen'] = $date->format('Y-m-d H:i:s');
			$id_user = $this->UserMod->set_user($data);
			if ($id_user) {
				$datauserstorage = array(
				array('_key' => 'nombre', '_value' => $this->input->post('nombre'), 'id_user' => $id_user),
				array('_key' => 'apellido', '_value' =>  $this->input->post('apellido'), 'id_user' => $id_user),
				array('_key' => 'direccion', '_value' => $this->input->post('direccion'), 'id_user' => $id_user),
				array('_key' => 'telefono',  '_value'=>$this->input->post('telefono'), 'id_user' => $id_user),
				array('_key' => 'create by',  '_value'=>$this->session->userdata('username'), 'id_user' => $id_user)
				);
			foreach ($datauserstorage as $key => $data) {
				$this->UserMod->set_datauserstorage($data);
			}
			redirect('Login/');
			}else{
				echo "Ha ocurrido un error :(";
			}
		}else{
			redirect('Login/');
		}
	}
}