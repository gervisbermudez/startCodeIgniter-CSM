<?php

require APPPATH . 'libraries/REST_Controller.php';

class Forms extends REST_Controller
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
        $this->load->model('Admin/Page');
    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function index_get($type)
    {
        $where = "WHERE fc.form_name = '$type'";
        $data = $this->Forms_model->get_form_data($where);

        if (!$data) {
            $data = array();
            $this->response($data, REST_Controller::HTTP_NOT_FOUND);
        } else {
            $this->response($data, REST_Controller::HTTP_OK);
        }

    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function index_post()
    {
        $data = array();
        $this->response($data, REST_Controller::HTTP_NOT_FOUND);
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
    public function index_delete($id)
    {
        $data = array();
        $this->response($data, REST_Controller::HTTP_NOT_FOUND);
    }

    public function group_get($level = 1)
    {

        $this->load->model('UserMod');
        $data = $this->UserMod->get_usergroup(array('status' => '1', 'level >' => $level));
        if (!$data) {
            $data = array();
            $this->response($data, REST_Controller::HTTP_NOT_FOUND);
        } else {
            $this->response($data, REST_Controller::HTTP_OK);
        }

    }

}
