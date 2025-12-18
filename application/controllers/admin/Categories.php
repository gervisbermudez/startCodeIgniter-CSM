<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Categories extends MY_Controller
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
        $this->load->model('Admin/Categorie');
    }

    public function index()
    {
        $this->renderAdminView('admin.categorias.categorias_list', 'Categorias', 'Todas las Categorias');
    }

    public function nueva()
    {
        $this->renderAdminView('admin.categorias.new_form', 'Categorias', 'Nueva Categoria', [
            'categorie_id' => '',
            'editMode' => 'new'
        ]);
    }

    public function editar($categorie_id)
    {
        $this->renderAdminView('admin.categorias.new_form', 'Categorias', 'Editar Categoria', [
            'categorie_id' => $categorie_id,
            'editMode' => 'edit'
        ]);
    }

}
