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
        if ($user_id) {
            if (!$this->can_access_user_profile($user_id)) {
                return;
            }
            $result = $this->User->get_full_info($user_id, array('include_inactive' => true));
            if ($result) {
                $this->response_ok(isset($result[0]) ? $result[0] : $result);
                return;
            }
            $this->response_error(lang('not_found_error'));
            return;
        }

        if (!$this->require_user_permision('SELECT_USERS')) {
            return;
        }

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
        $password = $this->input->post('password');
        $password_rule = 'min_length[8]|max_length[25]|regex_match[/^(?=.*?[0-9])(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[#.?!@$%^&*\-_]).{8,}$/]';
        $config = array(
            array('field' => 'username', 'label' => 'username', 'rules' => 'required|min_length[5]|max_length[18]|alpha_numeric'),
            array('field' => 'email', 'label' => 'email', 'rules' => 'required|valid_email'),
            array('field' => 'usergroup_id', 'label' => 'usergroup_id', 'rules' => 'required|integer|is_natural_no_zero'),
        );
        if (!$is_update) {
            $config[] = array('field' => 'password', 'label' => 'password', 'rules' => 'required|' . $password_rule);
        } elseif ($password) {
            $config[] = array('field' => 'password', 'label' => 'password', 'rules' => $password_rule);
        }
        $form->set_rules($config);
        if (!$form->run()) {
            $this->response_error(lang('new_user_validations_error'), ['errors' => $form->_error_array], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }
        $except_id = $is_update ? (int) $user_id : 0;
        $taken = array();
        if ($this->is_user_field_taken('username', $this->input->post('username'), $except_id)) {
            $taken['username'] = lang('users_form_field_taken');
        }
        if ($this->is_user_field_taken('email', $this->input->post('email'), $except_id)) {
            $taken['email'] = lang('users_form_field_taken');
        }
        if ($taken) {
            $this->response_error(lang('new_user_validations_error'), ['errors' => $taken], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }
        $user = new UserModel();
        if ($is_update) {
            if (!$user->find($user_id)) {
                $this->response_error(lang('user_not_found_error'));
                return;
            }
        }
        $user->username = $this->input->post('username');
        if ($password) {
            $user->password = password_hash($password, PASSWORD_DEFAULT);
        }
        $user->email = $this->input->post('email');
        $user->usergroup_id = $this->input->post('usergroup_id');
        $posted_data = $this->sanitize_user_data($this->input->post('user_data'));
        $current_data = is_array($user->user_data) ? $user->user_data : array();
        $user->user_data = array_merge($current_data, $posted_data);
        if (!$is_update) {
            $user->status = 1;
            $user->lastseen = date("Y-m-d H:i:s");
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
        if (!$user->find($user_id)) {
            $this->response_error(lang('user_not_found_error'));
            return;
        }
        if (!$this->assert_can_mutate_other_user($user)) {
            return;
        }
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
        if (!$this->can_access_user_profile($user_id)) {
            return;
        }
        $user = new UserModel();
        if (!$user_id || !$user->find($user_id)) {
            $this->response_error(lang('not_found_error'));
            return;
        }
        $pag = $this->pagination_from_get();
        $total = $this->User->count_timeline($user_id);
        $items = $this->User->get_timeline($user_id, $pag['per_page'], $pag['offset']);
        $this->response_ok($items, $this->pagination_meta($pag, $total));
    }

    public function logs_get($user_id = null)
    {
        if (!$this->can_access_user_profile($user_id)) {
            return;
        }
        $user = new UserModel();
        if (!$user_id || !$user->find($user_id)) {
            $this->response_error(lang('not_found_error'));
            return;
        }
        $pag = $this->pagination_from_get();
        $uid = (int) $user_id;
        $this->db->from('logger');
        $this->db->where('user_id', $uid);
        $total = (int) $this->db->count_all_results();

        $this->db->select('logger_id, type, type_id, token, comment, date_create');
        $this->db->from('logger');
        $this->db->where('user_id', $uid);
        $this->db->order_by('date_create', 'DESC');
        $this->db->limit($pag['per_page'], $pag['offset']);
        $query = $this->db->get();
        $rows = array();
        if ($query && $query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row['type_link'] = $this->logger_type_link($row['type'], $row['type_id']);
                $rows[] = $row;
            }
        }
        $meta = $this->pagination_meta($pag, $total);
        $meta['logger_enabled'] = ((string) config('SYSTEM_LOGGER') === '1');
        $this->response_ok($rows, $meta);
    }

    public function summary_get($user_id = null)
    {
        if (!$this->can_access_user_profile($user_id)) {
            return;
        }
        $user = new UserModel();
        if (!$user_id || !$user->find($user_id)) {
            $this->response_error(lang('not_found_error'));
            return;
        }
        $uid = (int) $user_id;
        $drafts = array();
        $this->db->select('page_id, title, date_update');
        $this->db->from('page');
        $this->db->where('user_id', $uid);
        $this->db->where('status', 2);
        $this->db->order_by('date_update', 'DESC');
        $this->db->limit(5);
        $drafts_query = $this->db->get();
        if ($drafts_query && $drafts_query->num_rows() > 0) {
            $drafts = $drafts_query->result_array();
        }

        $recent_files = array();
        $this->db->select('file_id, file_name, file_path, file_type, date_create');
        $this->db->from('file');
        $this->db->where('user_id', $uid);
        $this->db->where('status !=', 0);
        $this->db->order_by('file_id', 'DESC');
        $this->db->limit(8);
        $files_query = $this->db->get();
        if ($files_query && $files_query->num_rows() > 0) {
            $recent_files = $files_query->result_array();
        }

        $last_login = null;
        $this->db->select('date_create');
        $this->db->from('logger');
        $this->db->where('user_id', $uid);
        $this->db->where('token', 'login');
        $this->db->order_by('logger_id', 'DESC');
        $this->db->limit(1);
        $login_query = $this->db->get();
        if ($login_query && $login_query->num_rows() > 0) {
            $login_row = $login_query->row_array();
            $last_login = isset($login_row['date_create']) ? $login_row['date_create'] : null;
        }

        $this->load->model('Admin/UsergroupModel');
        $usergroup = new UsergroupModel();
        $permissions = array();
        $usergroup_name = '';
        if ($user->usergroup_id && $usergroup->find($user->usergroup_id)) {
            $usergroup_name = $usergroup->name;
            $perms = $usergroup->usergroup_permisions();
            $permissions = is_array($perms) ? $perms : array();
        }

        $status_ne = array('status !=' => 0);
        $data = array(
            'counts' => array(
                'pages' => $this->count_by_user('page', $uid, $status_ne),
                'collections' => $this->count_by_user('custom_model', $uid, $status_ne),
                'items' => $this->count_by_user('custom_model_content', $uid, $status_ne),
                'files' => $this->count_by_user('file', $uid, $status_ne),
                'drafts' => $this->count_by_user('page', $uid, array('status' => 2)),
                'fragments' => $this->count_by_user('fragmentos', $uid, $status_ne),
                'albums' => $this->count_by_user('album', $uid, $status_ne),
                'events' => $this->count_by_user('events', $uid, $status_ne),
                'menus' => $this->count_by_user('menu', $uid, $status_ne),
                'siteforms' => $this->count_by_user('siteform', $uid, $status_ne),
            ),
            'drafts' => $drafts,
            'recent_files' => $recent_files,
            'permissions' => $permissions,
            'last_login' => $last_login,
            'role' => isset($user->usergroup_id) ? $usergroup_name : '',
            'usergroup_id' => $user->usergroup_id,
            'usergroup_name' => $usergroup_name,
        );
        $this->response_ok($data);
    }

    public function status_post()
    {
        if (!$this->require_user_permision('UPDATE_USER')) {
            return;
        }
        $user_id = $this->input->post('user_id');
        $status = (int) $this->input->post('status');
        if ($status !== 0 && $status !== 1) {
            $this->response_error(lang('new_user_validations_error'), array(), REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }
        $user = new UserModel();
        if (!$user->find($user_id)) {
            $this->response_error(lang('user_not_found_error'));
            return;
        }
        if (!$this->assert_can_mutate_other_user($user)) {
            return;
        }
        $user->status = $status;
        if ($user->save()) {
            $token = $status === 1 ? 'activated' : 'deactivated';
            $comment = $status === 1 ? 'A user has been activated' : 'A user has been deactivated';
            system_logger('users', $user->user_id, $token, $comment);
            $this->response_ok($user);
            return;
        }
        $this->response_error(lang('new_user_unexpected_error'), array(), REST_Controller::HTTP_INTERNAL_SERVER_ERROR, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
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
        $perms = $usergroup->usergroup_permisions();
        if (!is_array($perms)) {
            $perms = array();
        }
        set_cached('usergroup_permisions_' . (int) $usergroup_id, $perms, 3600);
        $this->session->set_userdata('usergroup_permisions', $perms);
    }

    /**
     * True when another active user already has this username or email (status != 0).
     *
     * @param string $field username|email
     * @param string $value
     * @param int    $except_user_id
     * @return bool
     */
    protected function is_user_field_taken($field, $value, $except_user_id = 0)
    {
        $allowed = array('username', 'email');
        if (!in_array($field, $allowed, true)) {
            return true;
        }
        $probe = new UserModel();
        $result = $probe->where(array($field => $value, 'status !=' => 0));
        if (!$result) {
            return false;
        }
        foreach ($result as $row) {
            if ((int) $row->user_id !== (int) $except_user_id) {
                return true;
            }
        }
        return false;
    }

    /**
     * Keep only known profile keys and cap EAV values (user_data._value is varchar 600).
     *
     * @param mixed $data
     * @return array
     */
    protected function sanitize_user_data($data)
    {
        if (!is_array($data)) {
            return array();
        }
        $allowed = array('nombre', 'apellido', 'direccion', 'telefono', 'avatar', 'cargo', 'bio');
        $clean = array();
        foreach ($allowed as $key) {
            if (!array_key_exists($key, $data)) {
                continue;
            }
            $val = is_string($data[$key]) ? trim($data[$key]) : '';
            $val = str_replace(array('"', "\r\n", "\n", "\r"), array("'", ' ', ' ', ' '), $val);
            if (strlen($val) > 600) {
                $val = substr($val, 0, 600);
            }
            $clean[$key] = $val;
        }
        return $clean;
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
     * @param mixed $user_id
     * @return bool
     */
    protected function can_access_user_profile($user_id)
    {
        $user_id = (int) $user_id;
        if ($user_id > 0 && $user_id === (int) userdata('user_id')) {
            return true;
        }
        if (function_exists('has_permisions') && has_permisions('SELECT_USERS')) {
            return true;
        }
        $this->response_error(
            lang('not_have_permissions'),
            array(),
            REST_Controller::HTTP_FORBIDDEN,
            REST_Controller::HTTP_FORBIDDEN
        );
        return false;
    }

    /**
     * @param UserModel $target
     * @return bool
     */
    protected function assert_can_mutate_other_user($target)
    {
        if (!$target || empty($target->user_id)) {
            $this->response_error(lang('user_not_found_error'));
            return false;
        }
        if ((int) $target->user_id === (int) userdata('user_id')) {
            $this->response_error(
                lang('not_have_permissions'),
                array(),
                REST_Controller::HTTP_FORBIDDEN,
                REST_Controller::HTTP_FORBIDDEN
            );
            return false;
        }
        if ((int) $target->usergroup_id < (int) userdata('usergroup_id')) {
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
     * @return array
     */
    protected function pagination_from_get()
    {
        $current_page = (int) $this->get('page');
        if ($current_page < 1) {
            $current_page = 1;
        }
        $per_page = (int) $this->get('per_page');
        if ($per_page < 1) {
            $per_page = 20;
        }
        if ($per_page > 100) {
            $per_page = 100;
        }
        return array(
            'current_page' => $current_page,
            'per_page' => $per_page,
            'offset' => ($current_page - 1) * $per_page,
        );
    }

    /**
     * @param array $pag
     * @param int $total_rows
     * @return array
     */
    protected function pagination_meta($pag, $total_rows)
    {
        $total_rows = (int) $total_rows;
        $per_page = isset($pag['per_page']) ? (int) $pag['per_page'] : 20;
        $current_page = isset($pag['current_page']) ? (int) $pag['current_page'] : 1;
        $total_pages = $per_page > 0 ? (int) ceil($total_rows / $per_page) : 0;
        return array(
            'current_page' => $current_page,
            'per_page' => $per_page,
            'total_rows' => $total_rows,
            'offset' => isset($pag['offset']) ? (int) $pag['offset'] : 0,
            'total_pages' => $total_pages,
            'first_page' => 1,
            'last_page' => $total_pages,
            'next_page' => $current_page + 1,
            'prev_page' => $current_page - 1,
        );
    }

    /**
     * @param string $table
     * @param int $user_id
     * @param array $where
     * @return int
     */
    protected function count_by_user($table, $user_id, $where = array())
    {
        $allowed = array(
            'page',
            'custom_model',
            'custom_model_content',
            'file',
            'fragmentos',
            'album',
            'events',
            'menu',
            'siteform',
        );
        if (!in_array($table, $allowed, true)) {
            return 0;
        }
        $this->db->from($table);
        $this->db->where('user_id', (int) $user_id);
        if (is_array($where)) {
            foreach ($where as $field => $value) {
                $this->db->where($field, $value);
            }
        }
        return (int) $this->db->count_all_results();
    }

    /**
     * @param string $type
     * @param mixed $type_id
     * @return string|null
     */
    protected function logger_type_link($type, $type_id)
    {
        $type_id = (int) $type_id;
        switch ($type) {
            case 'pages':
                return 'admin/pages/editar/' . $type_id;
            case 'users':
                return 'admin/users/ver/' . $type_id;
            case 'custom_model':
                return 'admin/custommodels/items/' . $type_id;
            case 'custom_model_content':
                return 'admin/custommodels/';
            case 'config':
            case 'site_config':
                return 'admin/configuracion/';
            default:
                return null;
        }
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
        invalidate_usergroup_permisions_cache($usergroup_id);
    }
}