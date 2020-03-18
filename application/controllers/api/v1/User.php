<?php

require APPPATH . 'libraries/REST_Controller.php';

class User extends REST_Controller
{

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        
        $this->load->model('Admin/User', 'User_model');
        
    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function index_get($id = 0)
    {
        $where = "";

        if (!empty($id)) {
            $where = "WHERE u.id = $id";
        }

        $data = $this->User_model->get_full_info($where);

        if (!$data) {
            $data = array();
            $this->response($data, REST_Controller::HTTP_NOT_FOUND);
        } else {
            foreach ($data as $key => &$value) {
                $data_values = json_decode($value['user_data']);
                $value['user_data'] = $data_values;
            }

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

        $data = $this->User_model->get_usergroup(array('status' => '1', 'level >' => $level));
        if (!$data) {
            $data = array();
            $this->response($data, REST_Controller::HTTP_NOT_FOUND);
        } else {
            $this->response($data, REST_Controller::HTTP_OK);
        }

    }

}
