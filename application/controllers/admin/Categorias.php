<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Categorias extends MY_Controller
{

    public $routes_permisions = [
        "index" => [ 
            "patern" => '/admin\/categorias/',
            "required_permissions" => ["SELECT_CATEGORIES"],
            "conditions" => [],
        ],
        "nueva" => [ 
            "patern" => '/admin\/categorias\/nueva/',
            "required_permissions" => ["CREATE_CATEGORIE"],
            "conditions" => [],
        ],
        "editar" => [ 
            "patern" => '/admin\/categorias\/editar\/(\d+)/',
            "required_permissions" => ["UPDATE_CATEGORIE"],
            "conditions" => ["check_self_permissions"],
        ],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->check_permisions();
        $this->load->model('Admin/Categories');
    }

    public function index()
    {
        $data['h1'] = "Todas las Categorias";
        $data['title'] = ADMIN_TITLE . " | Categorias";
        $data['header'] = $this->load->view('admin/header', $data, true);
        echo $this->blade->view("admin.categorias.categorias_list", $data);

    }

    public function nueva()
    {
        $data['title'] = ADMIN_TITLE . " | Categorias";
        $data['h1'] = "Nueva Categoria";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['categorie_id'] = '';
        $data['editMode'] = 'new';
        echo $this->blade->view("admin.categorias.new_form", $data);
    }

    public function editar($categorie_id)
    {
        $data['title'] = ADMIN_TITLE . " | Categorias";
        $data['h1'] = "Editar Categoria";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['categorie_id'] = $categorie_id;
        $data['editMode'] = 'edit';
        echo $this->blade->view("admin.categorias.new_form", $data);
    }

}
