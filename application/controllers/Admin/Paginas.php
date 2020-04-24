<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class Paginas extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin/Page');
    }

    /**
     * Index page for admin/paginas
     *
     * @return void
     */
    public function index()
    {
        $this->output->enable_profiler(true);

        $data['h1'] = "Todas las Paginas";
        $pages = new Page();
        $data['paginas'] = $pages->all();
        $data['title'] = "Admin | Paginas";
        $data['header'] = $this->load->view('admin/header', $data, true);
        echo $this->blade->view("admin.pages.pages_list", $data);
    }

    public function nueva()
    {

        $data['title'] = "Admin | Nueva Paginas";
        $data['h1'] = "Nueva Pagina";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['action'] = base_url('admin/paginas/save/');
        $data['templates'] = [];
        $data['page_id'] = '';
        $data['editMode'] = 'new';

        echo $this->blade->view("admin.pages.new", $data);
    }

    public function editar($page_id)
    {
        $page = new Page();
        if ($page->find($page_id)) {
            $data['title'] = "Admin | Nueva Paginas";
            $data['h1'] = "Nueva Pagina";
            $data['page_id'] = $page_id;
            $data['editMode'] = 'edit';

            $data['header'] = $this->load->view('admin/header', $data, true);
            $data['action'] = base_url('admin/paginas/save/');
            $data['pagina'] = array();
            $data['templates'] = [];
            echo $this->blade->view("admin.pages.new", $data);
        } else {
            $this->showError('Pagina no encontrada');
        }
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
        $page->type = $this->input->post('type');
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

    public function ajax_get_pages()
    {
        $this->output->enable_profiler(false);
        $page = new Page();
        $pages = $page->all();
        $response = array(
            'code' => 200,
            'data' => $pages ? $pages : [],
        );
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function ajax_get_templates()
    {
        $this->output->enable_profiler(false);
        $this->load->helper('directory');
        $map = directory_map('./application/views/site/layouts', 1);
        $response = array(
            'code' => 200,
            'data' => $map ? $map : [],
        );
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function ajax_get_page()
    {
        $this->output->enable_profiler(false);
        $page = new Page();
        if ($page->find($this->input->post('page_id'))) {
            $response = array(
                'code' => 200,
                'data' => $page,
            );
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        } else {
            $error_message = 'Pagina no encontrada';
            $response = array(
                'code' => 404,
                'error_message' => $error_message,
                'data' => $_POST,
            );
            $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        }

    }

    public function ajax_delete_page()
    {
        $this->output->enable_profiler(false);
        $page = new Page();
        $page->find($this->input->post('page_id'));
        if ($page->delete()) {
            $response = array(
                'code' => 200,
                'data' => $page,
            );
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        } else {
            $error_message = 'Pagina no encontrada';
            $response = array(
                'code' => 404,
                'error_message' => $error_message,
                'data' => $_POST,
            );
            $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        }

    }

    public function ajax_get_page_types()
    {
        $this->output->enable_profiler(false);
        $this->load->model('Admin/Page_types');

        $page_types = new Page_types();
        $all_pages = $page_types->all();
        $response = array(
            'code' => 200,
            'data' => $all_pages ? $all_pages : [],
        );
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));

    }

}
