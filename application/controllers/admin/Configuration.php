<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Configuration Controller
 */
class Configuration extends MY_Controller
{

    public $routes_permisions = [
        "index" => [
            "patern" => '/admin\/configuracion/',
            "required_permissions" => ["SELECT_CONFIG"],
            "conditions" => [],
        ],
        "new" => [
            "patern" => '/admin\/configuracion\/new/',
            "required_permissions" => ["CREATE_CONFIG"],
            "conditions" => [],
        ],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->check_permisions();

    }

    public function index()
    {
        $data['title'] = ADMIN_TITLE . " | Configuracion";
        $data['h1'] = "Configuracion";
        $data['header'] = $this->load->view('admin/header', $data, true);

        echo $this->blade->view("admin.configuracion.all_config", $data);
    }

    function new () {
        $data['title'] = ADMIN_TITLE . " | Configuracion";
        $data['h1'] = "Configuracion";
        $data['site_config_id'] = '';
        $data['editMode'] = 'new';
        $data['header'] = $this->load->view('admin/header', $data, true);
        echo $this->blade->view("admin.configuracion.new_form", $data);
    }

    public function logger()
    {
        $data['title'] = ADMIN_TITLE . " | System Log";
        $data['h1'] = "Logger";
        $data['header'] = $this->load->view('admin/header', $data, true);

        echo $this->blade->view("admin.configuracion.all_logger", $data);
    }

    public function apilogger()
    {
        $data['title'] = ADMIN_TITLE . " | API Log";
        $data['h1'] = "API Log";
        $data['header'] = $this->load->view('admin/header', $data, true);

        echo $this->blade->view("admin.configuracion.all_apilogger", $data);
    }

    public function usertrackinglogger()
    {
        $data['title'] = ADMIN_TITLE . " | User Tracking Log";
        $data['h1'] = "User Tracking Log";
        $data['header'] = $this->load->view('admin/header', $data, true);

        echo $this->blade->view("admin.configuracion.all_usertrackinglogger", $data);
    }

    public function export()
    {
        $data['title'] = ADMIN_TITLE . " | Export";
        $data['h1'] = "Export Data";
        $data['header'] = $this->load->view('admin/header', $data, true);
        echo $this->blade->view("admin.configuracion.export", $data);
    }

    public function import()
    {
        $data['title'] = ADMIN_TITLE . " | Import";
        $data['h1'] = "Import Data";
        $data['header'] = $this->load->view('admin/header', $data, true);
        echo $this->blade->view("admin.configuracion.import", $data);
    }
}