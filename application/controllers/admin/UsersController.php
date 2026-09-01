<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class UsersController extends MY_Controller
{

    public $routes_permisions = [
        "index" => [
            "patern" => '/^admin\/users\/?$/',
            "required_permissions" => ["SELECT_USERS"],
            "conditions" => [],
        ],
        "ver" => [
            "patern" => '/^admin\/users\/ver\/(\d+)/',
            "required_permissions" => ["SELECT_USERS"],
            "conditions" => ["check_self_permissions"],
        ],
        "edit" => [
            "patern" => '/^admin\/users\/edit\/(\d+)/',
            "required_permissions" => ["UPDATE_USER"],
            "conditions" => ["check_self_permissions"],
        ],
        "changePassword" => [
            "patern" => '/^admin\/users\/changePassword\/(\d+)/',
            "required_permissions" => ["UPDATE_USER"],
            "conditions" => ["check_self_permissions"],
        ],
        "agregar" => [
            "patern" => '/^admin\/users\/add\/?$/',
            "required_permissions" => ["CREATE_USER"],
            "conditions" => [],
        ],
        "usergroups" => [
            "patern" => '/^admin\/users\/usergroups\/?$/',
            "required_permissions" => ["SELECT_USERGROUPS"],
            "conditions" => [],
        ],
        "editGroup" => [
            "patern" => '/^admin\/users\/editGroup\/(\d+)/',
            "required_permissions" => ["UPDATE_USERGROUP"],
            "conditions" => [],
        ],
        "newGroup" => [
            "patern" => '/^admin\/users\/newGroup\/?$/',
            "required_permissions" => ["CREATE_USERGROUP"],
            "conditions" => [],
        ],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('admin/users');
        $this->check_permisions();
        $this->load->model('Admin/UserModel', 'User');
    }

    public function index()
    {
        $this->renderAdminView('admin.user.users', lang('menu_users'), lang('menu_users'));
    }

    public function ver($user_id = false)
    {
        //get_user
        $user = $this->User->get_full_info($user_id);
        if ($user && $user_id) {
            $user = (object) $user->first();
            $user->user_data = $this->profile_user_data($user);
            $full_name = trim($user->user_data->nombre . ' ' . $user->user_data->apellido);
            $data['title'] = ADMIN_TITLE . " | " . ($full_name !== '' ? $full_name : $user->username);
            $data['user'] = $user;
            //Make the menu options
            $data['dropdown_id'] = 'dropdown' . random_string('alnum', 16);
            $this->load->library('menu');
            $this->menu->char = "'";
            $this->menu->set_ul_properties(
                array(
                    'class' => 'dropdown-content',
                    'role' => 'menu',
                    'id' => $data['dropdown_id'],
                )
            );

            $links = array(
                lang('btn_edit') => array('href' => base_url('admin/users/edit/' . $user_id)),
                'Change avatar' => array('href' => '#folderSelector', 'class' => 'modal-trigger'),
                lang('btn_delete') => array('href' => '#!'),
                'Block' => array('href' => '#!'),
            );

            if ($user_id == userdata('user_id')) {
                $links = array(
                    lang('btn_edit') => array('href' => base_url('admin/users/edit/' . $user_id)),
                    'Change Password' => array('href' => base_url('admin/users/changePassword/' . $user_id)),
                    'Change avatar' => array('href' => '#folderSelector', 'class' => 'modal-trigger'),
                );
            }

            $data['dropdown_menu'] = $this->menu->get_menu($links);
            //Page includes
            //$data['head_includes'] = array('file-input' => link_tag('public/js/fileinput-master/css/fileinput.min.css'));
            //$data['footer_includes'] = array('file-input' => '<script src="' . base_url('public/js/fileinput-master/js/fileinput.js') . '"></script>', 'file-input-canvas' => '<script src="' . base_url('public/js/fileinput-master/js/plugins/canvas-to-blob.min.js') . '"></script>');

            // Load the views
            echo $this->blade->view("admin.user.user_profile", $data);
        } else {
            $this->error404();
        }
    }

    public function edit($id)
    {
        $user = $this->findOrFail(new UserModel(), $id, 'User not found');
        
        $this->renderAdminView('admin.user.form', lang('users_form_edit'), lang('users_form_edit'), [
            'userdata' => $user,
            'action' => 'Admin/User/save/',
            'mode' => 'new',
        ]);
    }

    public function changePassword($id)
    {
        $user = $this->findOrFail(new UserModel(), $id, 'User not found');
        
        $this->renderAdminView('admin.user.change_password', 'Change Password', 'Change Password', [
            'userdata' => $user,
            'action' => 'Admin/User/save/',
            'mode' => 'new'
        ]);
    }

    public function agregar()
    {
        $this->load->model('Admin/UsergroupModel');
        
        $this->renderAdminView('admin.user.form', lang('users_form_new'), lang('users_form_new'), [
            'action' => 'Admin/User/save/',
            'userdata' => false,
            'mode' => 'new',
        ]);
    }

    /**
     * Profile Blade reads EAV keys that optional signup may omit.
     *
     * @param object $user
     * @return object
     */
    protected function profile_user_data($user)
    {
        $ud = isset($user->user_data) ? $user->user_data : null;
        if (is_array($ud)) {
            $ud = (object) $ud;
        }
        if (!is_object($ud)) {
            $ud = new stdClass();
        }
        foreach (array('nombre', 'apellido', 'telefono', 'direccion', 'avatar', 'cargo', 'bio') as $key) {
            if (!isset($ud->{$key})) {
                $ud->{$key} = '';
            }
        }
        return $ud;
    }

    public function ajax_check_field()
    {
        $this->output->enable_profiler(false);
        $field = $this->input->post('field');
        $value = $this->input->post('value');
        $user_id = (int) $this->input->post('user_id');
        $allowed = array('username', 'email');
        $available = true;
        if (!in_array($field, $allowed, true)) {
            $available = false;
        } else {
            $result = $this->User->where(array($field => $value, 'status !=' => 0));
            if ($result) {
                foreach ($result as $row) {
                    if (!$user_id || (int) $row->user_id !== $user_id) {
                        $available = false;
                        break;
                    }
                }
            }
        }

        $response = array(
            'code' => 200,
            'error_message' => '',
            'data' => $available,
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function usergroups()
    {
        $this->renderAdminView('admin.user.user_groups', lang('menu_usergroups'), lang('menu_usergroups'));
    }

    public function editGroup($usergroup_id)
    {
        $this->load->model('Admin/UsergroupModel');
        $this->findOrFail(new UsergroupModel(), $usergroup_id, lang('not_found_error'));
        $this->renderAdminView('admin.user.user_groups_permissions', lang('usergroups_edit'), lang('usergroups_edit'), [
            'editMode' => 'edit',
            'usergroup_id' => $usergroup_id,
        ]);
    }

    public function newGroup()
    {
        $this->renderAdminView('admin.user.user_groups_permissions', lang('usergroups_new'), lang('usergroups_new'), [
            'editMode' => 'new',
            'usergroup_id' => false,
        ]);
    }

    public function permissions()
    {
        $data['title'] = ADMIN_TITLE . " | permissions";
        $data['h1'] = "permissions";
        $data['header'] = $this->load->view('admin/header', $data, true);
        echo $this->blade->view("admin.user.permissions", $data);
    }

}
