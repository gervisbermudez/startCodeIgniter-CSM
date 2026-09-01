<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require APPPATH . 'libraries/REST_Controller.php';

class UsersController extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->output->enable_profiler(false);
        $this->lang->load('rest_lang', 'english');
        $this->lang->load('admin/users');

        if (!$this->verify_request()) {
            $this->response([
                'code' => REST_Controller::HTTP_UNAUTHORIZED,
            ], REST_Controller::HTTP_UNAUTHORIZED);
            exit();
        }

        $this->load->database();
        $this->load->model('Admin/UserModel', 'User');
        $this->refresh_editor_permisions();

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
        if (!$this->require_user_permision('SELECT_USERS')) {
            return;
        }

        if ($user_id) {
            $result = $this->User->get_full_info($user_id);
            if ($result) {
                // Return the first user object, not an array
                $this->response_ok(isset($result[0]) ? $result[0] : $result);
                return;
            }
            $this->response_error(lang('not_found_error'));
            return;
        }
        
        // Get all users with full info
        $result = $this->User->get_full_info();
        $this->response_ok($result);
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
        $user_id = $this->input->post('user_id');
        $is_update = ($user_id !== null && $user_id !== '' && $user_id !== false);
        if (!$this->require_user_permision($is_update ? 'UPDATE_USER' : 'CREATE_USER')) {
            return;
        }

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
            $this->response_error(lang('new_user_validations_error'), ['errors' => $form->_error_array], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }
        $user = new UserModel();
        $this->input->post('user_id') ? $user->find($this->input->post('user_id')) : false;
        $user->username = $this->input->post('username');
        $user->password = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
        $user->email = $this->input->post('email');
        $user->lastseen = date("Y-m-d H:i:s");
        $user->usergroup_id = $this->input->post('usergroup_id');
        $user->status = 1;
        $user->user_data = $this->input->post('user_data');
        if (!$this->input->post('user_id')) {
            $user->user_data['create_by_id'] = userdata('user_id');
            $user->date_create = date('Y-m-d H:i:s');
            $user->date_update = $user->date_create;
        }
        if ($user->save()) {
            $is_create = !$this->input->post('user_id');
            system_logger('users', $user->user_id, ($is_create ? "created" : "updated"),
                ($is_create ? "A user has been created" : "A user has been updated"));
            if ($is_create) {
                $exclude = array((int) userdata('user_id'), (int) $user->user_id);
                $recipients = array();
                $others = (new UserModel())->all();
                if ($others) {
                    foreach ($others as $other) {
                        if (!in_array((int) $other->user_id, $exclude, true)) {
                            $recipients[] = $other->user_id;
                        }
                    }
                }
                if (!empty($recipients)) {
                    set_notification(
                        lang('notification_user_created_title'),
                        sprintf(lang('notification_user_created_desc'), $user->username),
                        'user_created',
                        'admin/users/ver/' . $user->user_id,
                        $recipients
                    );
                }
            }
            $this->response_ok($user);
            return;
        }
        $this->response_error(lang('new_user_unexpected_error'), [], REST_Controller::HTTP_INTERNAL_SERVER_ERROR, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
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
    public function index_delete($user_id = null)
    {
        if (!$this->require_user_permision('DELETE_USER')) {
            return;
        }

        $user = new UserModel();
        $user->find($user_id);
        if ($user->delete()) {
            system_logger('users', $user->user_id, ("deleted"), ("A user has been deleted"));
            $this->response_ok($user);
            return;
        }
        $this->response_error(lang('user_not_found_error'));
    }

    public function usergroups_get($usergroup_id = null)
    {
        if (!$this->require_user_permision('SELECT_USERGROUPS')) {
            return;
        }
        $this->load->model('Admin/UsergroupModel');
        $usergroup = new UsergroupModel();
        if ($usergroup_id) {
            $result = $usergroup->find($usergroup_id);
            if (!$result) {
                $this->response_error(lang('not_found_error'));
                return;
            }
            $usergroup->{'usergroup_permisions'} = $usergroup->usergroup_permisions();
            $this->response_ok($usergroup);
            return;
        }
        $result = $usergroup->all();
        $this->response_ok($result ? $result : array());
    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function usergroups_post()
    {
        $posted_id = $this->input->post('usergroup_id');
        $is_create = !$posted_id;
        if (!$this->require_user_permision($is_create ? 'CREATE_USERGROUP' : 'UPDATE_USERGROUP')) {
            return;
        }
        $this->load->library('FormValidator');
        $this->load->model('Admin/UsergroupModel');

        $form = new FormValidator();
        $config = array(
            array('field' => 'name', 'label' => 'name', 'rules' => 'required|min_length[1]'),
            array('field' => 'description', 'label' => 'description', 'rules' => 'required|min_length[1]'),
            array('field' => 'status', 'label' => 'status', 'rules' => 'required|integer'),
        );
        $form->set_rules($config);
        if (!$form->run()) {
            $this->response_error(lang('validations_error'), array('errors' => $form->_error_array), REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $usergroup = new UsergroupModel();
        if (!$is_create) {
            $usergroup_id = (int) $posted_id;
            if ($usergroup_id === 1 && (int) userdata('usergroup_id') !== 1) {
                $this->response_error(lang('not_have_permissions'), array(), REST_Controller::HTTP_FORBIDDEN, REST_Controller::HTTP_FORBIDDEN);
                return;
            }
            if (!$usergroup->find($usergroup_id)) {
                $this->response_error(lang('not_found_error'));
                return;
            }
        }

        $final_ids = $this->build_merged_permision_ids($is_create ? 0 : (int) $usergroup->usergroup_id, $is_create);
        if ($final_ids === false) {
            return;
        }

        $usergroup->name = $this->input->post('name');
        $usergroup->description = $this->input->post('description');
        $usergroup->status = $this->input->post('status');
        if ($is_create) {
            $usergroup->level = $this->input->post('level') ? $this->input->post('level') : userdata('level') + 1;
            $usergroup->user_id = userdata('user_id');
            $usergroup->parent_id = userdata('usergroup_id');
            $usergroup->date_create = date("Y-m-d H:i:s");
            $usergroup->date_update = $usergroup->date_create;
        }
        if ($usergroup->save()) {
            $this->replace_usergroup_permisions($usergroup->usergroup_id, $final_ids);
            system_logger(
                'usergroups',
                $usergroup->usergroup_id,
                $is_create ? 'created' : 'updated',
                $is_create ? 'A usergroup has been created' : 'A usergroup has been updated'
            );
            $this->response_ok($usergroup);
            return;
        }
        $this->response_error(lang('unexpected_error'), array(), REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
    }

    public function usergroups_delete($usergroup_id = null)
    {
        if (!$this->require_user_permision('DELETE_USERGROUP')) {
            return;
        }
        $usergroup_id = (int) $usergroup_id;
        if ($usergroup_id === 1) {
            $this->response_error(lang('usergroups_cannot_delete_root'), array(), REST_Controller::HTTP_FORBIDDEN, REST_Controller::HTTP_FORBIDDEN);
            return;
        }
        $this->load->model('Admin/UsergroupModel');
        $usergroup = new UsergroupModel();
        if (!$usergroup->find($usergroup_id)) {
            $this->response_error(lang('not_found_error'));
            return;
        }
        $this->db->from('user');
        $this->db->where('usergroup_id', $usergroup_id);
        $this->db->where('status !=', 0);
        if ($this->db->count_all_results() > 0) {
            $this->response_error(lang('usergroups_cannot_delete_has_users'), array(), REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }
        if ($usergroup->delete()) {
            system_logger('usergroups', $usergroup_id, 'deleted', 'A usergroup has been deleted');
            $this->response_ok($usergroup);
            return;
        }
        $this->response_error(lang('unexpected_error'), array(), REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
    }

    public function permissions_get()
    {
        $this->load->model('Admin/UsergroupPermissionsModel');
        $UsergroupPermissions = new UsergroupPermissionsModel();
        $result = $UsergroupPermissions->get_permissions_info(array('usergroup_id' => userdata('usergroup_id')));

        if ($result) {
            $this->response_ok($result);
            return;
        }

        $this->response_error(lang('no_permissions_found'));
    }

    public function allpermissions_get()
    {
        if (!$this->require_user_permision('SELECT_USERGROUPS')) {
            return;
        }
        $this->load->model('Admin/PermissionsModel');
        $Permissions = new PermissionsModel();
        $result = $Permissions->all();

        if ($result) {
            $this->response_ok($result);
            return;
        }

        $this->response_error(lang('no_permissions_found'));
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
        $session_id = (int) userdata('user_id');
        if ((int) $user_id !== $session_id) {
            if (!$this->require_user_permision('SELECT_USERS')) {
                return;
            }
        }

        $user = new UserModel();
        if ($user_id) {
            $result = $user->find($user_id);
            $result = $result ? $user : [];
            if ($result) {
                $this->response_ok($user->get_timeline($user_id));
                return;
            }
        }

        $this->response_error(lang('not_found_error'));
    }

    public function avatar_post()
    {
        $user_id = $this->input->post('user_id');
        $session_id = (int) userdata('user_id');
        if ((int) $user_id !== $session_id) {
            if (!$this->require_user_permision('UPDATE_USER')) {
                return;
            }
        }
        $avatar = $this->input->post('avatar');
        $user = new UserModel();
        $result = false;
        $result_find = $user->find($user_id);
        if ($result_find) {
            $user->user_data['avatar'] = $avatar;
            $result = $user->save();
            if ($result) {
                $this->response_ok($result);
                return;
            }
        }
        $this->response_error(lang('not_found_error'), ['user' => $user], REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
    }

    public function changePassword_post()
    {
        $user_id = $this->input->post('user_id');
        $session_id = (int) userdata('user_id');
        if ((int) $user_id !== $session_id) {
            if (!$this->require_user_permision('UPDATE_USER')) {
                return;
            }
        }
        $currentPassword = $this->input->post('currentPassword');
        $user = new UserModel();
        $result = false;
        $result_find = $user->find($user_id);
        if ($result_find) {
            $this->load->model('Admin/LoginModModel', 'LoginMod');
            $login_data = $this->LoginMod->isLoged($user->username, $currentPassword);
            if ($login_data) {
                $result = $user->update_data(["user_id" => $user_id], ["password" => password_hash($this->input->post('password'), PASSWORD_DEFAULT)]);
                if ($result) {
                    $response = array(
                        'code' => REST_Controller::HTTP_OK,
                        'data' => true,
                        'error_message' => "Password changed correctly",
                    );
                } else {
                    $response = array(
                        'code' => REST_Controller::HTTP_BAD_REQUEST,
                        'data' => array(),
                        'error_message' => "An error has occurred",
                    );
                }
            } else {
                $response = array(
                    'code' => REST_Controller::HTTP_BAD_REQUEST,
                    'data' => array(),
                    'error_message' => "The current password is incorret",
                );
            }
            $this->response($response, $response['code']);
            return;
        }
        $this->response_error(lang('user_not_found_error'), [], REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
    }

    /**
     * JWT userdata from login can predate new grants; reload names from DB.
     *
     * @return void
     */
    protected function refresh_editor_permisions()
    {
        $usergroup_id = userdata('usergroup_id');
        if (!$usergroup_id) {
            return;
        }
        $this->load->model('Admin/UsergroupModel');
        $usergroup = new UsergroupModel();
        $usergroup->usergroup_id = $usergroup_id;
        $this->session->set_userdata('usergroup_permisions', $usergroup->usergroup_permisions());
    }

    protected function require_user_permision($permision)
    {
        if (!function_exists('has_permisions') || !has_permisions($permision)) {
            $this->response_error(
                lang('not_have_permissions'),
                array(),
                REST_Controller::HTTP_FORBIDDEN,
                REST_Controller::HTTP_FORBIDDEN
            );
            return false;
        }
        return true;
    }

    /**
     * Merge posted permission ids with grants the editor cannot change.
     *
     * @param int $usergroup_id
     * @param bool $is_create
     * @return array|false
     */
    protected function build_merged_permision_ids($usergroup_id, $is_create)
    {
        $posted_ids = array();
        $posted = $this->input->post('permissions');
        if (is_array($posted)) {
            foreach ($posted as $row) {
                if (is_array($row) && isset($row['permisions_id'])) {
                    $posted_ids[] = (int) $row['permisions_id'];
                } elseif (is_object($row) && isset($row->permisions_id)) {
                    $posted_ids[] = (int) $row->permisions_id;
                } elseif (is_numeric($row)) {
                    $posted_ids[] = (int) $row;
                }
            }
        }
        $posted_ids = array_values(array_unique($posted_ids));

        $this->load->model('Admin/PermissionsModel');
        $Permissions = new PermissionsModel();
        $catalog_rows = $Permissions->all();
        $catalog = array();
        if ($catalog_rows) {
            foreach ($catalog_rows as $row) {
                $catalog[(int) $row->permisions_id] = $row->permision_name;
            }
        }

        foreach ($posted_ids as $id) {
            if ($id < 1 || !isset($catalog[$id])) {
                $this->response_error(lang('validations_error'), array(), REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
                return false;
            }
        }

        $editor_names = userdata('usergroup_permisions');
        if (!is_array($editor_names)) {
            $editor_names = array();
        }

        $editable_posted = array();
        foreach ($posted_ids as $id) {
            if (in_array($catalog[$id], $editor_names, true)) {
                $editable_posted[] = $id;
            }
        }

        $current_ids = array();
        if (!$is_create && $usergroup_id) {
            $this->load->model('Admin/UsergroupPermissionsModel');
            $junction = new UsergroupPermissionsModel();
            $current = $junction->get_data(array('usergroup_id' => $usergroup_id, 'status' => 1));
            if ($current) {
                foreach ($current as $row) {
                    $current_ids[] = (int) $row->permision_id;
                }
            }
        }

        $untouchable = array();
        foreach ($current_ids as $id) {
            $name = isset($catalog[$id]) ? $catalog[$id] : null;
            if ($name === null || !in_array($name, $editor_names, true)) {
                $untouchable[] = $id;
            }
        }

        $final = array();
        foreach (array_unique(array_merge($untouchable, $editable_posted)) as $id) {
            $id = (int) $id;
            if ($id > 0) {
                $final[] = $id;
            }
        }
        return $final;
    }

    /**
     * @param int $usergroup_id
     * @param array $permision_ids
     * @return void
     */
    protected function replace_usergroup_permisions($usergroup_id, $permision_ids)
    {
        $this->load->model('Admin/UsergroupPermissionsModel');
        $UsergroupPermissions = new UsergroupPermissionsModel();
        $UsergroupPermissions->delete_data(array('usergroup_id' => $usergroup_id));
        foreach ($permision_ids as $permision_id) {
            $row = new UsergroupPermissionsModel();
            $row->permision_id = $permision_id;
            $row->usergroup_id = $usergroup_id;
            $row->status = 1;
            $now = date('Y-m-d H:i:s');
            $row->date_create = $now;
            $row->date_update = $now;
            $row->save();
        }
    }
}