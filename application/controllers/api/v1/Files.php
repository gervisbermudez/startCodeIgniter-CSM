<?php

require APPPATH . 'libraries/REST_Controller.php';

class Files extends REST_Controller
{

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function __construct()
    {
        parent::__construct();
        $this->output->enable_profiler(false);
        $this->load->database();
        $this->lang->load('rest_lang', 'english');
        if (!$this->session->userdata('logged_in')) {
            $this->lang->load('login_lang', 'english');
            $this->response([
                'code' => REST_Controller::HTTP_UNAUTHORIZED,
                'error_message' => lang('user_not_authenticated'),
            ], REST_Controller::HTTP_UNAUTHORIZED);
            exit();
        }
        $this->load->model('Admin/File');

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

        $file = new File();
        if ($file_id) {
            $result = $file_path ? $file->find_with(['file_path' => $file_path, "file_id" => $file_id]) : $file->find($file_id);
            $result = $result ? $file : [];
        } else {
            $result = $file_path ? $file->where($where) : $file->all();
        }

        if ($result) {
            $response = array(
                'code' => 200,
                'data' => $result,
            );
            $this->response($response, REST_Controller::HTTP_OK);
            return;
        }

        if ($file_id) {
            $response = array(
                'code' => REST_Controller::HTTP_NOT_FOUND,
                "error_message" => lang('not_found_error'),
                'data' => [],
            );
        } else {
            $response = array(
                'code' => REST_Controller::HTTP_OK,
                'data' => [],
            );
        }
        $this->response($response, REST_Controller::HTTP_OK);
    }

    /**
     * @api {post} /login/ Auth the client into the Start CMS API
     * @apiName login
     * @apiGroup Login
     *
     * @apiParam {string} username The username of the user.
     * @apiParam {string} password The password of the user.
     *
     *
     * @apiSuccess {integer} status The status code of the request.
     * @apiSuccess {string} token  The JWT token.
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {
     *     "status": 200,
     *     "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyX2lkIjoiMTgifQ.NftK7Sr9Iez248IvAaSg4qmZRYVA9IlDoWOSS-sARWQ"
     * }
     *
     * @apiError UserOrPasswordNotFound The <code>username</code> or <code>password</code> was not found.
     * @apiErrorExample {json} Error-Response:
     * HTTP/1.1 401 Unauthorized
     * {
     *   "error_message": "Username or password not found",
     *   "error_code": 3
     * }
     * @apiError Unauthorized Invalid <code>username</code> or <code>password</code>
     * @apiErrorExample {json} Error-Response:
     * HTTP/1.1 401 Unauthorized
     * {
     *   "error_message": "Invalid username or password",
     *   "error_code": 2
     * }
     */
    public function index_post()
    {

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
    public function index_delete()
    {
        $this->response(array('Metodo no permitido'), REST_Controller::HTTP_METHOD_NOT_ALLOWED);

    }

    public function featured_file_post()
    {
        $post_file = $this->input->post('file');
        $file = new File();
        $result = $file->find($post_file['file_id']);
        if ($result) {
            $file->featured = $post_file["featured"];
            $result = $file->save();
            $response = array(
                'code' => 200,
                'data' => $result,
            );
        } else {
            $response = array(
                'code' => REST_Controller::HTTP_NOT_FOUND,
                "error_message" => lang('not_found_error'),
                'data' => [],
            );
        }
        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function reload_file_explorer_post($folder = null)
    {
        $file = new File();

        if ($folder != null) {
            $file->current_folder = $folder . '/';
        }

        $response = array(
            'code' => 200,
            'data' => [
                'result' => $file->map_files(),
            ],
        );

        $this->response($response, REST_Controller::HTTP_OK);

    }

    public function move_file_post()
    {
        $file = $this->input->post('file');
        $newPath = $this->input->post('newPath');
        $file = new File();
        $restult = $file->find_with(array('rand_key' => $file['rand_key']));
        if ($restult) {
            $file->file_path = $newPath;
            $file->save();
            $file_name = $file['file_name'] . '.' . $file['file_type'];
            $rename = rename($file['file_path'] . $file_name, $newPath . $file_name);
            $response = array(
                'code' => 200,
                'data' => $rename,
            );
        } else {
            $restult = array(
                'code' => REST_Controller::HTTP_NOT_FOUND,
                "error_message" => lang('not_found_error'),
                'data' => [],
            );
        }
        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function copy_file_post()
    {
        $file = $this->input->post('file');

        $file_model = new File();

        $newPath = $this->input->post('newPath');
        $file_name = $file['file_name'] . '.' . $file['file_type'];
        $rename = copy($file['file_path'] . $file_name, $newPath . $file_name);
        $result = $rename;
        if ($rename) {
            $insert_array = $file_model->get_array_save_file($file_name, $newPath);
            $result = $file_model->set_data($insert_array);
        }

        $response = array(
            'code' => 200,
            'data' => $result,
        );
        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function rename_file_post()
    {
        $file = $this->input->post('file');
        $file_model = new File();
        $result = $file_model->find_with(array('rand_key' => $file['rand_key']));
        if ($result) {
            $file_model->file_name = $file['new_name'];
            $file_model->save();
            $rename = rename(
                $file['file_path'] . $file['file_name'] . '.' . $file['file_type'],
                $file['file_path'] . $file['new_name'] . '.' . $file['file_type']
            );
            $response = array(
                'code' => 200,
                'data' => $rename,
            );
        } else {
            $response = array(
                'code' => REST_Controller::HTTP_NOT_FOUND,
                "error_message" => lang('not_found_error'),
                'data' => [],
            );
        }
        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function filter_files_post()
    {
        $filter_name = $this->input->post('filter_name');
        $filter_value = $this->input->post('filter_value');
        $result = $this->File->get_filter_files($filter_name, $filter_value);
        $response = array(
            'code' => 200,
            'data' => $result,
        );

        $this->response($response, REST_Controller::HTTP_OK);
    }

}
