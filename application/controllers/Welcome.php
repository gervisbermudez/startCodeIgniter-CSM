<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function index()
	{	
		$data['title'] = "Modern Business - Start Bootstrap Template";
		$this->load->view('site/head', $data);
		$this->load->view('site/navbar', $data);
		$this->load->view('site/home', $data);
		$this->load->view('site/footer', $data);
	}
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */