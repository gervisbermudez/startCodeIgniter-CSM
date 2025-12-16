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
        $data['base_url'] = $this->config->base_url();
        $data['title'] = ADMIN_TITLE . " | Usuarios";
        $data['h1'] = "Usuarios";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['username'] = $this->session->userdata('username');
        $data['footer_includes'] = array("<script src=" . base_url('public/js/components/userComponent.min.js?v=1') . "></script>");

        echo $this->blade->view("admin.user.users", $data);
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
        $data['userdata'] = $this->User->find($id);
        if ($data['userdata']) {
            $data['action'] = 'Admin/User/save/';
            $data['title'] = ADMIN_TITLE . " | Editar Usuario";
            $data['h1'] = "Editar Usuario";
            $data['header'] = $this->load->view('admin/header', $data, true);
            $data['mode'] = 'new';
            $data['footer_includes'] = array(
                script('public/js/validateForm.min.js'),
                script('public/js/components/UserNewForm.min.js'),
            );
            echo $this->blade->view("admin.user.form", $data);
        } else {
            $this->showError('Usuario no encontrado');
        }
    }

    public function changePassword($id)
    {
        $data['userdata'] = $this->User->find($id);
        if ($data['userdata']) {
            $data['action'] = 'Admin/User/save/';
            $data['title'] = ADMIN_TITLE . " | Cambiar Password";
            $data['h1'] = "Cambiar Password";
            $data['header'] = $this->load->view('admin/header', $data, true);
            $data['mode'] = 'new';
            echo $this->blade->view("admin.user.changepassword", $data);
        } else {
            $this->showError('Usuario no encontrado');
        }
    }

    public function agregar()
    {
        $this->load->model('Admin/Usergroup');
        // set the url base
        $data['action'] = 'Admin/User/save/';
        $data['title'] = ADMIN_TITLE . " | Nuevo Usuario";
        $data['h1'] = "Nuevo Usuario";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['userdata'] = false;
        $data['mode'] = 'new';
        $data['footer_includes'] = array(
            script('public/js/validateForm.min.js'),
            script('public/js/components/UserNewForm.min.js'),
        );
        echo $this->blade->view("admin.user.form", $data);
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
