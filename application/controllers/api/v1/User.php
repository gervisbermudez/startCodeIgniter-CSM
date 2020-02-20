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

        $sql = "SELECT u.`id`,
        u.`username`,
        u.`email`,
        u.`lastseen`,
        u.`usergroup`,
        u.`status`, CONCAT('{', GROUP_CONCAT(s.data), '}') AS `user_data`,
        g.name AS `role`,
        g.`level`
        FROM (
        SELECT d.id_user, CONCAT('\"', d._key, '\"', ':', '\"', d._value, '\"') AS `data`
        FROM datauserstorage d
        GROUP BY d.id) s
        INNER JOIN `user` u ON u.id = s.id_user
        INNER JOIN `usergroup` g ON u.usergroup = g.id
        $where
        GROUP BY s.id_user;";

        $data = $this->db->query($sql)->result_array();

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
