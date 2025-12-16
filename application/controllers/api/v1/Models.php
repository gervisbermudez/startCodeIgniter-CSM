<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require APPPATH . 'libraries/REST_Controller.php';

class Models extends REST_Controller
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
        $this->load->model('Admin/CustomModel');
    }

    /**
     *
     * @api {get} /models/:form_id Get a lists of users
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
        $form = new CustomModel();
        if ($form_id) {
            $result = $form->find($form_id);
            $result = $result ? $form : [];
        } else {
            $result = $form->all();
        }

        if ($result) {
            $this->response_ok($result);
            return;
        }

        $this->response_error(lang('not_found_error'));
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
        if (isset($data->custom_model_id) && $data->custom_model_id) {
            //Update Form
            $result = $this->Custom_model->update_form($data);
        } else {
            //Insert Form
            $result = $this->Custom_model->save_form($data);
        }

        if ($result) {
            $this->response_ok(['custom_model_id' => $result]);
            return;
        }

        $this->response_error(lang('unexpected_error'), [], REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
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
        $form = new CustomModel();
        $result = $form->find($form_id);
        if ($result) {
            $result = $form->delete($form_id);
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
    public function data_get($form_id = null)
    {
        $this->load->model('Admin/CustomModelContent');
        $Form_conten = new CustomModelContent();
        if ($form_id) {
            $result = $Form_conten->where(['custom_model_content_id' => $form_id]);
            $result = $result ? $result : [];
        } else {
            $result = $Form_conten->all();
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
    public function form_data_get($form_id = null)
    {
        $this->load->model('Admin/CustomModelContent');
        $Form_conten = new CustomModelContent();
        if ($form_id) {
            $result = $Form_conten->where(['custom_model_id' => $form_id, 'status' => 1]);
            $result = $result ? $Form_conten->as_single_object($result) : [];
        } else {
            $result = $Form_conten->all();
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
    public function data_post()
    {
        $this->load->model('Admin/CustomModelContent');
        $Form_conten = new CustomModelContent();
        $data = $_POST['data'];
        if (isset($data['custom_model_content_id']) && $data['custom_model_content_id']) {
            $result = $Form_conten->update_data_form($data);
        } else {
            $result = $this->Custom_model_content->save_data_form((object) $data);
        }
        if ($result) {
            $this->response_ok($result);
            return;

        }
        $this->response_error(lang('unexpected_error'), [], REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
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
    public function data_delete($custom_model_content_id = null)
    {
        $this->load->model('Admin/CustomModelContent');
        $Form_conten = new CustomModelContent();
        $Form_conten->find($custom_model_content_id);
        $result = $Form_conten->delete();
        if ($result) {
            $this->response_ok($result);
            return;
        }
        $this->response_error(lang('not_found_error'));
    }

    public function data_set_status_post($custom_model_content_id = null)
    {
        $this->load->model('Admin/CustomModelContent');
        $Form_conten = new CustomModelContent();
        $Form_conten->find($custom_model_content_id);
        $Form_conten->status = $this->input->post('status');
        $result = $Form_conten->save();
        if ($result) {
            $this->response_ok($result);
        }
        $this->response_error(lang('not_found_error'));
    }
}
