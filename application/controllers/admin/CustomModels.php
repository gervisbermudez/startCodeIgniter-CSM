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
        $this->load->model('Admin/Form_custom');
    }

    public function index()
    {
        $data['title'] = ADMIN_TITLE . " | Models";
        $data['h1'] = "Models";
        echo $this->blade->view("admin.custommodels.list", $data);
    }

    public function nuevo()
    {
        $data['base_url'] = $this->config->base_url();
        $data['title'] = ADMIN_TITLE . " | Modelos";
        $data['h1'] = "New Model";
        echo $this->blade->view("admin.custommodels.form", $data);
    }

    public function editForm($form_custom_id)
    {
        $data['base_url'] = $this->config->base_url();
        $data['title'] = ADMIN_TITLE . " | Modelo";
        $data['h1'] = "Edit Model";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['username'] = userdata('username');
        $data['form_custom_id'] = $form_custom_id;
        echo $this->blade->view("admin.custommodels.form", $data);
    }

    public function content()
    {
        $data['base_url'] = $this->config->base_url();
        $data['title'] = ADMIN_TITLE . " | Model Contents";
        $data['h1'] = "Model Contents";
        $data['header'] = $this->load->view('admin/header', $data, true);
        echo $this->blade->view("admin.custommodels.content_list", $data);
    }

    public function addData($form_custom_id)
    {
        $data['base_url'] = $this->config->base_url();
        $data['title'] = ADMIN_TITLE . " | Modelo";
        $data['h1'] = "Modelos";
        $data['header'] = $this->load->view('admin/header', $data, true);

        $data['form_custom_id'] = $form_custom_id;
        $data['form_content_id'] = false;
        $data['editMode'] = false;

        echo $this->blade->view("admin.custommodels.content_form", $data);
    }

    public function editData($form_custom_id, $form_content_id)
    {
        $data['base_url'] = $this->config->base_url();
        $data['title'] = ADMIN_TITLE . " | Modelo";
        $data['h1'] = "Modelos";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['form_custom_id'] = $form_custom_id;
        $data['form_content_id'] = $form_content_id;
        $data['editMode'] = true;
        echo $this->blade->view("admin.custommodels.content_form", $data);
    }
}
