<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class MenusController extends MY_Controller
{
    public $routes_permisions = [
        "index" => [ 
            "patern" => '/admin\/menus/',
            "required_permissions" => ["SELECT_MENUS"],
            "conditions" => [],
        ],
        "nuevo" => [ 
            "patern" => '/admin\/menus\/nuevo/',
            "required_permissions" => ["CREATE_MENU"],
            "conditions" => [],
        ],
        "editForm" => [ 
            "patern" => '/admin\/menus\/editForm\/(\d+)/',
            "required_permissions" => ["UPDATE_MENU"],
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
        $this->renderAdminView('admin.menu.menu_list', 'Menus', 'Todas los Menus');
    }

    public function nuevo()
    {
        $this->renderAdminView('admin.menu.new_form', 'Menu', 'Nuevo Menu', [
            'menu_id' => '',
            'editMode' => 'new'
        ]);
    }

    public function editar($menu_id)
    {
        $this->renderAdminView('admin.menu.new_form', 'Menu', 'Editar Menu', [
            'menu_id' => $menu_id,
            'editMode' => 'edit'
        ]);
    }

}
