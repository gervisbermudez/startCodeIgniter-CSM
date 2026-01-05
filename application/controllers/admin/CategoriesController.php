<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class CategoriesController extends MY_Controller
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
        $this->load->model('Admin/CategorieModel');
    }

    public function index()
    {
        $this->renderAdminView('admin.categories.categories_list', lang('menu_categories'), lang('categories_all'));
    }

    public function nueva()
    {
        $this->renderAdminView('admin.categories.new_form', lang('menu_categories'), lang('categories_new'), [
            'categorie_id' => '',
            'editMode' => 'new'
        ]);
    }

    public function editar($categorie_id)
    {
        $this->renderAdminView('admin.categories.new_form', lang('menu_categories'), lang('categories_edit'), [
            'categorie_id' => $categorie_id,
            'editMode' => 'edit'
        ]);
    }

}
