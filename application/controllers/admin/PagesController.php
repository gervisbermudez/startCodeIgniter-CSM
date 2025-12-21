<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class PagesController extends MY_Controller
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
        $this->load->model('Admin/PageModel');
    }

    /**
     * Index page for admin/pages
     *
     * @return void
     */
    public function index()
    {
        $pages = new PageModel();
        $this->renderAdminView('admin.pages.pages_list', lang('menu_pages'), lang('pages_all'), [
            'paginas' => $pages->all()
        ]);
    }

    public function nueva()
    {
        $this->renderAdminView('admin.pages.new', lang('pages_new'), lang('pages_new'), [
            'action' => base_url('admin/pages/save/'),
            'templates' => [],
            'page_id' => '',
            'editMode' => 'new'
        ]);
    }

    public function editar($page_id)
    {
        $page = $this->findOrFail(new PageModel(), $page_id, lang('pages_not_found'));
        
        $this->renderAdminView('admin.pages.new', lang('pages_edit'), lang('pages_edit'), [
            'page_id' => $page_id,
            'editMode' => 'edit',
            'action' => base_url('admin/pages/save/'),
            'pagina' => [],
            'templates' => []
        ]);
    }

    public function view($page_id)
    {
        $page = $this->findOrFail(new PageModel(), $page_id, lang('pages_not_found'));
        
        $this->renderAdminView('admin.pages.view', lang('btn_preview'), lang('btn_preview'), [
            'page_id' => $page_id,
            'editMode' => 'edit',
            'action' => base_url('admin/pages/save/'),
            'pagina' => [],
            'templates' => []
        ]);
    }

}
