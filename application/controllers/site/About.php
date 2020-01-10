<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class About extends CI_Controller {
	public function index()
	{	
		$data['title'] = "Modern Business - Start Bootstrap Template";
		$this->load->view('home/head', $data);
		$this->load->view('home/navbar', $data);
		$this->load->view('home/home', $data);
		$this->load->view('home/footer', $data);
	}
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */