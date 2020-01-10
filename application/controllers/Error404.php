<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Error404 extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{

		$data['base_url'] = $this->config->base_url();
		$data['title'] = "404";
		$url = uri_string();
		if (stristr($url, 'admin') === FALSE) {
			$this->load->view('site/head', $data);
			$this->load->view('site/navbar', $data);
			$this->load->view('error404', $data);
			$this->load->view('site/footer', $data);
		}else{
			if (!$this->session->userdata('logged_in')) {
				$uri = str_replace('/','_',uri_string());
				redirect('login/index/'.$uri);
			}else{
				$data['h1'] = "404 Page not found :(";
				$data['header'] = $this->load->view('admin/header', $data, TRUE);
				$this->load->view('admin/head', $data);
				$this->load->view('admin/navbar', $data);
				$this->load->view('admin/template', $data);
				$this->load->view('admin/footer', $data);
			}
		}

	}
	
}