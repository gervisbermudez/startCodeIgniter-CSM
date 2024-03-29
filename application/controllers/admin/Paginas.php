<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class Paginas extends MY_Controller
{

    public $routes_permisions = [
        "index" => [ 
            "patern" => '/admin\/paginas/',
            "required_permissions" => ["SELECT_PAGES"],
            "conditions" => [],
        ],
        "nueva" => [ 
            "patern" => '/admin\/paginas\/nueva/',
            "required_permissions" => ["CREATE_PAGE"],
            "conditions" => [],
        ],
        "editar" => [ 
            "patern" => '/admin\/paginas\/editar\/(\d+)/',
            "required_permissions" => ["UPDATE_PAGE"],
            "conditions" => ["check_self_permissions"],
        ],
    ];

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
        $data['h1'] = "";
        $pages = new Page();
        $data['paginas'] = $pages->all();
        $data['title'] = ADMIN_TITLE . " | Paginas";
        $data['header'] = $this->load->view('admin/header', $data, true);
        echo $this->blade->view("admin.pages.pages_list", $data);
    }

    public function nueva()
    {
        $data['title'] = ADMIN_TITLE . " | Nueva Pagina";
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
            $data['title'] = ADMIN_TITLE . " | Editar";
            $data['h1'] = "Editar Pagina";
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

    public function view($page_id)
    {
        $page = new Page();
        if ($page->find($page_id)) {
            $data['title'] = ADMIN_TITLE . " | View";
            $data['h1'] = "View Page";
            $data['page_id'] = $page_id;
            $data['editMode'] = 'edit';

            $data['header'] = $this->load->view('admin/header', $data, true);
            $data['action'] = base_url('admin/paginas/save/');
            $data['pagina'] = array();
            $data['templates'] = [];
            echo $this->blade->view("admin.pages.view", $data);
        } else {
            $this->showError('Pagina no encontrada');
        }
    }

}
