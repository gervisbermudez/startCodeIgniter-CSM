<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Formularios extends MY_Controller
{
    public $routes_permisions = [
        "index" => [ 
            "patern" => '/admin\/formularios/',
            "required_permissions" => ["SELECT_FORM_CUSTOMS"],
            "conditions" => [],
        ],
        "nuevo" => [ 
            "patern" => '/admin\/formularios\/nuevo/',
            "required_permissions" => ["CREATE_FORM_CUSTOM"],
            "conditions" => [],
        ],
        "editForm" => [ 
            "patern" => '/admin\/formularios\/editForm\/(\d+)/',
            "required_permissions" => ["UPDATE_FORM_CUSTOM"],
            "conditions" => ["check_self_permissions"],
        ],
        "content" => [ 
            "patern" => '/admin\/formularios\/content/',
            "required_permissions" => ["SELECT_CONTENT_DATA"],
            "conditions" => ["check_self_permissions"],
        ],
        "addData" => [ 
            "patern" => '/admin\/formularios\/addData\/(\d+)/',
            "required_permissions" => ["CREATE_CONTENT_DATA"],
            "conditions" => ["check_self_permissions"],
        ],
        "editData" => [ 
            "patern" => '/admin\/formularios\/editData\/(\d+)\/(\d+)/',
            "required_permissions" => ["UPDATE_CONTENT_DATA"],
            "conditions" => ["check_self_permissions"],
        ],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin/Form_custom');
    }

    public function index()
    {
        $data['title'] = ADMIN_TITLE . " | Formularios";
        $data['h1'] = "Formularios";
        echo $this->blade->view("admin.formularios.formularios_list", $data);
    }

    public function nuevo()
    {
        $data['base_url'] = $this->config->base_url();
        $data['title'] = ADMIN_TITLE . " | Formularios";
        $data['h1'] = "Nuevo formulario";
        echo $this->blade->view("admin.formularios.new", $data);
    }

    public function editForm($form_custom_id)
    {
        $data['base_url'] = $this->config->base_url();
        $data['title'] = ADMIN_TITLE . " | Formularios";
        $data['h1'] = "Formularios";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['username'] = userdata('username');
        $data['form_custom_id'] = $form_custom_id;
        echo $this->blade->view("admin.formularios.new", $data);
    }

    public function content()
    {
        $data['base_url'] = $this->config->base_url();
        $data['title'] = ADMIN_TITLE . " | Content";
        $data['h1'] = "Content";
        $data['header'] = $this->load->view('admin/header', $data, true);
        echo $this->blade->view("admin.formularios.content_list", $data);
    }

    public function addData($form_custom_id)
    {
        $data['base_url'] = $this->config->base_url();
        $data['title'] = ADMIN_TITLE . " | Formularios";
        $data['h1'] = "Formularios";
        $data['header'] = $this->load->view('admin/header', $data, true);

        $data['form_custom_id'] = $form_custom_id;
        $data['form_content_id'] = false;
        $data['editMode'] = false;

        echo $this->blade->view("admin.formularios.formContent", $data);
    }

    public function editData($form_custom_id, $form_content_id)
    {
        $data['base_url'] = $this->config->base_url();
        $data['title'] = ADMIN_TITLE . " | Formularios";
        $data['h1'] = "Formularios";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['form_custom_id'] = $form_custom_id;
        $data['form_content_id'] = $form_content_id;
        $data['editMode'] = true;
        echo $this->blade->view("admin.formularios.formContent", $data);
    }
}
