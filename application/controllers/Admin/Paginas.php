<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class Paginas extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
		$this->load->model('PageModel');
		$this->load->model('Admin/Page');
    }

    /**
     * Index page for admin/paginas
     *
     * @return void
     */
    public function index()
    {
        $data['h1'] = "Todas las Paginas";
		$pages = new Page();
		$data['paginas'] = $pages->all();
        $data['title'] = "Admin | Paginas";
        $data['header'] = $this->load->view('admin/header', $data, true);
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
            $data['header'] = $this->load->view('admin/header', $data, true);
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
        $this->load->helper('directory');

        $data['title'] = "Admin | Nueva Paginas";
        $data['h1'] = "Nueva Pagina";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['action'] = base_url('admin/paginas/save/');
        $data['pagina'] = array();
        $data['templates'] = [];

        echo $this->blade->view("admin.pages.new", $data);
    }

    public function ajax_save_page()
    {
		$this->output->enable_profiler(false);
		
        $page = new Page();
        $this->input->post('page_id') ? $page->find($this->input->post('page_id')) : false;
		$page->title = $this->input->post('title');
		$page->subtitle = $this->input->post('subtitle');
        $page->path = $this->input->post('path');
		$page->content = $this->input->post('content');
		$page->author = userdata('user_id');
		$page->status = $this->input->post('status');
		$page->template = $this->input->post('template');
		$page->date_publish = !$this->input->post('publishondate') ? $this->input->post('date_publish') : null;
		$page->visibility = $this->input->post('visibility');
		$page->categorie = $this->input->post('categorie');
		$page->subcategories = $this->input->post('subcategories');


        if ($page->save()) {
            $response = array(
                'code' => 200,
                'data' => $page,
            );

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } else {

            $error_message = 'Ha ocurrido un error inesperado';
            $response = array(
                'code' => 500,
                'error_message' => $error_message,
                'data' => $_POST,
            );

            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

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
            'status' => $status,
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
            'tinymceinit' => "<script>tinymce.init({ selector:'textarea',  plugins : ['link table code'] });</script>",
        );

        echo $this->blade->view("admin.pages.new", $data);
    }
}
