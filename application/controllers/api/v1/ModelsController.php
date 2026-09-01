<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require APPPATH . 'libraries/REST_Controller.php';

class ModelsController extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->output->enable_profiler(false);
        $this->lang->load('rest_lang', 'english');
        $this->lang->load('admin/admin', $this->config->item('language'));

        if (!$this->verify_request()) {
            $this->response([
                'code' => REST_Controller::HTTP_UNAUTHORIZED,
            ], REST_Controller::HTTP_UNAUTHORIZED);
            exit();
        }

        $this->load->database();
        $this->load->model('Admin/CustomModelModel');
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
        if (!$this->require_model_permision('SELECT_FORM_CUSTOMS')) {
            return;
        }

        $form = new CustomModelModel();
        if ($form_id) {
            $result = $form->find($form_id);
            if ($result) {
                $payload = $form->as_data();
                $form->decorate_type($payload, true);
                $this->response_ok($payload);
                return;
            }
            $this->response_error(lang('not_found_error'));
            return;
        }

        $this->respond_index_list($form);
    }

    public function templates_get()
    {
        if (!$this->require_model_permision('SELECT_FORM_CUSTOMS')) {
            return;
        }

        $this->response_ok(list_collection_templates());
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
        $raw = isset($_POST['data']) ? $_POST['data'] : '';
        $data = is_string($raw) ? json_decode($raw) : $raw;
        $is_update = ($data && is_object($data) && !empty($data->custom_model_id));
        if (!$this->require_model_permision($is_update ? 'UPDATE_FORM_CUSTOM' : 'CREATE_FORM_CUSTOM')) {
            return;
        }
        if (!$data || !is_object($data)) {
            $this->response_error(lang('validations_error'), array('errors' => array('data' => lang('collections_need_field'))), REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $errors = $this->validate_collection_schema($data);
        if (!empty($errors)) {
            $this->response_error(lang('validations_error'), array('errors' => $errors), REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $exclude_id = !empty($data->custom_model_id) ? $data->custom_model_id : null;
        if ($this->CustomModelModel->slug_exists($data->slug, $exclude_id)) {
            $this->response_error(lang('validations_error'), array('errors' => array('slug' => lang('collections_slug_taken'))), REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        if (isset($data->custom_model_id) && $data->custom_model_id) {
            $result = $this->CustomModelModel->update_form($data);
            $action = 'updated';
        } else {
            $result = $this->CustomModelModel->save_form($data);
            $action = 'created';
        }

        if ($result === false && $this->CustomModelModel->last_error === 'tab_has_data') {
            $this->response_error(lang('collections_tab_has_data'), array(), REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }
        if ($result === false && $this->CustomModelModel->last_error === 'field_has_data') {
            $this->response_error(lang('collections_tab_has_data'), array(), REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        if ($result) {
            system_logger('custom_model', $result, $action, 'A collection has been ' . $action);
            $this->response_ok(array('custom_model_id' => $result));
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
        if (!$this->require_model_permision('DELETE_FORM_CUSTOM')) {
            return;
        }

        $form = new CustomModelModel();
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
        if (!$this->require_model_permision('SELECT_CONTENT_DATA')) {
            return;
        }

        $this->load->model('Admin/CustomModelContentModel');
        $Form_conten = new CustomModelContentModel();
        $custom_model_id = $this->input->get('custom_model_id');
        if ($form_id) {
            $result = $Form_conten->where(['custom_model_content_id' => $form_id]);
            $result = $result ? $result : [];
            if ($result) {
                $this->response_ok($result);
                return;
            }
            $this->response_error(lang('not_found_error'));
            return;
        }

        if ($custom_model_id) {
            $this->respond_index_list(
                $Form_conten,
                array(
                    'custom_model_id' => (int) $custom_model_id,
                    'status_in' => array(1, 2, 3),
                ),
                array('collection_items', 'DESC'),
                array('unfiltered' => true)
            );
            return;
        }

        $this->respond_index_list($Form_conten);
    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function form_data_get($form_id = null)
    {
        if (!$this->require_model_permision('SELECT_CONTENT_DATA')) {
            return;
        }

        $this->load->model('Admin/CustomModelContentModel');
        $this->load->model('Admin/CustomModelModel');
        if ($form_id) {
            $type = new CustomModelModel();
            if (!$type->find($form_id) || (int) $type->status !== 1) {
                $this->response_ok(array());
                return;
            }
            $content = new CustomModelContentModel();
            $items = $content->get_normalized_items($type, array());
            $this->response_ok($items);
            return;
        }

        $Form_conten = new CustomModelContentModel();
        $this->respond_index_list($Form_conten);
    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function data_post()
    {
        $raw = isset($_POST['data']) ? $_POST['data'] : null;
        $probe = $raw;
        if (is_string($probe)) {
            $probe = json_decode($probe);
        } elseif (is_array($probe)) {
            $probe = (object) $probe;
        }
        $is_update = ($probe && is_object($probe) && !empty($probe->custom_model_content_id));
        if (!$this->require_model_permision($is_update ? 'UPDATE_CONTENT_DATA' : 'CREATE_CONTENT_DATA')) {
            return;
        }

        $this->load->model('Admin/CustomModelContentModel');
        $Form_conten = new CustomModelContentModel();
        $data = isset($_POST['data']) ? $_POST['data'] : null;
        if (is_string($data)) {
            $decoded = json_decode($data);
            $data = $decoded ? $decoded : null;
        } elseif (is_array($data)) {
            $data = (object) $data;
        }
        if (!$data || !is_object($data)) {
            $this->response_error(lang('validations_error'), array('errors' => array('data' => lang('collections_error'))), REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }
        if (!empty($data->custom_model_content_id)) {
            $result = $Form_conten->update_data_form($data);
        } else {
            $result = $Form_conten->save_data_form($data);
        }
        if ($result) {
            system_logger('custom_model_content', $result['custom_model_content_id'], 'saved', 'A collection item has been saved');
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
        if (!$this->require_model_permision('DELETE_CONTENT_DATA')) {
            return;
        }

        $this->load->model('Admin/CustomModelContentModel');
        $Form_conten = new CustomModelContentModel();
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
        if (!$this->require_model_permision('UPDATE_CONTENT_DATA')) {
            return;
        }

        $this->load->model('Admin/CustomModelContentModel');
        $Form_conten = new CustomModelContentModel();
        if (!$Form_conten->find($custom_model_content_id)) {
            $this->response_error(lang('not_found_error'));
            return;
        }
        $status = (int) $this->input->post('status');
        if ($status !== 0 && $status !== 1 && $status !== 2) {
            $this->response_error(lang('validations_error'), array('errors' => array('status' => lang('collections_error'))), REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }
        $Form_conten->status = $status;
        $result = $Form_conten->save();
        if ($result) {
            $this->response_ok($result);
            return;
        }
        $this->response_error(lang('not_found_error'));
    }

    protected function validate_collection_schema($data)
    {
        $errors = array();
        if (empty($data->form_name) || !is_string($data->form_name)) {
            $errors['form_name'] = lang('collections_name');
        }
        $slug = isset($data->slug) ? $data->slug : '';
        if ($slug === '' || !preg_match('/^[a-z0-9_]+$/', $slug)) {
            $errors['slug'] = lang('collections_slug_invalid');
        }
        $tabs = isset($data->tabs) ? $data->tabs : array();
        $field_count = 0;
        foreach ($tabs as $tab) {
            $tab = is_object($tab) ? $tab : (object) $tab;
            $fields = isset($tab->custom_model_fields) ? $tab->custom_model_fields : array();
            $field_count += count($fields);
        }
        if (count($tabs) < 1 || $field_count < 1) {
            $errors['tabs'] = lang('collections_need_field');
        }
        return $errors;
    }

    protected function require_model_permision($permision)
    {
        if (!function_exists('has_permisions') || !has_permisions($permision)) {
            $this->response_error('You do not have permission to perform this action', array(), REST_Controller::HTTP_FORBIDDEN, REST_Controller::HTTP_FORBIDDEN);
            return false;
        }
        return true;
    }
}
