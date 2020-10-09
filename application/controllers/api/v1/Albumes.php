<?php

require APPPATH . 'libraries/REST_Controller.php';

class Albumes extends REST_Controller
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
        $this->lang->load('rest_lang', 'english');

        if (!$this->session->userdata('logged_in')) {
            $this->lang->load('login_lang', 'english');
            $this->response([
                'code' => REST_Controller::HTTP_UNAUTHORIZED,
                'error_message' => lang('user_not_authenticated'),
            ], REST_Controller::HTTP_UNAUTHORIZED);
            exit();
        }

        $this->load->database();
        $this->load->model('Admin/Album');

    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function index_get($album_id = null)
    {
        $album = new Album();
        if ($album_id) {
            $result = $album->where(["album_id" => $album_id])->first();
        } else {
            $result = $album->all();
        }

        if ($result) {
            $response = array(
                'code' => 200,
                'data' => $result,
            );
            $this->response($response, REST_Controller::HTTP_OK);
            return;
        }

        if ($album_id) {
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
     * Get All Data from this method.
     *
     * @return Response
     */
    public function index_post()
    {
        $this->load->library('FormValidator');
        $form = new FormValidator();

        $config = array(
            array('field' => 'name', 'label' => 'name', 'rules' => 'required|min_length[5]'),
            array('field' => 'description', 'label' => 'description', 'rules' => 'required|min_length[5]'),
            array('field' => 'status', 'label' => 'status', 'rules' => 'required|integer'),
        );

        $form->set_rules($config);

        if (!$form->run()) {
            $response = array(
                'code' => REST_Controller::HTTP_BAD_REQUEST,
                'error_message' => lang('validations_error'),
                'errors' => $form->_error_array,
                'request_data' => $_POST,
            );
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $album = new Album();
        $this->input->post('album_id') ? $album->find($this->input->post('album_id')) : false;
        $album->name = $this->input->post('name');
        $album->description = $this->input->post('description');
        $album->user_id = userdata('user_id');
        $album->status = $this->input->post('status');
        $album->date_publish = $this->input->post('date_publish') ? $this->input->post('date_publish') : date("Y-m-d H:i:s");
        $album->date_create = date("Y-m-d H:i:s");

        if ($album->save()) {

            $album_items = $this->input->post("album_items");
            $this->load->model('Admin/Album_items');
            foreach ($album_items as $value) {
               $item = new Album_items();
               $value['album_item_id'] ? $item->find($value['album_item_id']) : false;
               $item->album_id = $album->album_id;
               $item->file_id = $value['file_id'];
               $item->name = $value['name'];
               $item->description = $value['description'];
               $item->status = $value['status'];
               $item->date_create = date("Y-m-d H:i:s");
               $item->save();
            }

            $response = array(
                'code' => REST_Controller::HTTP_OK,
                'data' => $album,
            );

            $this->response($response, REST_Controller::HTTP_OK);

        } else {
            $response = array(
                'code' => REST_Controller::HTTP_BAD_REQUEST,
                "error_message" => lang('unexpected_error'),
                'data' => $_POST,
                'request_data' => $_POST,
            );

            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);

        }
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
        $page = new Album();
        $page->find($id);
        if ($page->delete()) {
            $response = array(
                'code' => REST_Controller::HTTP_OK,
                'data' => $page,
            );
            $this->response($response, REST_Controller::HTTP_OK);
            return;
        } else {
            $response = array(
                'code' => REST_Controller::HTTP_NOT_FOUND,
                'error_message' => lang('not_found_error'),
                'data' => $_POST,
            );
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }
    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function delete_album_item_get($item_album_id)
    {
        $this->load->model('Admin/Album_items');
        $album_items = new Album_items();
        $album_items->find($item_album_id);
        if ($album_items->delete()) {
            $response = array(
                'code' => REST_Controller::HTTP_OK,
                'data' => $album_items,
            );
            $this->response($response, REST_Controller::HTTP_OK);
            return;
        } else {
            $response = array(
                'code' => REST_Controller::HTTP_NOT_FOUND,
                'error_message' => lang('not_found_error'),
                'data' => $_POST,
            );
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }
    }

}
