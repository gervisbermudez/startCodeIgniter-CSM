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
        $this->renderAdminView('admin.configuration.all_config', 'Configuracion', 'Configuracion');
    }

    function new () {
        $this->renderAdminView('admin.configuration.new_form', 'Configuracion', 'Configuracion', [
            'site_config_id' => '',
            'editMode' => 'new'
        ]);
    }

    public function logger()
    {
        $this->renderAdminView('admin.configuration.all_logger', 'System Log', 'Logger');
    }

    public function apilogger()
    {
        $this->renderAdminView('admin.configuration.all_apilogger', 'API Log', 'API Log');
    }

    public function usertrackinglogger()
    {
        $this->renderAdminView('admin.configuration.all_usertrackinglogger', 'User Tracking Log', 'User Tracking Log');
    }

    public function export()
    {
        $this->renderAdminView('admin.configuration.export', 'Export', 'Export Data');
    }

    public function import()
    {
        $this->renderAdminView('admin.configuration.import', 'Import', 'Import Data');
    }
}