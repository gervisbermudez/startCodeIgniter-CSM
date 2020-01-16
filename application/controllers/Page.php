<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->load->model('PageModel');
		$pageInfo = $this->PageModel->get_data(array('path' => $this->uri->uri_string(), 'status' => 1), 'page');
		if($pageInfo){
				$data['page'] = $pageInfo[0];
				$data['title'] = $pageInfo[0]['title'];
				echo $this->blade->view("site.template", $data);
		}else{
			$this->error404();
		}
	}
	
}