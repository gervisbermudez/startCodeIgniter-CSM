<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class FilesController extends MY_Controller
{
    public $routes_permisions = [
        "index" => [
            "patern" => '/^admin\/files\/?$/',
            "required_permissions" => ["SELECT_FILES"],
            "conditions" => [],
        ],
        "ajax_upload_file" => [
            "patern" => '/^admin\/files\/ajax_upload_file/',
            "required_permissions" => ["CREATE_FILE"],
            "conditions" => [],
        ],
        "ajax_replace_file" => [
            "patern" => '/^admin\/files\/ajax_replace_file/',
            "required_permissions" => ["UPDATE_FILE"],
            "conditions" => [],
        ],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->check_permisions();
        $this->load->model('Admin/FileModel');
        $this->load->model('Admin/FileActivityModel');
    }

    public function index()
    {
        $this->renderAdminView('admin.files.file_explorer', lang('menu_files'), '');
    }

    public function ajax_upload_file()
    {
        $this->output->enable_profiler(false);

        $result = $this->uploadFile();

        if (!isset($result['error'])) {
            $result = $this->persistFileToDatabase($result);
        }

        $this->sendJsonResponse($result);
    }

    public function ajax_replace_file()
    {
        $this->output->enable_profiler(false);

        $file_id = $this->input->post('file_id');
        $file = new FileModel();
        if (!$file_id || !$file->find($file_id) || $file->file_type === 'folder') {
            $this->sendJsonResponse(array('error' => lang('toast_error')));
            return;
        }
        if (!$file->isAllowedLibraryPath($file->file_path) && !$file->isTrashPath($file->file_path)) {
            $this->sendJsonResponse(array('error' => lang('toast_error')));
            return;
        }
        if (!isset($_FILES['file']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
            $this->sendJsonResponse(array('error' => lang('toast_error')));
            return;
        }

        $info = pathinfo($_FILES['file']['name']);
        $ext = isset($info['extension']) ? strtolower($info['extension']) : $file->file_type;
        $oldAbsolute = $file->resolveDiskPath($file->file_path, $file->file_name, $file->file_type);
        $newAbsolute = $file->resolveDiskPath($file->file_path, $file->file_name, $ext);
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $newAbsolute)) {
            $this->sendJsonResponse(array('error' => lang('file_read_error')));
            return;
        }
        if ($oldAbsolute !== $newAbsolute && is_file($oldAbsolute)) {
            @unlink($oldAbsolute);
        }
        $file->file_type = $ext;
        $file->date_update = date('Y-m-d H:i:s');
        $file->save();
        $file->logActivity($file->file_id, 'replace', 'The file contents were replaced');
        $this->sendJsonResponse(array('ok' => true, 'file_type' => $ext));
    }

    private function uploadFile()
    {
        $this->load->library('FileUploader');
        $loaderClient = new FileUploader();
        return $loaderClient->upload();
    }

    private function persistFileToDatabase($result)
    {
        if (empty($result['savedFileName']) || empty($result['savedDir'])) {
            return $result;
        }

        $file = new FileModel();
        $insert_array = $file->get_array_save_file($result['savedFileName'], $result['savedDir']);
        $existingFile = $file->find_with([
            'file_path' => $insert_array['file_path'],
            'file_name' => $insert_array['file_name'],
            'file_type' => $insert_array['file_type'],
        ]);

        if (!$existingFile) {
            $file->set_data($insert_array);
            $insert_id = $this->db->insert_id();
            $created = $file->get_data(['file_id' => $insert_id]);
            $result['file_object'] = $created;
            if ($insert_id) {
                $file->logActivity($insert_id, 'upload', 'The file was upload');
            }
        } else {
            $file->date_update = date('Y-m-d H:i:s');
            $file->save();
            $result['file_object'] = $file->as_data();
        }

        return $result;
    }

    private function sendJsonResponse($data)
    {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}
