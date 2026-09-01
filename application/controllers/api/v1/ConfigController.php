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

        $pages = array();
        $pageQuery = $this->db
            ->select('page_id, title, path, status')
            ->from('page')
            ->where('status', 1)
            ->order_by('page_id', 'ASC')
            ->get();
        if ($pageQuery && $pageQuery->num_rows() > 0) {
            foreach ($pageQuery->result() as $row) {
                $pages[] = $row;
            }
        }

        $config = array();
        $configQuery = $this->db
            ->select('site_config_id, config_name, config_label, config_type')
            ->from('site_config')
            ->where('status', 1)
            ->order_by('site_config_id', 'ASC')
            ->get();
        if ($configQuery && $configQuery->num_rows() > 0) {
            foreach ($configQuery->result() as $row) {
                $config[] = $row;
            }
        }

        $this->response_ok(array(
            'pages' => $pages,
            'config' => $config,
        ));
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

        $exportData = $this->input->post('exportData');
        $pageIds = $this->selected_id_list($exportData, 'pages');
        $configIds = $this->selected_id_list($exportData, 'config');

        if (empty($pageIds) && empty($configIds)) {
            $this->response_error(lang('config_export_empty'), array(), REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $pages = $this->build_pages_export($pageIds);
        $config = $this->build_config_export($configIds);
        if (empty($pages) && empty($config)) {
            $this->response_error(lang('config_export_empty'), array(), REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $payload = array(
            'version' => 1,
            'exported_at' => date('c'),
            'pages' => $pages,
            'config' => $config,
        );

        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if ($json === false) {
            $this->response_error(lang('config_error'), array(), REST_Controller::HTTP_INTERNAL_SERVER_ERROR, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            return;
        }

        $filename = 'export_data_' . date('Y-m-d_H-i-s') . '.json';
        system_logger(
            'config',
            0,
            'export',
            'Export generated: ' . count($payload['pages']) . ' pages, ' . count($payload['config']) . ' config'
        );

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

        $selection = json_decode($this->input->post('exportData'));
        $selectedPageIds = $this->selected_id_list($selection, 'pages');
        $selectedConfigIds = $this->selected_id_list($selection, 'config');

        if (empty($selectedPageIds) && empty($selectedConfigIds)) {
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
            $importedPages = 0;
            $importedConfig = 0;

            if (isset($file_content->pages) && is_array($file_content->pages)) {
                $this->load->model('Admin/PageModel');
                foreach ($file_content->pages as $value) {
                    if (!is_object($value) || !$this->is_selected_id(isset($value->page_id) ? $value->page_id : 0, $selectedPageIds)) {
                        continue;
                    }
                    if (!$this->import_page_row($value)) {
                        $this->db->trans_rollback();
                        $this->response_error(lang('config_error'), array(), REST_Controller::HTTP_INTERNAL_SERVER_ERROR, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                        return;
                    }
                    $importedPages++;
                }
            }

            if (isset($file_content->config) && is_array($file_content->config)) {
                foreach ($file_content->config as $value) {
                    if (!is_object($value) || !$this->is_selected_id(isset($value->site_config_id) ? $value->site_config_id : 0, $selectedConfigIds)) {
                        continue;
                    }
                    if (!$this->import_config_row($value)) {
                        $this->db->trans_rollback();
                        $this->response_error(lang('config_error'), array(), REST_Controller::HTTP_INTERNAL_SERVER_ERROR, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                        return;
                    }
                    $importedConfig++;
                }
            }

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                $this->response_error(lang('config_error'), array(), REST_Controller::HTTP_INTERNAL_SERVER_ERROR, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                return;
            }

            $this->db->trans_commit();

            if ($importedConfig > 0) {
                invalidate_site_config_cache();
            }
            if ($importedPages > 0 || $importedConfig > 0) {
                invalidate_public_html_cache();
            }

            system_logger(
                'config',
                0,
                'import',
                'Import applied: ' . $importedPages . ' pages, ' . $importedConfig . ' config'
            );

            $this->response_ok(array(
                'message' => lang('config_import_ok'),
                'pages' => $importedPages,
                'config' => $importedConfig,
            ));
        } catch (\Throwable $th) {
            $this->db->trans_rollback();
            $this->response_error(lang('config_error'), array(), REST_Controller::HTTP_INTERNAL_SERVER_ERROR, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            return;
        }
    }

    /**
     * @param mixed $values
     * @return int[]
     */
    private function normalize_id_list($values)
    {
        if (!is_array($values)) {
            if ($values === null || $values === '' || $values === false) {
                return array();
            }
            $values = array($values);
        }
        $ids = array();
        foreach ($values as $value) {
            $id = (int) $value;
            if ($id > 0) {
                $ids[$id] = $id;
            }
        }
        return array_values($ids);
    }

    /**
     * @param mixed $selection
     * @param string $key
     * @return int[]
     */
    private function selected_id_list($selection, $key)
    {
        if (is_object($selection) && isset($selection->{$key})) {
            return $this->normalize_id_list($selection->{$key});
        }
        if (is_array($selection) && isset($selection[$key])) {
            return $this->normalize_id_list($selection[$key]);
        }
        return array();
    }

    /**
     * @param mixed $id
     * @param int[] $selectedIds
     * @return bool
     */
    private function is_selected_id($id, $selectedIds)
    {
        if (empty($selectedIds)) {
            return false;
        }
        return in_array((int) $id, $selectedIds, true);
    }

    /**
     * @param object|array $row
     * @param string[] $fields
     * @return array
     */
    private function pick_fields($row, $fields)
    {
        $out = array();
        foreach ($fields as $field) {
            if (is_object($row) && property_exists($row, $field)) {
                $out[$field] = $row->{$field};
            } elseif (is_array($row) && array_key_exists($field, $row)) {
                $out[$field] = $row[$field];
            }
        }
        return $out;
    }

    /**
     * @param mixed $value
     * @return string|null
     */
    private function encode_json_field($value)
    {
        if (is_array($value) || is_object($value)) {
            return json_encode($value);
        }
        return $value;
    }

    /**
     * @param int[] $pageIds
     * @return array
     */
    private function build_pages_export($pageIds)
    {
        $fields = array(
            'page_id',
            'path',
            'template',
            'title',
            'subtitle',
            'content',
            'json_content',
            'page_type_id',
            'visibility',
            'categorie_id',
            'subcategorie_id',
            'status',
            'layout',
            'mainImage',
            'thumbnailImage',
            'date_publish',
        );
        if (empty($pageIds)) {
            return array();
        }
        $this->db->reset_query();
        $this->db->select(implode(', ', $fields));
        $this->db->from('page');
        $this->db->where('status', 1);
        $this->db->where_in('page_id', $pageIds);
        $this->db->order_by('page_id', 'ASC');
        $query = $this->db->get();
        if (!$query || $query->num_rows() < 1) {
            return array();
        }
        $rows = $query->result();
        $exportedIds = array();
        foreach ($rows as $row) {
            $exportedIds[] = (int) $row->page_id;
        }
        $dataByPage = $this->load_page_data_map($exportedIds);
        $pages = array();
        foreach ($rows as $row) {
            $item = $this->pick_fields($row, $fields);
            $pid = (int) $row->page_id;
            $item['page_data'] = isset($dataByPage[$pid]) ? $dataByPage[$pid] : array();
            $pages[] = $item;
        }
        return $pages;
    }

    /**
     * @param int[] $configIds
     * @return array
     */
    private function build_config_export($configIds)
    {
        $fields = array(
            'site_config_id',
            'config_name',
            'config_value',
            'config_description',
            'config_label',
            'config_type',
            'config_data',
            'readonly',
            'status',
        );
        if (empty($configIds)) {
            return array();
        }
        $this->db->reset_query();
        $this->db->select(implode(', ', $fields));
        $this->db->from('site_config');
        $this->db->where('status', 1);
        $this->db->where_in('site_config_id', $configIds);
        $this->db->order_by('site_config_id', 'ASC');
        $query = $this->db->get();
        if (!$query || $query->num_rows() < 1) {
            return array();
        }
        $config = array();
        foreach ($query->result() as $row) {
            $config[] = $this->pick_fields($row, $fields);
        }
        return $config;
    }

    /**
     * @param int[] $pageIds
     * @return array
     */
    private function load_page_data_map($pageIds)
    {
        $map = array();
        if (empty($pageIds)) {
            return $map;
        }
        $this->db->reset_query();
        $this->db->where_in('page_id', $pageIds);
        $query = $this->db->get('page_data');
        if (!$query || $query->num_rows() < 1) {
            return $map;
        }
        foreach ($query->result() as $row) {
            $pid = (int) $row->page_id;
            if (!isset($map[$pid])) {
                $map[$pid] = array();
            }
            $decoded = json_decode($row->_value);
            if (is_object($decoded) || is_array($decoded)) {
                $map[$pid][$row->_key] = $decoded;
            } else {
                $map[$pid][$row->_key] = $row->_value;
            }
        }
        return $map;
    }

    /**
     * Skip hasOne/computed hydration (user, files) during import upsert.
     *
     * @param object $model
     * @return object
     */
    private function without_import_relations($model)
    {
        $model->hasOne = array();
        $model->hasMany = array();
        $model->computed = array();
        return $model;
    }

    /**
     * @param object $value
     * @return bool
     */
    private function import_page_row($value)
    {
        $page = $this->without_import_relations(new PageModel());
        $found = false;
        $path = isset($value->path) ? trim((string) $value->path) : '';
        if ($path !== '') {
            $found = $page->find_with(array('path' => $path));
        }
        if (!$found) {
            $page = $this->without_import_relations(new PageModel());
            $page->user_id = userdata('user_id');
            $page->status = 1;
        }

        $fields = array(
            'path',
            'template',
            'title',
            'subtitle',
            'content',
            'page_type_id',
            'visibility',
            'categorie_id',
            'subcategorie_id',
            'status',
            'layout',
            'mainImage',
            'thumbnailImage',
            'date_publish',
        );
        $this->apply_allowlist($page, $value, $fields);
        if (isset($value->json_content)) {
            $page->json_content = $this->encode_json_field($value->json_content);
        }
        if (isset($value->page_data)) {
            $pageData = json_decode(json_encode($value->page_data), true);
            $page->page_data = is_array($pageData) ? $pageData : array();
        }
        return (bool) $page->save();
    }

    /**
     * @param object $value
     * @return bool
     */
    private function import_config_row($value)
    {
        $config = $this->without_import_relations(new SiteConfigModel());
        $found = false;
        $name = isset($value->config_name) ? trim((string) $value->config_name) : '';
        if ($name !== '') {
            $found = $config->find_with(array('config_name' => $name));
        }
        if (!$found) {
            $config = $this->without_import_relations(new SiteConfigModel());
            $config->user_id = userdata('user_id');
            $config->status = 1;
        }

        $fields = array(
            'config_name',
            'config_value',
            'config_description',
            'config_label',
            'config_type',
            'readonly',
            'status',
        );
        $this->apply_allowlist($config, $value, $fields);
        if (isset($value->config_data)) {
            $config->config_data = $this->encode_json_field($value->config_data);
        }
        return (bool) $config->save();
    }

    /**
     * @param object $model
     * @param object $source
     * @param string[] $fields
     * @return void
     */
    private function apply_allowlist($model, $source, $fields)
    {
        foreach ($fields as $field) {
            if (is_object($source) && property_exists($source, $field)) {
                $model->{$field} = $source->{$field};
            }
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