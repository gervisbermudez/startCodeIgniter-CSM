<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require APPPATH . 'libraries/REST_Controller.php';

class Config extends REST_Controller
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
        $this->load->model('Admin/SiteConfig');
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
        $SiteConfig = new SiteConfig();
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

        $configuration = new SiteConfig();
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
        // Load the DB utility class
        $this->load->dbutil();
        // Backup your entire database and assign it to a variable
        $backup = $this->dbutil->backup();
        // Load the file helper and write the file to your server
        $this->load->helper('file');
        if (!file_exists('./backups/database/')) {
            mkdir('./backups/database/', 0777, true);
        }
        $data = array();
        if (write_file('./backups/database/' . date('YmdHis') . '.gz', $backup)) {
            $data = array(
                'result' => "backup created",
                "code" => REST_Controller::HTTP_OK,
            );
        } else {
            $data = array(
                'result' => "unnable to created a backup",
                "code" => REST_Controller::HTTP_BAD_REQUEST,
            );
        }
        $this->response($data, REST_Controller::HTTP_OK);
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

            $this->load->model('Admin/SiteConfig');
            $config = new SiteConfig();

            $config->find_with(['config_name' => 'UPDATER_LAST_CHECK_DATA']);
            $config->config_value = $data;
            $config->save();

            $config = new SiteConfig();
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
        $this->load->model('Admin/Logger');

        $Logger = new Logger();
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
        $this->load->model('Admin/ApiLogs');

        $Api_logs = new ApiLogs();
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
        $this->load->model('Admin/UserTracking');

        $User_tracking = new UserTracking();
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
        $this->load->model('Admin/Page');
        $pages = new Page();
        $data['pages'] = $pages->all();

        $config = new SiteConfig();
        $data['config'] = $config->all();

        $this->response_ok($data);
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
            $this->load->model('Admin/Page');
            $pages = new Page();
            $data['pages'] = $pages->where($this->getWhereStringFrom($exportData["pages"], "page_id"));

            $data['pages'] = array_map("removeUser", $data['pages']->toArray());
        }

        if (isset($exportData["config"])) {
            $config = new SiteConfig();
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
                $this->load->model('Admin/Page');
                $file_content->pages = array_filter($file_content->pages, function ($page) use ($exportData) {
                    return in_array($page->page_id, $exportData->pages);
                });
                foreach ($file_content->pages as $key => $value) {
                    $page = new Page();
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
                    $SiteConfig = new SiteConfig();
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