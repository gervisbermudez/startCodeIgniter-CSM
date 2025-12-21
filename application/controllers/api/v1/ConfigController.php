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
        try {
            // Load the DB utility class
            $this->load->dbutil();
            
            // Define backup directory
            $backup_dir = './backups/database/';
            
            // Check if directory exists, if not create it
            if (!file_exists($backup_dir)) {
                if (!@mkdir($backup_dir, 0777, true)) {
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
                $this->response([
                    'result' => 'No se pudo escribir el archivo de backup',
                    'code' => REST_Controller::HTTP_BAD_REQUEST
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            system_logger('error', 'Error al crear backup de base de datos', [
                'error' => $e->getMessage()
            ]);
            
            $this->response([
                'result' => 'Error al crear el backup: ' . $e->getMessage(),
                'code' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function themes_get()
    {
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
        $this->load->model('Admin/LoggerModel');

        $Logger = new LoggerModel();
        if ($logger_id) {
            $result = $Logger->where(["site_config_id" => $logger_id]);
            $result = $result ? $result->first() : [];
        } else {
            $result = $Logger->pager();
        }

        if ($result) {
            $this->response_ok($result, $Logger->get_pagination_info());
            return;
        }

        $this->response_error(lang('not_found_error'), $Logger->get_pagination_info());
    }

    public function apilogger_get($api_log_id = null)
    {
        $this->load->model('Admin/ApiLogsModel');

        $Api_logs = new ApiLogsModel();
        if ($api_log_id) {
            $result = $Api_logs->where(["site_config_id" => $api_log_id]);
            $result = $result ? $result->first() : [];
        } else {
            $result = $Api_logs->pager();
        }

        if ($result) {
            $this->response_ok($result, $Api_logs->get_pagination_info());
            return;
        }

        $this->response_error(lang('not_found_error'), $Api_logs->get_pagination_info());
    }

    public function usertrackinglogger_get($api_log_id = null)
    {
        $this->load->model('Admin/UserTrackingModel');

        $User_tracking = new UserTrackingModel();
        if ($api_log_id) {
            $result = $User_tracking->where(["site_config_id" => $api_log_id]);
            $result = $result ? $result->first() : [];
        } else {
            $result = $User_tracking->pager();
        }

        if ($result) {
            $this->response_ok($result, $User_tracking->get_pagination_info());
            return;
        }

        $this->response_error(lang('not_found_error'), $User_tracking->get_pagination_info());
    }

    public function export_data_get()
    {
        $data = [];

        //Pages
        $this->load->model('Admin/PageModel');
        $pages = new PageModel();
        $data['pages'] = $pages->all();

        $config = new SiteConfigModel();
        $data['config'] = $config->all();

        $this->response_ok($data);
    }

    public function system_info_get()
    {
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

    private function getWhereStringFrom($arrayData, $id)
    {
        $whereString = "";
        foreach ($arrayData as $key => $value) {
            if (count($arrayData) === ($key + 1)) {
                $whereString .= $id . " = " . $value . "";
            } else {
                $whereString .= $id . " = " . $value . " OR ";
            }
        }
        return $whereString;
    }

    public function generate_export_file_post()
    {
        $exportData = $this->input->post("exportData");

        function removeUser($item)
        {
            unset($item->user);
            unset($item->imagen_file);

            return $item;
        }

        $data = [
            "pages" => [],
            "config" => [],
        ];

        if (isset($exportData["pages"])) {
            //Pages
            $this->load->model('Admin/PageModel');
            $pages = new PageModel();
            $data['pages'] = $pages->where($this->getWhereStringFrom($exportData["pages"], "page_id"));

            $data['pages'] = array_map("removeUser", $data['pages']->toArray());
        }

        if (isset($exportData["config"])) {
            $config = new SiteConfigModel();
            $data['config'] = $config->where($this->getWhereStringFrom($exportData["config"], "site_config_id"));
            $data['config'] = array_map("removeUser", $data['config']->toArray());
        }

        $json = json_encode($data);

        $exportFilename = "export_data_" . date("Y-m-d_H-i-s") . ".json";

        if (!file_exists("./temp/")) {
            @mkdir("./temp/");
        }

        if (file_put_contents("./temp/" . $exportFilename, $json)) {
            $this->response_ok([
                "message" => "JSON file created successfully",
                "exportFilename" => $exportFilename,
            ]);
        } else {
            $this->response_error([
                "message" => "Oops! Error creating json file",
            ]);
        }
    }

    public function import_file_post()
    {
        $exportData = json_decode($this->input->post('exportData'));

        if (!move_uploaded_file($_FILES["import_file"]["tmp_name"], "./uploads/" . $_FILES["import_file"]["name"])) {
            $this->response_error([
                "message" => "Oops! Error reading json file",
            ]);
            return;
        }

        $string = file_get_contents("./uploads/" . $_FILES["import_file"]["name"]);

        if ($string === false) {
            $this->response_error([
                "message" => "Oops! Error reading json file",
            ]);
            return;
        }

        try {
            $file_content = json_decode($string);
            if (isset($file_content->pages) && is_array($file_content->pages)) {
                $this->load->model('Admin/PageModel');
                $file_content->pages = array_filter($file_content->pages, function ($page) use ($exportData) {
                    return in_array($page->page_id, $exportData->pages);
                });
                foreach ($file_content->pages as $key => $value) {
                    $page = new PageModel();
                    //field already exist in database, so let's update it
                    $page->find($value->page_id);
                    foreach ($value as $index => $val) {
                        $page->{$index} = $val;
                    }
                    $page->save();
                }
            }

            if (isset($file_content->config) && is_array($file_content->config)) {

                $file_content->config = array_filter($file_content->config, function ($Site_config) use ($exportData) {
                    return in_array($SiteConfig->site_config_id, $exportData->config);
                });
                foreach ($file_content->config as $key => $value) {
                    $SiteConfig = new SiteConfigModel();
                    //field already exist in database, so let's update it
                    $SiteConfig->find($value->site_config_id);
                    foreach ($value as $index => $val) {
                        $SiteConfig->{$index} = $val;
                    }
                    $SiteConfig->save();
                }
            }

            $this->response_ok(
                [
                    "message" => "JSON file created successfully",
                ],
                ["file_content" => $file_content]
            );
        } catch (\Throwable $th) {
            $this->response_error([
                "message" => "Oops! Error reading json file",
                "error" => $th->getMessage(),
                "trace" => $th->getTrace(),
            ]);
            return;
        }
    }
}