<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Configuration Controller
 */
class ConfigurationController extends MY_Controller
{

    public $routes_permisions = [
        "index" => [
            "patern" => '/^admin\/configuration\/?$/',
            "required_permissions" => ["SELECT_CONFIG"],
            "conditions" => [],
        ],
        "new" => [
            "patern" => '/^admin\/configuration\/new/',
            "required_permissions" => ["CREATE_CONFIG"],
            "conditions" => [],
        ],
        "data" => [
            "patern" => '/^admin\/configuration\/data/',
            "required_permissions" => ["SELECT_CONFIG"],
            "conditions" => [],
        ],
        "logs" => [
            "patern" => '/^admin\/configuration\/logs/',
            "required_permissions" => ["SELECT_CONFIG"],
            "conditions" => [],
        ],
        "import" => [
            "patern" => '/^admin\/configuration\/import/',
            "required_permissions" => ["SELECT_CONFIG"],
            "conditions" => [],
        ],
        "export" => [
            "patern" => '/^admin\/configuration\/export/',
            "required_permissions" => ["SELECT_CONFIG"],
            "conditions" => [],
        ],
        "logger" => [
            "patern" => '/^admin\/configuration\/logger/',
            "required_permissions" => ["SELECT_CONFIG"],
            "conditions" => [],
        ],
        "apilogger" => [
            "patern" => '/^admin\/configuration\/apilogger/',
            "required_permissions" => ["SELECT_CONFIG"],
            "conditions" => [],
        ],
        "usertrackinglogger" => [
            "patern" => '/^admin\/configuration\/usertrackinglogger/',
            "required_permissions" => ["SELECT_CONFIG"],
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
        $this->renderAdminView('admin.configuration.all_config', lang('menu_configuration'), lang('menu_configuration'));
    }

    function new () {
        $this->renderAdminView('admin.configuration.new_form', lang('menu_configuration'), lang('config_add_entry'), [
            'site_config_id' => '',
            'editMode' => 'new'
        ]);
    }

    public function data()
    {
        $this->renderAdminView('admin.configuration.data', lang('menu_data'), lang('menu_data'));
    }

    public function logs()
    {
        $this->renderAdminView('admin.configuration.logs', lang('menu_logs'), lang('menu_logs'));
    }

    public function logger()
    {
        redirect('admin/configuration/logs?tab=system');
    }

    public function apilogger()
    {
        redirect('admin/configuration/logs?tab=api');
    }

    public function usertrackinglogger()
    {
        redirect('admin/configuration/logs?tab=tracking');
    }

    public function export()
    {
        redirect('admin/configuration/data?section=export');
    }

    public function import()
    {
        redirect('admin/configuration/data?section=import');
    }

    /**
     * Toggle Debug Mode
     * POST /admin/config/toggle-debug
     */
    public function toggle_debug()
    {
        try {
            if (!has_permisions('UPDATE_CONFIG')) {
                throw new Exception(lang('not_have_permissions'));
            }

            $this->load->model('Admin/SiteConfigModel');
            if (!class_exists('Site_config_catalog', false)) {
                require_once APPPATH . 'libraries/Site_config_catalog.php';
            }
            $configuration = new SiteConfigModel();
            $found = $configuration->find_with(array('config_name' => 'DEBUG_MODE'));
            if (!$found) {
                $configuration->sync_catalog();
                $found = $configuration->find_with(array('config_name' => 'DEBUG_MODE'));
            }

            $current = $found ? $found->config_value : '0';
            $debug_enabled = !Site_config_catalog::value_is_on($current);
            $new_value = $debug_enabled ? '1' : '0';

            if ($found) {
                $this->db->where('site_config_id', $found->site_config_id);
                $this->db->update('site_config', array('config_value' => $new_value));
            }

            invalidate_site_config_cache();
            system_logger('config', $found ? $found->site_config_id : 0, 'toggle_debug', 'Debug mode ' . ($debug_enabled ? 'on' : 'off'));

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'debug_enabled' => $debug_enabled,
                    'message' => $debug_enabled ? lang('config_debug_on') : lang('config_debug_off')
                ]));

        } catch (Exception $e) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]));
        }
    }
}
