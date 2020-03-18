<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class Paginas extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('PageModel');
	}

	/**
	 * Index page for admin/paginas
	 *
	 * @return void
	 */
	public function index()
	{
		$data['h1'] = "Todas las Paginas";
		$data['paginas'] = $this->PageModel->all();
		$data['title'] = "Admin | Paginas";
		$data['header'] = $this->load->view('admin/header', $data, TRUE);
		echo $this->blade->view("admin.pages.pages_list", $data);
	}

	public function filter($str_tipo = 'all')
	{
		$data['h1'] = "Todas las Categorias";
		if ($str_tipo == 'all') {
			$data['categorias'] = $this->StModel->get_data('all', 'categorias');
		} else {
			$data['categorias'] = $this->StModel->get_data(array('tipo' => $str_tipo), 'categorias');
			$data['h1'] = "Categoria " . $str_tipo;
		}
		if ($data['categorias']) {
			$data['title'] = "Admin | Paginas";
			$data['header'] = $this->load->view('admin/header', $data, TRUE);
			$this->load->view('admin/head', $data);
			$this->load->view('admin/navbar', $data);
			$this->load->view('admin/categorias/categorias_list', $data);
			$this->load->view('admin/footer', $data);
		} else {
			$this->showError('Tipo de categoria no encontrada :(');
		}
	}
	public function nueva()
	{
		$this->load->helper('array');

		$data['title'] = "Admin | Paginas";
		$data['h1'] = "Nueva Pagina";
		$data['header'] = $this->load->view('admin/header', $data, TRUE);
		$data['action'] = base_url('admin/paginas/save/');
		$data['pagina'] = array();

		$data['footer_includes'] = array(
			'tinymce' => '<script src="' . base_url('public/js/tinymce/js/tinymce/tinymce.min.js') . '"></script>',
			'tinymceinit' => "<script>tinymce.init({ 
				selector:'textarea',  
				plugins : ['link table code']});</script>"
		);
		echo $this->blade->view("admin.pages.new", $data);
	}

	public function save()
	{
		$data['title'] = "Admin | Paginas";
		$data['h1'] = "Paginas";
		$data['header'] = $this->load->view('admin/header', $data, TRUE);
		$data['paginas'] = $this->PageModel->get_data('all', 'page');
		$status = 0;
		if ($this->input->post('status_form') === '1') {
			$status = 1;
		}
		$fecha = new DateTime;

		$data = array(
			'id' => 'null',
			'path' => $this->input->post('path'),
			'title' => $this->input->post('title'),
			'content' => $this->input->post('content'),
			'author' => $this->session->userdata('id'),
			'date_create' => $fecha->format('Y-m-d H:i:s'),
			'status' => $status
		);

		if ($this->PageModel->set_data($data, 'page')) {
			$this->index();
		} else {
			$this->showError();
		}
	}

	public function update()
	{
		$status = 0;
		if ($this->input->post('status_form') === '1') {
			$status = 1;
		}

		$where = array('id' => $this->input->post('id_form'));
		$data = array(
			'path' => $this->input->post('path'),
			'title' => $this->input->post('title'),
			'content' => $this->input->post('content'),
			'author' => $this->session->userdata('id'),
			'status' => $status
		);

		if ($this->PageModel->update_data($where, $data, 'page')) {
			$this->index();
		} else {
			$this->showError();
		}
	}

	public function editar($int_id)
	{
		$this->load->helper('array');

		$data['title'] = "Admin | Paginas";
		$data['h1'] = "Editar Pagina";
		$data['action'] = base_url('admin/paginas/update/');
		$data['pagina'] = $this->PageModel->get_data(array('id' => $int_id), 'page')[0];

		$data['footer_includes'] = array(
			'tinymce' => '<script src="' . base_url('public/js/tinymce/js/tinymce/tinymce.min.js') . '"></script>',
			'tinymceinit' => "<script>tinymce.init({ selector:'textarea',  plugins : ['link table code'] });</script>"
		);

		echo $this->blade->view("admin.pages.new", $data);
	}
}
