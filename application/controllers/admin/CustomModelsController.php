<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class CustomModelsController extends MY_Controller
{
    public $routes_permisions = [
        "index" => [
            "patern" => '/admin\/custommodels/',
            "required_permissions" => ["SELECT_FORM_CUSTOMS"],
            "conditions" => [],
        ],
        "nuevo" => [
            "patern" => '/admin\/custommodels\/(nuevo|new)/',
            "required_permissions" => ["CREATE_FORM_CUSTOM"],
            "conditions" => [],
        ],
        "editForm" => [
            "patern" => '/admin\/custommodels\/(editForm|edit)\/(\d+)/',
            "required_permissions" => ["UPDATE_FORM_CUSTOM"],
            "conditions" => ["check_self_permissions"],
        ],
        "items" => [
            "patern" => '/admin\/custommodels\/items\/(\d+)/',
            "required_permissions" => ["SELECT_CONTENT_DATA"],
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
        $this->load->model('Admin/CustomModelModel');
    }

    public function index()
    {
        $data = $this->prepareAdminData(lang('menu_collections'), lang('menu_collections'));
        echo $this->blade->view("admin.custommodels.list", $data);
    }

    public function nuevo()
    {
        $data = $this->prepareAdminData(lang('collections_new'), lang('collections_new'));
        echo $this->blade->view("admin.custommodels.form", $data);
    }

    public function editForm($custom_model_id)
    {
        $this->renderAdminView('admin.custommodels.form', lang('menu_collections'), lang('collections_edit'), [
            'custom_model_id' => $custom_model_id
        ]);
    }

    public function items($custom_model_id)
    {
        $model = new CustomModelModel();
        if (!$model->find($custom_model_id) || (int) $model->status === 0) {
            $this->showError(lang('not_found_error'));
            return;
        }
        $heading = $model->form_name . ' — ' . lang('collections_items_heading');
        $this->renderAdminView('admin.custommodels.items', lang('menu_collections'), $heading, [
            'custom_model_id' => $custom_model_id,
            'collection_name' => $model->form_name,
            'items_count' => isset($model->items_count) ? (int) $model->items_count : 0,
        ]);
    }

    public function content()
    {
        redirect('admin/custommodels');
    }

    public function addData($custom_model_id)
    {
        $model = new CustomModelModel();
        $model->find($custom_model_id);
        $this->renderAdminView('admin.custommodels.content_form', lang('menu_collections'), lang('collections_item_new'), [
            'custom_model_id' => $custom_model_id,
            'custom_model_content_id' => false,
            'editMode' => false,
            'collection_name' => $model->form_name,
        ]);
    }

    public function editData($custom_model_id, $custom_model_content_id)
    {
        $model = new CustomModelModel();
        $model->find($custom_model_id);
        $this->renderAdminView('admin.custommodels.content_form', lang('menu_collections'), lang('collections_item_edit'), [
            'custom_model_id' => $custom_model_id,
            'custom_model_content_id' => $custom_model_content_id,
            'editMode' => true,
            'collection_name' => $model->form_name,
        ]);
    }
}
