<?php

require APPPATH . 'libraries/REST_Controller.php';

class Users extends REST_Controller
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
        $this->load->model('Admin/User');

    }

    /**
     *
     * @api {get} /users/:user_id Get a lists of users
     * @apiName GetUser
     * @apiGroup User
     *
     * @apiParam {Number} user_id <code>optional</code> User unique ID.
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {
     *    "code": 200,
     *    "data": [
     *        {
     *            "user_id": "18",
     *            "username": "gerber",
     *            "email": "gerber@gmail.com",
     *            "lastseen": "2016-09-03 03:22:31",
     *            "usergroup_id": "2",
     *            "status": "1",
     *            "user_data": {
     *                "nombre": "Gervis",
     *                "apellido": "Mora",
     *                "direccion": "Mara",
     *                "telefono": "0414-1672173",
     *                "create by": "gerber",
     *                "avatar": "300_3.jpg"
     *            },
     *            "role": "Administrador",
     *            "level": "2",
     *            "date_create": "2020-03-01 16:11:25",
     *            "date_update": "2020-03-01 16:11:25"
     *        }
     *    ]
     * }
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "data": [],
     *       "code": 404
     *     }
     */
    public function index_get($user_id = null)
    {
        $user = new User();
        if ($user_id) {
            $result = $user->find($user_id);
            $result = $result ? $user : [];
        } else {
            $result = $this->User->get_full_info($user_id);
        }
        if ($result) {
            $response = array(
                'code' => 200,
                'data' => $result,
            );
            $this->response($response, REST_Controller::HTTP_OK);
            return;
        }

        $response = array(
            'code' => REST_Controller::HTTP_NOT_FOUND,
            'data' => [],
        );
        $this->response($response, REST_Controller::HTTP_NOT_FOUND);
    }

    /**
     *
     * @api {post} /users/ Create a new User
     * @apiName PostUser
     * @apiGroup User
     *
     * @apiParam {string} username The unique user username
     * @apiParam {string} password The password
     * @apiParam {string} email The email
     * @apiParam {integer} usergroup_id The usergroup_id
     * @apiParam {string} user_data[nombre] The user name
     * @apiParam {string} user_data[apellido] The user lastname
     * @apiParam {string} user_data[direccion] The user address
     * @apiParam {string} user_data[telefono] The user phone
     *
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {
     *     "code": 200,
     *     "data": {
     *         "primaryKey": "user_id",
     *         "user_data": {
     *             "nombre": "Nestor",
     *             "apellido": "Barroso",
     *             "direccion": "Caseros",
     *             "telefono": "112345678"
     *         },
     *         "table": "user",
     *         "timestamps": true,
     *         "fields": [
     *             "user_id",
     *             "username",
     *             "password",
     *             "email",
     *             "lastseen",
     *             "usergroup_id",
     *             "status",
     *             "date_create",
     *             "date_update"
     *         ],
     *         "username": "nestor12",
     *         "password": "Lamisu1234_",
     *         "email": "nestor@gmail.com",
     *         "lastseen": "2020-05-05 02:49:53",
     *         "usergroup_id": "3",
     *         "status": "1",
     *         "user_id": "34",
     *         "date_create": "2020-05-04 21:49:53",
     *         "date_update": "2020-05-04 21:49:53"
     *     }
     * }
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "data": [],
     *       "code": 404
     *     }
     */
    public function index_post()
    {
        $this->load->library('FormValidator');

        $form = new FormValidator();

        $config = array(
            array('field' => 'username', 'label' => 'username', 'rules' => 'required|min_length[5]|max_length[18]|alpha_numeric'),
            array('field' => 'password', 'label' => 'password', 'rules' => 'required|min_length[5]|max_length[18]|regex_match[/^(?=.*?[0-9])(?=.*?[A-Z])(?=.*?[#.?!@$%^&*\-_]).{8,}$/]'),
            array('field' => 'email', 'label' => 'email', 'rules' => 'required|valid_email'),
            array('field' => 'usergroup_id', 'label' => 'usergroup_id', 'rules' => 'required|integer|is_natural_no_zero'),
        );

        $form->set_rules($config);

        if (!$form->run()) {
            $response = array(
                'code' => REST_Controller::HTTP_BAD_REQUEST,
                'error_message' => lang('new_user_validations_error'),
                'errors' => $form->_error_array,
                'request_data' => $_POST,
            );
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $usuario = new User();
        $this->input->post('user_id') ? $usuario->find($this->input->post('user_id')) : false;
        $usuario->username = $this->input->post('username');
        $usuario->password = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
        $usuario->email = $this->input->post('email');
        $usuario->lastseen = date("Y-m-d H:i:s");
        $usuario->usergroup_id = $this->input->post('usergroup_id');
        $usuario->status = 1;
        $usuario->user_data = $this->input->post('user_data');
        if (!$this->input->post('user_id')) {
            $usuario->user_data['create_by_id'] = userdata('user_id');
        }

        if ($usuario->save()) {
            $response = array(
                'code' => REST_Controller::HTTP_OK,
                'data' => $usuario,
            );

            $this->response($response, REST_Controller::HTTP_OK);
            return;

        } else {

            $response = array(
                'code' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,
                'error_message' => lang('new_user_unexpected_error'),
                'data' => $_POST,
            );

            $this->response($response, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
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
        $usuario = new User();
        $usuario->find($id);
        if ($usuario->delete()) {
            $response = array(
                'code' => REST_Controller::HTTP_OK,
                'data' => $usuario,
            );
            $this->response($response, REST_Controller::HTTP_OK);
            return;
        } else {
            $response = array(
                'code' => REST_Controller::HTTP_NOT_FOUND,
                'error_message' => lang('user_not_found_error'),
                'data' => $_POST,
            );
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function usergroups_get($usergroup_id = null)
    {

        $this->load->model('Admin/Usergroup');

        $usergroup = new Usergroup();

        if($usergroup_id){
            $result = $usergroup->find_with(array("usergroup_id" => $usergroup_id));
        }else{
            $result = $usergroup->where(array('level >=' => userdata('level')));
        }

        if ($result) {
            $response = array(
                'code' => REST_Controller::HTTP_OK,
                'data' => $result,
            );
            $this->response($response, REST_Controller::HTTP_OK);
            return;
        }

        $response = array(
            'code' => REST_Controller::HTTP_NOT_FOUND,
            'data' => $result,
        );
        $this->response($response, REST_Controller::HTTP_NOT_FOUND);
    }

    public function usergroups_post()
    {
        $this->response([], REST_Controller::HTTP_OK);
    }

    /**
     *
     * @api {get} /users/timeline/:user_id Get a lists of users
     * @apiName GetUser
     * @apiGroup User
     *
     * @apiParam {Number} user_id <code>required</code> User unique ID.
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {
     * "code": 200,
     * "data": [
     *   {
     *     "page_id": "68",
     *     "path": "collections",
     *     "template": "default",
     *     "title": "Collections",
     *     "subtitle": "",
     *     "content": "",
     *     "page_type_id": "1",
     *     "user_id": "1",
     *     "visibility": "1",
     *     "categorie_id": "0",
     *     "subcategorie_id": "0",
     *     "status": "1",
     *     "layout": "default",
     *     "mainImage": null,
     *     "date_publish": "2020-09-13 23:31:39",
     *     "date_update": "2020-09-13 11:20:05",
     *     "date_create": "2020-09-13 23:31:39",
     *     "user": {
     *       "user_id": "1",
     *       "username": "gerber",
     *       "email": "gerber@gmail.com",
     *       "lastseen": "2020-09-14 16:35:23",
     *       "usergroup_id": "1",
     *       "status": "1",
     *       "date_create": "2020-03-01 16:11:25",
     *       "date_update": "2020-09-09 14:56:41",
     *       "usergroup": {
     *         "usergroup_id": "1",
     *         "name": "root",
     *         "level": "1",
     *         "description": "All permisions allowed",
     *         "status": "1",
     *         "date_create": "2016-08-27 09:22:22",
     *         "date_update": "2020-03-01 16:10:01"
     *       },
     *       "user_data": {
     *         "nombre": "Gervis",
     *         "apellido": "Mora",
     *         "direccion": "Mara",
     *         "telefono": "0414-1672173",
     *         "create by": "gerber",
     *         "avatar": "300_3.jpg"
     *       }
     *     },
     *     "model_type": "page"
     *   },
     * ]
     *
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "data": [],
     *       "code": 404
     *     }
     */
    public function timeline_get($user_id = null)
    {
        $user = new User();
        if ($user_id) {
            $result = $user->find($user_id);
            $result = $result ? $user : [];
            if ($result) {
                $response = array(
                    'code' => 200,
                    'data' => $user->get_timeline($user_id),
                );
                $this->response($response, REST_Controller::HTTP_OK);
                return;
            }
        }

        $response = array(
            'code' => REST_Controller::HTTP_NOT_FOUND,
            'data' => [],
        );
        $this->response($response, REST_Controller::HTTP_NOT_FOUND);
    }

    public function avatar_post()
    {

        $user_id = $this->input->post('user_id');
        $avatar = $this->input->post('avatar');

        $usuario = new User();
        $result = false;

        if ($usuario->find($user_id)) {

            if (isset($usuario->user_data->avatar)) {
                $usuario->user_data->avatar = $avatar;
                $result = $usuario->save();
            } else {
                $insert = array(
                    'user_id' => $user_id,
                    '_key' => 'avatar',
                    '_value' => $avatar,
                    'status' => 1,
                );
                $result = $usuario->set_user_data($insert);
            }

            if ($result) {
                $response = array(
                    'code' => REST_Controller::HTTP_OK,
                    'data' => $result,
                );
                $this->response($response, REST_Controller::HTTP_OK);
                return;
            }
        }

        $response = array(
            'code' => REST_Controller::HTTP_BAD_REQUEST,
            'data' => $result,
            'user' => $usuario,
        );
        $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
    }
}
