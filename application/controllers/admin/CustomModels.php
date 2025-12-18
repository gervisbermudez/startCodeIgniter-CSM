<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class CustomModels extends MY_Controller
{
    public $routes_permisions = [
        "index" => [
            "patern" => '/admin\/custommodels/',
            "required_permissions" => ["SELECT_FORM_CUSTOMS"],
            "conditions" => [],
        ],
        "nuevo" => [
            "patern" => '/admin\/custommodels\/nuevo/',
            "required_permissions" => ["CREATE_FORM_CUSTOM"],
            "conditions" => [],
        ],
        "editForm" => [
            "patern" => '/admin\/custommodels\/editForm\/(\d+)/',
            "required_permissions" => ["UPDATE_FORM_CUSTOM"],
            "conditions" => ["check_self_permissions"],
        ],
        "content" => [
            "patern" => '/admin\/custommodels\/content/',
            "required_permissions" => ["SELECT_CONTENT_DATA"],
            "conditions" => ["check_self_permissions"],
        ],
        "addData" => [
            "patern" => '/admin\/custommodels\/addData\/(\d+)/',
            "required_permissions" => ["CREATE_CONTENT_DATA"],
            "conditions" => ["check_self_permissions"],
        ],
        "editData" => [
            "patern" => '/admin\/custommodels\/editData\/(\d+)\/(\d+)/',
            "required_permissions" => ["UPDATE_CONTENT_DATA"],
            "conditions" => ["check_self_permissions"],
        ],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->check_permisions();
        $this->load->model('Admin/CustomModel');
    }

    public function index()
    {
        $data = $this->prepareAdminData('Models', 'Models');
        echo $this->blade->view("admin.custommodels.list", $data);
    }

    public function nuevo()
    {
        $data = $this->prepareAdminData('Modelos', 'New Model');
        echo $this->blade->view("admin.custommodels.form", $data);
    }

    public function editForm($custom_model_id)
    {
        $this->renderAdminView('admin.custommodels.form', 'Modelo', 'Edit Model', [
            'custom_model_id' => $custom_model_id
        ]);
    }

    public function content()
    {
        $this->renderAdminView('admin.custommodels.content_list', 'Model Contents', 'Model Contents');
    }

    public function addData($custom_model_id)
    {
        $this->renderAdminView('admin.custommodels.content_form', 'Modelo', 'Modelos', [
            'custom_model_id' => $custom_model_id,
            'custom_model_content_id' => false,
            'editMode' => false
        ]);
    }

    public function editData($custom_model_id, $custom_model_content_id)
    {
        $data['base_url'] = $this->config->base_url();
        $data['title'] = ADMIN_TITLE . " | Modelo";
        $data['h1'] = "Modelos";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['custom_model_id'] = $custom_model_id;
        $data['custom_model_content_id'] = $custom_model_content_id;
        $data['editMode'] = true;
        echo $this->blade->view("admin.custommodels.content_form", $data);
    }
}
