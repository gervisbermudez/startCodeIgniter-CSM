<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Users extends MY_Controller
{

    public $routes_permisions = [
        "index" => [ 
            "patern" => '/admin\/usuarios/',
            "required_permissions" => ["SELECT_USERS"],
            "conditions" => [],
        ],
        "ver" => [ 
            "patern" => '/admin\/usuarios\/ver\/(\d+)/',
            "required_permissions" => ["SELECT_USERS"],
            "conditions" => ["check_self_permissions"],
        ],
        "edit" => [ 
            "patern" => '/admin\/usuarios\/edit\/(\d+)/',
            "required_permissions" => ["UPDATE_USER"],
            "conditions" => ["check_self_permissions"],
        ],
        "changePassword" => [ 
            "patern" => '/admin\/usuarios\/changePassword\/(\d+)/',
            "required_permissions" => ["UPDATE_USER"],
            "conditions" => ["check_self_permissions"],
        ],
        "agregar" => [ 
            "patern" => '/admin\/usuarios\/agregar/',
            "required_permissions" => ["CREATE_USER"],
            "conditions" => [],
        ],
        "usergroups" => [ 
            "patern" => '/admin\/usuarios\/usergroups/',
            "required_permissions" => ["SELECT_USERS"],
            "conditions" => [],
        ],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->check_permisions();
        $this->load->model('Admin/User');
    }

    public function index()
    {
        $this->renderAdminView('admin.user.users', 'Usuarios', 'Usuarios', [
            'footer_includes' => [
                "<script src=" . base_url('resources/components/UserComponent.js?v=' . ADMIN_VERSION) . "></script>"
            ]
        ]);
    }

    public function ver($user_id = false)
    {
        //get_user
        $user = $this->User->get_full_info($user_id);
        if ($user && $user_id) {
            $user = (object) $user->first();
            $data['title'] = ADMIN_TITLE . " | " . $user->user_data->nombre . ' ' . $user->user_data->apellido;
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
                'Editar' => array('href' => base_url('admin/users/edit/' . $user_id)),
                'Cambiar avatar' => array('href' => '#folderSelector', 'class' => 'modal-trigger'),
                'Eliminar' => array('href' => '#!'),
                'Bloquear' => array('href' => '#!'),
            );

            if ($user_id == userdata('user_id')) {
                $links = array(
                    'Editar' => array('href' => base_url('admin/users/edit/' . $user_id)),
                    'Cambiar Contraseña' => array('href' => base_url('admin/users/changePassword/' . $user_id)),
                    'Cambiar avatar' => array('href' => '#folderSelector', 'class' => 'modal-trigger'),
                );
            }

            $data['dropdown_menu'] = $this->menu->get_menu($links);
            //Page includes
            //$data['head_includes'] = array('file-input' => link_tag('public/js/fileinput-master/css/fileinput.min.css'));
            //$data['footer_includes'] = array('file-input' => '<script src="' . base_url('public/js/fileinput-master/js/fileinput.js') . '"></script>', 'file-input-canvas' => '<script src="' . base_url('public/js/fileinput-master/js/plugins/canvas-to-blob.min.js') . '"></script>');

            // Load the views
            echo $this->blade->view("admin.user.userprofile", $data);
        } else {
            $this->error404();
        }
    }

    public function edit($id)
    {
        $user = $this->findOrFail(new User(), $id, 'Usuario no encontrado');
        
        $this->renderAdminView('admin.user.form', 'Editar Usuario', 'Editar Usuario', [
            'userdata' => $user,
            'action' => 'Admin/User/save/',
            'mode' => 'new',
            'footer_includes' => [
                script('resources/js/validateForm.js'),
                script('resources/components/UserNewForm.js'),
            ]
        ]);
    }

    public function changePassword($id)
    {
        $user = $this->findOrFail(new User(), $id, 'Usuario no encontrado');
        
        $this->renderAdminView('admin.user.changepassword', 'Cambiar Password', 'Cambiar Password', [
            'userdata' => $user,
            'action' => 'Admin/User/save/',
            'mode' => 'new'
        ]);
    }

    public function agregar()
    {
        $this->load->model('Admin/Usergroup');
        
        $this->renderAdminView('admin.user.form', 'Nuevo Usuario', 'Nuevo Usuario', [
            'action' => 'Admin/User/save/',
            'userdata' => false,
            'mode' => 'new',
            'footer_includes' => [
                script('resources/js/validateForm.js'),
                script('resources/components/UserNewForm.js'),
            ]
        ]);
    }

    public function ajax_check_field()
    {
        $this->output->enable_profiler(false);
        $field = $this->input->post('field');
        $value = $this->input->post('value');
        $result = $this->User->where(array($field => $value));

        $response = array(
            'code' => 200,
            'error_message' => '',
            'data' => $result ? false : true,
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function usergroups()
    {
        $data['title'] = ADMIN_TITLE . " | User Groups";
        $data['h1'] = "User Groups";
        $data['header'] = $this->load->view('admin/header', $data, true);
        echo $this->blade->view("admin.user.usergroups", $data);
    }

    public function editGroup($usergroup_id)
    {
        
        $this->output->enable_profiler(false);

        $this->load->model('Admin/Usergroup');
        $usergroup = new Usergroup();
        $result = $usergroup->find($usergroup_id);
        if ($result) {
            $data['action'] = 'Admin/User/save/';
            $data['title'] = ADMIN_TITLE . " | Editar Permisos";
            $data['h1'] = "Editar Permisos";
            $data['header'] = $this->load->view('admin/header', $data, true);
            $data['editMode'] = 'edit';
            $data['usergroup_id'] = $usergroup_id;
           
            echo $this->blade->view("admin.user.usergroups_permissions", $data);
        } else {
            $this->showError('Usergroup no encontrado');
        }
    }

    public function newGroup($usergroup_id)
    {
        
        $this->output->enable_profiler(false);

        $this->load->model('Admin/Usergroup');
        $usergroup = new Usergroup();
        $result = $usergroup->find($usergroup_id);
        if ($result) {
            $data['action'] = 'Admin/User/save/';
            $data['title'] = ADMIN_TITLE . " | Nuevo Grupo";
            $data['h1'] = "Nuevo Grupo";
            $data['header'] = $this->load->view('admin/header', $data, true);
            $data['editMode'] = 'new';
            $data['usergroup_id'] = null;
           
            echo $this->blade->view("admin.user.usergroups_permissions", $data);
        } else {
            $this->showError('Usergroup no encontrado');
        }
    }

    public function permissions()
    {
        $data['title'] = ADMIN_TITLE . " | permissions";
        $data['h1'] = "permissions";
        $data['header'] = $this->load->view('admin/header', $data, true);
        echo $this->blade->view("admin.user.permissions", $data);
    }

}
