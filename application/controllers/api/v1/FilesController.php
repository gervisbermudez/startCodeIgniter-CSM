<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require APPPATH . 'libraries/REST_Controller.php';

class FilesController extends REST_Controller
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
        $this->load->model('Admin/FileModel');
        $this->load->model('Admin/FileActivityModel');

    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function index_get($file_id = null)
    {
        $file_path = $this->input->get('path');
        $where = array('file_path' => $file_path, 'status' => 1);

        $file = new FileModel();
        if ($file_id) {
            $result = $file_path ? $file->find_with(['file_path' => $file_path, "file_id" => $file_id]) : $file->find($file_id);
            $result = $result ? $file : [];
        } else {
            $result = $file_path ? $file->where($where) : $file->all();
        }

        if ($result) {
            $this->response_ok($result);
            return;
        }

        $this->response_error(lang('not_found_error'));
    }

    public function index_post()
    {
        $this->response(array('Metodo no permitido'), REST_Controller::HTTP_METHOD_NOT_ALLOWED);
    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function index_put()
    {
        $this->response(array('Metodo no permitido'), REST_Controller::HTTP_METHOD_NOT_ALLOWED);
    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function delete_post($file_id = null)
    {

        if (!$file_id) {
            $response = array(
                'code' => REST_Controller::HTTP_NOT_FOUND,
                "error_message" => lang('not_found_error'),
                'data' => [],
            );
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
            return;
        }

        $result = false;
        $file = new FileModel();
        $result = $file->find($file_id);

        if ($result) {
            unlink($file->file_path . $file->file_name . '.' . $file->file_type);
            $file->delete();
            $this->response_ok($result);
            return;
        }
        $this->response_error(lang('not_found_error'));
    }

    public function featured_file_post()
    {
        $post_file = $this->input->post('file');
        $file = new FileModel();
        $result = $file->find($post_file['file_id']);
        if ($result) {
            $file->featured = $post_file["featured"];
            $result = $file->save();

            $file_activity = new FileActivityModel();
            $file_activity->file_id = $file->file_id;
            $file_activity->user_id = userdata('user_id');
            $file_activity->action = "featured";
            $file_activity->description = $post_file["featured"] ? "The file has been marked as featured" : "The file has been removed as featured";
            $file_activity->date_create = date("Y-m-d H:i:s");
            $file_activity->status = 1;
            $file_activity->save();

            $this->response_ok($result);
            return;
        }
        $this->response_error(lang('not_found_error'));
    }

    public function reload_file_explorer_post($folder = null)
    {
        $File = new FileModel();
        if ($folder != null) {
            $File->current_folder = $folder . '/';
        }
        $allFiles = $File->all();

        foreach ($allFiles as $key => $file) {
            if (!file_exists($file->file_path . $file->file_name . '.' . $file->file_type)) {
                $File->find($file->file_id);
                $File->delete();
            }
        }

        $this->response_ok(['result' => $File->map_files()]);
    }

    public function move_file_post()
    {
        $file = $this->input->post('file');
        $newPath = $this->input->post('newPath');
        $file_model = new FileModel();
        $restult = $file_model->find_with(array('rand_key' => $file['rand_key']));
        if ($restult) {
            $file_model->file_path = $newPath;
            $file_model->save();
            $file_name = $file['file_name'] . '.' . $file['file_type'];
            $rename = rename($file['file_path'] . $file_name, $newPath . $file_name);

            $file_activity = new FileActivityModel();
            $file_activity->file_id = $file_model->file_id;
            $file_activity->user_id = userdata('user_id');
            $file_activity->action = "move";
            $file_activity->description = "The file was moved to " . $newPath;
            $file_activity->date_create = date("Y-m-d H:i:s");
            $file_activity->status = 1;
            $file_activity->save();

            $this->response_ok($rename);
            return;
        }

        $this->response_error(lang('not_found_error'));
    }

    public function copy_file_post()
    {
        $file = $this->input->post('file');

        $file_model = new FileModel();

        $newPath = $this->input->post('newPath');
        $file_name = $file['file_name'] . '.' . $file['file_type'];
        $new_file_name = $file['file_name'] . '.' . $file['file_type'];
        if ($file['file_path'] . $file_name == $newPath . $new_file_name) {
            $new_file_name = $file['file_name'] . '(1)' . '.' . $file['file_type'];
        }
        $rename = copy($file['file_path'] . $file_name, $newPath . $new_file_name);
        $result = $rename;
        if ($rename) {
            $insert_array = $file_model->get_array_save_file($new_file_name, $newPath);
            $result = $file_model->set_data($insert_array);
        }

        $this->response_ok($result);
    }

    public function make_dir_post()
    {
        $path = $this->input->post('path');
        $new_folder_name = $this->input->post('new_folder_name');
        $result = false;
        if (!is_dir($path . $new_folder_name)) {
            $result = mkdir($path . $new_folder_name);
        }
        if ($result) {
            $folder = new FileModel();
            $file_key = random_string('alnum', 16);
            $folder->rand_key = $file_key;
            $folder->file_name = $new_folder_name;
            $folder->file_path = $path;
            $folder->file_type = 'folder';
            $folder->parent_name = $path;
            $folder->user_id = userdata('user_id');
            $folder->shared_user_group_id = userdata('usergroup_id');
            $folder->share_link = "admin/files/shared_file/" . $file_key;
            $folder->date_create = date("Y-m-d H:i:s");
            $folder->date_update = date("Y-m-d H:i:s");
            $folder->date_featured = 0;
            $folder->status = 1;
            $folder->save();
            $response = array(
                'code' => 200,
                'data' => $folder,
            );
            $this->response_ok($folder);
        }

        $this->response_error(lang('not_found_error'));
    }

    public function rename_file_post()
    {
        $file = $this->input->post('file');
        $file_model = new FileModel();
        $result = $file_model->find_with(array('rand_key' => $file['rand_key']));
        if ($result) {
            $file_model->file_name = $file['new_name'];
            $file_model->save();
            $rename = rename(
                $file['file_path'] . $file['file_name'] . '.' . $file['file_type'],
                $file['file_path'] . $file['new_name'] . '.' . $file['file_type']
            );

            $file_activity = new FileActivityModel();
            $file_activity->file_id = $file_model->file_id;
            $file_activity->user_id = userdata('user_id');
            $file_activity->action = "rename";
            $file_activity->description = "The file " . $file['file_name'] . '.' . $file['file_type'] . " was renamed to " . $file['new_name'] . '.' . $file['file_type'];
            $file_activity->date_create = date("Y-m-d H:i:s");
            $file_activity->status = 1;
            $file_activity->save();

            $this->response_ok($result);
        }
        $this->response_error(lang('not_found_error'));
    }

    public function filter_files_get()
    {
        $filter_name = $this->input->get('filter_name');
        $filter_value = $this->input->get('filter_value');
        $result = $this->File->get_filter_files($filter_name, $filter_value, null, array('date_update', "DESC"));
        $this->response_ok($result);
    }

    public function get_file_content_get()
    {
        $file = $this->input->get('file');
        $file_model = new FileModel();
        $result = $file_model->find($file["file_id"]);

        if (!$result) {
            $this->response_error([
                "message" => "File seems doesn't exist!",
            ]);
            return;
        }

        try {
            $string = file_get_contents($file_model->getFileFullPath());
            $this->response_ok(
                [
                    "message" => "File content",
                ],
                ["file_content" => $string]
            );
        } catch (\Throwable $th) {
            if ($string === false) {
                $this->response_error([
                    "message" => "Oops! Error reading file",
                ]);
                return;
            }
        }

    }

}