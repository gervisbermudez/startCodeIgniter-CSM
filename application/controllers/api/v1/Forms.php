<?php

require APPPATH . 'libraries/REST_Controller.php';

class Forms extends REST_Controller
{

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
        $this->load->model('Admin/Form_custom');
    }

    /**
     *
     * @api {get} /forms/:form_id Get a lists of users
     * @apiName GetForms
     * @apiGroup Forms
     *
     * @apiParam {Number} form_id <code>optional</code> Form unique ID.
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {
     *    "code": 200,
     *    "data": [
     *        {
     *            "form_id": "18",
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
    public function index_get($form_id = null)
    {
        $form = new Form_custom();
        if ($form_id) {
            $result = $form->find($form_id);
            $result = $result ? $form : [];
        } else {
            $result = $form->all();
        }

        if ($result) {
            $response = array(
                'code' => 200,
                'data' => $result,
            );
            $this->response($response, REST_Controller::HTTP_OK);
            return;
        }

        if ($form_id) {
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
    public function index_post()
    {
        //@todo: validate data
        $data = (json_decode($_POST['data']));
        if (isset($data->form_custom_id) && $data->form_custom_id) {
            //Update Form
            $result = $this->Form_custom->update_form($data);
        } else {
            //Insert Form
            $result = $this->Form_custom->save_form($data);
        }

        if ($result) {
            $response = array(
                'code' => REST_Controller::HTTP_OK,
                'data' => [
                    'form_custom_id' => $result,
                ],
            );

            $this->response($response, REST_Controller::HTTP_OK);
        } else {
            $response = array(
                'code' => REST_Controller::HTTP_BAD_REQUEST,
                "error_message" => lang('unexpected_error'),
                'data' => $_POST,
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
    public function index_delete($form_id = null)
    {
        $result = $this->Form_custom->delete_form($form_id);
        if ($result) {
            $response = array(
                'code' => REST_Controller::HTTP_OK,
                'data' => [],
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

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function data_get($form_id = null)
    {
        $this->load->model('Admin/Form_content');
        $Form_conten = new Form_content();
        if ($form_id) {
            $result = $Form_conten->where(['form_content_id' => $form_id]);
            $result = $result ? $result : [];
        } else {
            $result = $Form_conten->all();
        }

        if ($result) {
            $response = array(
                'code' => 200,
                'data' => $result,
            );
            $this->response($response, REST_Controller::HTTP_OK);
            return;
        } else {
            $response = array(
                'code' => REST_Controller::HTTP_NOT_FOUND,
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
    public function data_post()
    {
        $this->load->model('Admin/Form_content');
        $Form_conten = new Form_content();
        $data = $_POST['data'];
        if (isset($data['form_content_id']) && $data['form_content_id']) {
            $result = $Form_conten->update_data_form($data);
        } else {
            $result = $this->Form_content->save_data_form((object) $data);
        }
        if ($result) {
            $response = array(
                'code' => REST_Controller::HTTP_OK,
                'data' => $result,
            );
            $this->response($response, REST_Controller::HTTP_OK);
        } else {
            $response = array(
                'code' => REST_Controller::HTTP_BAD_REQUEST,
                "error_message" => lang('unexpected_error'),
                'data' => $_POST,
            );
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function data_put($id)
    {
        $this->response([], REST_Controller::HTTP_OK);
    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function data_delete($form_content_id = null)
    {
        $this->load->model('Admin/Form_content');
        $Form_conten = new Form_content();
        $Form_conten->find($form_content_id);
        $result = $Form_conten->delete();
        if ($result) {
            $response = array(
                'code' => REST_Controller::HTTP_OK,
                'data' => [],
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
}
