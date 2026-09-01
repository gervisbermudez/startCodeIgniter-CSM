<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require APPPATH . 'libraries/REST_Controller.php';

class ConfigController extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->output->enable_profiler(false);
        $this->lang->load('rest_lang', 'english');

        if (!$this->verify_request()) {
            $this->response([
                'code' => REST_Controller::HTTP_UNAUTHORIZED,
            ], REST_Controller::HTTP_UNAUTHORIZED);
            exit();
        }

        $this->load->database();
        $this->load->helper('general');
        $this->load->model('Admin/SiteConfigModel');
        $this->lang->load('admin/common', 'english');
    }

    /**
     * @api {get} /api/v1/configuration/:configuration_id Request configuration information
     * @apiName Getconfiguration
     * @apiGroup configuration
     *
     * @apiParam {Number} configuration_id configuration unique ID.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *   {
     *       "code": 200,
     *       "data": [
     *           {
     *               "configuration_id": "4",
     *               "name": "Categoria 1",
     *               "description": "Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!",
     *               "type": "page",
     *               "parent_id": "0",
     *               "date_publish": "2020-04-19 10:36:10",
     *               "date_create": "2020-04-19 10:36:14",
     *               "date_update": "2020-04-19 10:40:20",
     *               "status": "1"
     *           },
     *           {
     *               "configuration_id": "5",
     *               "name": "Categoria 2",
     *               "description": "Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!",
     *               "type": "page",
     *               "parent_id": "0",
     *               "date_publish": "2020-04-19 10:36:10",
     *               "date_create": "2020-04-19 10:36:14",
     *               "date_update": "2020-04-19 10:40:28",
     *               "status": "1"
     *           },
     *       ]
     *   }
     *
     * @apiError configurationNotFound The id of the User was not found.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     * {
     *     "code": 404,
     *     "error_message": "Resource not found",
     *     "data": []
     * }
     */
    public function index_get($site_config_id = null)
    {
        if (!$this->require_config_permision('SELECT_CONFIG')) {
            return;
        }

        $SiteConfig = new SiteConfigModel();
        if ($site_config_id) {
            $result = $SiteConfig->where(["site_config_id" => $site_config_id]);
            $result = $result ? $result->first() : [];
        } else {
            $result = $SiteConfig->all();
        }

        if ($result) {
            $this->response_ok($result);
            return;
        }

        $this->response_error(lang('not_found_error'));
    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function index_post()
    {
        $site_config_id = $this->input->post('site_config_id');
        $is_update = ($site_config_id !== null && $site_config_id !== '' && $site_config_id !== false);
        if (!$this->require_config_permision($is_update ? 'UPDATE_CONFIG' : 'CREATE_CONFIG')) {
            return;
        }

        $configuration = new SiteConfigModel();
        $this->input->post('site_config_id') ? $configuration->find($this->input->post('site_config_id')) : false;
        $configuration->config_name = $this->input->post('config_name');
        $configuration->config_value = $this->input->post('config_value');
        $configuration->config_description = $this->input->post('config_description');
        $configuration->config_label = $this->input->post('config_label');
        $configuration->config_data = json_encode($this->input->post('config_data'));
        $configuration->readonly = $this->input->post('readonly');
        $configuration->config_type = $this->input->post('config_type');
        $configuration->user_id = userdata('user_id');
        $configuration->status = $this->input->post('status');
        $configuration->date_create = date("Y-m-d H:i:s");

        if ($configuration->save()) {
            invalidate_site_config_cache();
            invalidate_public_html_cache();
            system_logger('config', $configuration->site_config_id, ($this->input->post('site_config_id') ? "updated" : "created"), "Configuración " . $configuration->config_name . " fue " . ($this->input->post('site_config_id') ? "actualizada" : "creada"));
            $this->response_ok($configuration);
            return;
        }

        $this->response_error(lang('unexpected_error'), REST_Controller::HTTP_BAD_REQUEST);
    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function index_put($id)
    {
        $data = array();
        $this->response($data, REST_Controller::HTTP_NOT_FOUND);
    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function index_delete($id = null)
    {
        $data = array();
        $this->response($data, REST_Controller::HTTP_NOT_FOUND);
    }

    /**
     * Backup Your Database
     * @return Response
     */
    public function backup_database_get()
    {
        if (!$this->require_config_permision('SELECT_CONFIG')) {
            return;
        }

        try {
            // Load the DB utility class
            $this->load->dbutil();
            
            // Define backup directory
            $backup_dir = './backups/database/';
            
            // Check if directory exists, if not create it
            if (!file_exists($backup_dir)) {
                if (!@mkdir($backup_dir, 0777, true)) {
                    set_notification(
                        lang('notification_backup_fail_title'),
                        lang('notification_backup_fail_desc'),
                        'system_error',
                        'admin/configuration/data'
                    );
                    $this->response([
                        'result' => 'No se pudo crear el directorio de backups. Verifica los permisos.',
                        'code' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,
                        'path' => realpath('./backups')
                    ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                    return;
                }
                @chmod($backup_dir, 0777);
            }
            
            // Check if directory is writable
            if (!is_writable($backup_dir)) {
                set_notification(
                    lang('notification_backup_fail_title'),
                    lang('notification_backup_fail_desc'),
                    'system_error',
                    'admin/configuration/data'
                );
                $this->response([
                    'result' => 'El directorio de backups no tiene permisos de escritura.',
                    'code' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,
                    'path' => realpath($backup_dir)
                ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                return;
            }
            
            // Backup your entire database
            $backup = $this->dbutil->backup();
            
            // Load the file helper and write the file
            $this->load->helper('file');
            $filename = $backup_dir . date('YmdHis') . '.gz';
            
            if (write_file($filename, $backup)) {
                // Log the successful backup
                system_logger('config', 'Backup de base de datos creado exitosamente', [
                    'filename' => basename($filename),
                    'size' => filesize($filename)
                ]);
                
                $this->response([
                    'result' => 'Backup creado exitosamente',
                    'code' => REST_Controller::HTTP_OK,
                    'filename' => basename($filename),
                    'size' => filesize($filename)
                ], REST_Controller::HTTP_OK);
            } else {
                set_notification(
                    lang('notification_backup_fail_title'),
                    lang('notification_backup_fail_desc'),
                    'system_error',
                    'admin/configuration/data'
                );
                $this->response([
                    'result' => 'No se pudo escribir el archivo de backup',
                    'code' => REST_Controller::HTTP_BAD_REQUEST
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            system_logger('error', 'Error al crear backup de base de datos', [
                'error' => $e->getMessage()
            ]);
            set_notification(
                lang('notification_backup_fail_title'),
                $e->getMessage(),
                'system_error',
                'admin/configuration/data'
            );
            
            $this->response([
                'result' => 'Error al crear el backup: ' . $e->getMessage(),
                'code' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function themes_get()
    {
        if (!$this->require_config_permision('SELECT_CONFIG')) {
            return;
        }

        $this->load->helper('directory');
        $map = directory_map('./themes/');

        $response = [];

        foreach ($map as $key => $value) {
            if (is_array($value)) {
                $folder_name = substr($key, 0, -1);
                $folder = str_replace('\\', '/', $key);
                $file_path = FCPATH . 'themes/' . $folder . "theme_info.json";
                if (file_exists($file_path)) {
                    $string = file_get_contents($file_path);
                    $json_a = json_decode($string, true);
                    $response[$folder_name] = $json_a;
                }
            }
        }

        $response = array(
            'code' => REST_Controller::HTTP_OK,
            'data' => $response,
        );

        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function check_update_get()
    {
        if (!$this->require_config_permision('SELECT_CONFIG')) {
            return;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://raw.githubusercontent.com/gervisbermudez/startCodeIgniter-CSM/master/startcms_info.json');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);

        $string = file_get_contents("./startcms_info.json");
        $local_info = json_decode($string, true);
        $remote_info = json_decode($data, true);

        if ($remote_info) {

            $this->load->model('Admin/SiteConfigModel');
            $config = new SiteConfigModel();

            $config->find_with(['config_name' => 'UPDATER_LAST_CHECK_DATA']);
            $config->config_value = $data;
            $config->save();

            $config = new SiteConfigModel();
            $config->find_with(['config_name' => 'UPDATER_LAST_CHECK_UPDATE']);
            $config->config_value = date("Y-m-d H:i:s");
            $config->save();
            invalidate_site_config_cache();

            $response = array(
                'data' => [
                    "local" => $local_info,
                    "remote" => $remote_info,
                ],
                "code" => REST_Controller::HTTP_OK,
            );
        } else {
            $response = array(
                'data' => ["message" => "unnable to check updates"],
                "code" => REST_Controller::HTTP_BAD_REQUEST,
            );
        }

        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function download_update_get()
    {
        if (!$this->require_config_permision('UPDATE_CONFIG')) {
            return;
        }

        $filename = "./temp/startCodeIgniter-CSM-master-" . date("Ymd") . ".zip";
        $result = file_put_contents($filename, fopen(ADMIN_GIT_MASTERZIP_URL, 'r'));
        if ($result && file_exists($filename)) {
            $response = array(
                'data' => [
                    "result" => $result,
                    "downloaded_file" => $filename,
                    "message" => "Package downloaded successfully!",
                ],
                "code" => REST_Controller::HTTP_OK,
            );
        } else {
            $response = array(
                'data' => ["message" => "Unnable to download the package"],
                "code" => REST_Controller::HTTP_BAD_REQUEST,
            );
        }

        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function download_install_theme_post()
    {
        if (!$this->require_config_permision('UPDATE_CONFIG')) {
            return;
        }

        $response = array(
            'data' => ["message" => "Unnable to download the package"],
            "code" => REST_Controller::HTTP_BAD_REQUEST,
        );

        $filenamePath = "./temp/startCodeIgniter-CSM-theme-" . date("Ymd");
        $filename = $filenamePath . ".zip";
        $url = $this->input->post('theme_url');
        $result = file_put_contents($filename, fopen($url, 'r'));
        if ($result && file_exists($filename)) {
            $zip = new ZipArchive;
            $extractResult = false;
            if ($zip->open($filename) === true) {
                $zip->extractTo('./themes');
                $zip->close();
                $extractResult = true;
                if ($extractResult) {
                    unlink($filename);
                    $response = array(
                        'data' => [
                            "result" => $result,
                            "downloaded_file" => $filename,
                            "message" => "Package downloaded successfully!",
                        ],
                        "code" => REST_Controller::HTTP_OK,
                    );
                }
            }
        }

        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function install_downloaded_update_get()
    {
        if (!$this->require_config_permision('UPDATE_CONFIG')) {
            return;
        }

        /* $filename = $this->input->get('packagename');
    if (file_exists($filename)) {
    $ignorefiles = ['.', '..', 'config.php', 'database.php'];
    recurse_copy($source, $destination, $ignorefiles);
    $response = array(
    'data' => [
    "result" => $result,
    "downloaded_file" => $filename,
    "message" => "Package installed successfully!",
    ],
    "code" => REST_Controller::HTTP_OK,
    );
    } else {
    $response = array(
    'data' => ["message" => "Unnable to install the package"],
    "code" => REST_Controller::HTTP_BAD_REQUEST,
    );
    }

    $this->response($response, REST_Controller::HTTP_OK); */
    }

    public function systemlogger_get($logger_id = null)
    {
        if (!$this->require_config_permision('SELECT_CONFIG')) {
            return;
        }

        $this->load->model('Admin/LoggerModel');

        $Logger = new LoggerModel();
        if ($logger_id) {
            $result = $Logger->where(["logger_id" => $logger_id]);
            $result = $result ? $result->first() : [];
            if ($result) {
                $this->response_ok($result);
                return;
            }
            $this->response_error(lang('not_found_error'));
            return;
        }

        $this->respond_collection($Logger->pager(), $Logger);
    }

    public function apilogger_get($api_log_id = null)
    {
        if (!$this->require_config_permision('SELECT_CONFIG')) {
            return;
        }

        $this->load->model('Admin/ApiLogsModel');

        $Api_logs = new ApiLogsModel();
        if ($api_log_id) {
            $result = $Api_logs->where(["api_log_id" => $api_log_id]);
            $result = $result ? $result->first() : [];
            if ($result) {
                $this->response_ok($result);
                return;
            }
            $this->response_error(lang('not_found_error'));
            return;
        }

        $this->respond_collection($Api_logs->pager(), $Api_logs);
    }

    public function usertrackinglogger_get($user_tracking_id = null)
    {
        if (!$this->require_config_permision('SELECT_CONFIG')) {
            return;
        }

        $this->load->model('Admin/UserTrackingModel');

        $User_tracking = new UserTrackingModel();
        if ($user_tracking_id) {
            $result = $User_tracking->where(["user_tracking_id" => $user_tracking_id]);
            $result = $result ? $result->first() : [];
            if ($result) {
                $this->response_ok($result);
                return;
            }
            $this->response_error(lang('not_found_error'));
            return;
        }

        $this->respond_collection($User_tracking->pager(), $User_tracking);
    }

    public function export_data_get()
    {
        if (!$this->require_config_permision('SELECT_CONFIG')) {
            return;
        }

        $this->load->library('content_export');
        $unpublished = $this->input->get('unpublished_pages');
        $this->response_ok($this->content_export->catalog(!empty($unpublished) && $unpublished !== '0'));
    }

    public function system_info_get()
    {
        if (!$this->require_config_permision('SELECT_CONFIG')) {
            return;
        }

        $this->load->helper('number');
        
        $info = [
            'php_version' => PHP_VERSION,
            'db_driver' => $this->db->platform(),
            'db_version' => $this->db->version(),
            'server_os' => PHP_OS,
            'max_upload' => ini_get('upload_max_filesize'),
            'max_post' => ini_get('post_max_size'),
            'memory_limit' => ini_get('memory_limit'),
            'disk_free' => byte_format(disk_free_space(".")),
            'disk_total' => byte_format(disk_total_space(".")),
            'disk_usage_pct' => round((1 - (disk_free_space(".") / disk_total_space("."))) * 100, 2),
            'server_time' => date('Y-m-d H:i:s'),
        ];

        $this->response_ok($info);
    }

    /**
     * Run maintenance tasks: Cleanup old logs
     */
    public function cleanup_logs_post()
    {
        if (!$this->require_config_permision('UPDATE_CONFIG')) {
            return;
        }

        // Check if auto cleanup is enabled
        $auto_cleanup = config('AUTO_CLEANUP_ENABLED');
        if ($auto_cleanup != '1' && $auto_cleanup != 'Si' && $auto_cleanup != 'On') {
            return $this->response([
                'code' => 400,
                'error_message' => 'La limpieza automática está desactivada.'
            ], 400);
        }

        $this->load->model('Admin/LoggerModel');
        $this->load->model('Admin/ApiLogsModel');
        $this->load->model('Admin/UserTrackingModel');

        $results = [
            'system_logs' => 0,
            'api_logs' => 0,
            'user_tracking' => 0
        ];

        // 1. System Logs
        $retention_logger = (int)config('LOGGER_RETENTION_DAYS');
        if ($retention_logger > 0) {
            $date = date('Y-m-d H:i:s', strtotime("-$retention_logger days"));
            $this->db->where('date_create <', $date);
            $this->db->delete('logger');
            $results['system_logs'] = $this->db->affected_rows();
        }

        // 2. API Logs
        $retention_api = (int)config('API_LOGS_RETENTION_DAYS');
        if ($retention_api > 0) {
            $date = date('Y-m-d H:i:s', strtotime("-$retention_api days"));
            $this->db->where('date_create <', $date);
            $this->db->delete('api_logs');
            $results['api_logs'] = $this->db->affected_rows();
        }

        // 3. User Tracking (SEO)
        $retention_tracking = (int)config('USER_TRACKING_RETENTION_DAYS');
        if ($retention_tracking > 0) {
            $date = date('Y-m-d H:i:s', strtotime("-$retention_tracking days"));
            $this->db->where('date_create <', $date);
            $this->db->delete('user_tracking');
            $results['user_tracking'] = $this->db->affected_rows();
        }

        $this->response_ok($results);
    }

    public function generate_export_file_post()
    {
        if (!$this->require_config_permision('UPDATE_CONFIG')) {
            return;
        }

        $this->load->library('content_export');
        $exportData = $this->input->post('exportData', false);
        if (is_string($exportData)) {
            $decoded = json_decode($exportData);
            if (is_object($decoded) || is_array($decoded)) {
                $exportData = $decoded;
            }
        }

        if ($this->content_export->selection_is_empty($exportData)) {
            $this->response_error(lang('config_export_empty'), array(), REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $payload = $this->content_export->dump($exportData);
        if ($this->content_export->payload_is_empty($payload)) {
            $this->response_error(lang('config_export_empty'), array(), REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if ($json === false) {
            $this->response_error(lang('config_error'), array(), REST_Controller::HTTP_INTERNAL_SERVER_ERROR, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            return;
        }

        $filename = 'export_data_' . date('Y-m-d_H-i-s') . '.json';
        system_logger('config', 0, 'export', 'Export generated: ' . $this->content_export->summarize_payload($payload));

        $this->response_ok(array(
            'filename' => $filename,
            'exportJson' => $json,
        ));
    }

    public function import_file_post()
    {
        if (!$this->require_config_permision('UPDATE_CONFIG')) {
            return;
        }

        $this->load->library('content_export');
        $selection = json_decode($this->input->post('exportData', false));
        if ($this->content_export->selection_is_empty($selection)) {
            $this->response_error(lang('config_import_empty'), array(), REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        if (empty($_FILES['import_file']['tmp_name']) || !is_uploaded_file($_FILES['import_file']['tmp_name'])) {
            $this->response_error(lang('config_import_invalid'), array(), REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $maxBytes = 8 * 1024 * 1024;
        if (!empty($_FILES['import_file']['size']) && (int) $_FILES['import_file']['size'] > $maxBytes) {
            $this->response_error(lang('config_import_invalid'), array(), REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $raw = file_get_contents($_FILES['import_file']['tmp_name']);
        if ($raw === false || $raw === '') {
            $this->response_error(lang('config_import_invalid'), array(), REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $file_content = json_decode($raw);
        if (!is_object($file_content)) {
            $this->response_error(lang('config_import_invalid'), array(), REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $this->db->trans_begin();
        try {
            $counts = $this->content_export->import_payload($file_content, $selection);
            if ($counts === false || $this->db->trans_status() === false) {
                $this->db->trans_rollback();
                $this->response_error(lang('config_error'), array(), REST_Controller::HTTP_INTERNAL_SERVER_ERROR, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                return;
            }

            $this->db->trans_commit();

            if (!empty($counts['config'])) {
                invalidate_site_config_cache();
            }
            $contentImported = 0;
            foreach ($counts as $n) {
                $contentImported += (int) $n;
            }
            if ($contentImported > 0) {
                invalidate_public_html_cache();
            }

            system_logger('config', 0, 'import', 'Import applied: ' . $this->content_export->summarize_counts($counts));

            $ok = array('message' => lang('config_import_ok'));
            foreach ($counts as $key => $n) {
                $ok[$key] = $n;
            }
            $this->response_ok($ok);
        } catch (\Throwable $th) {
            $this->db->trans_rollback();
            $this->response_error(lang('config_error'), array(), REST_Controller::HTTP_INTERNAL_SERVER_ERROR, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            return;
        }
    }

    protected function require_config_permision($permision)
    {
        if (!function_exists('has_permisions') || !has_permisions($permision)) {
            $this->response_error('You do not have permission to perform this action', array(), REST_Controller::HTTP_FORBIDDEN, REST_Controller::HTTP_FORBIDDEN);
            return false;
        }
        return true;
    }
}