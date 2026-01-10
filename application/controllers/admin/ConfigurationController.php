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

    public function analytics()
    {
        $this->renderAdminView('admin.analytics.dashboard', 'Analytics Dashboard', 'Analytics');
    }

    public function export()
    {
        $this->renderAdminView('admin.configuration.export', 'Export', 'Export Data');
    }

    public function import()
    {
        $this->renderAdminView('admin.configuration.import', 'Import', 'Import Data');
    }
    
    /**
     * Toggle Debug Mode
     * POST /admin/config/toggle-debug
     */
    public function toggle_debug()
    {
        try {
            // Verificar permisos
            if (!has_permisions('UPDATE_CONFIG')) {
                throw new Exception('Sin permisos para cambiar configuración');
            }
            
            // Cargar modelo de configuración
            $this->load->model('site_config_model');
            
            // Obtener configuración actual de debug
            $debug_config = $this->site_config_model->where('config_key', 'DEBUG_MODE')->first();
            
            if ($debug_config) {
                // Toggle el valor
                $new_value = ($debug_config->config_value === 'true' || $debug_config->config_value === '1') ? '0' : '1';
                
                $this->site_config_model->update($debug_config->site_config_id, [
                    'config_value' => $new_value
                ]);
                
                $debug_enabled = ($new_value === '1');
            } else {
                // Si no existe, crear con valor true
                $this->site_config_model->insert([
                    'config_key' => 'DEBUG_MODE',
                    'config_value' => '1',
                    'config_description' => 'Debug mode enabled/disabled'
                ]);
                
                $debug_enabled = true;
            }
            
            // Log de la acción
            system_logger('site_config', 0, 'toggle_debug', 'Debug mode ' . ($debug_enabled ? 'activado' : 'desactivado'));
            
            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'debug_enabled' => $debug_enabled,
                    'message' => $debug_enabled ? 'Debug activado' : 'Debug desactivado'
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