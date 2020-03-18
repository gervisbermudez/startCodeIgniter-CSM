<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Usuarios extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin/User');
    }

    /**
     * Index page for @route admin/usuarios
     */
    public function index()
    {
        $data['base_url'] = $this->config->base_url();
        $data['title'] = "Admin | Usuarios";
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
        if ($user) {
            $user = (object) $user->first();
            $data['title'] = "Admin | Usuario";
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
                'Editar' => array('href' => base_url('admin/usuarios/edit/' . $user_id)),
                'Cambiar avatar' => array('href' => '#modal2', 'class' => 'modal-trigger'),
                'Eliminar' => array('href' => '#!'),
                'Bloquear' => array('href' => '#!'),
            );

            if ($user_id == $this->session->userdata('user_id')) {
                $links = array(
                    'Editar' => array('href' => base_url('admin/usuarios/edit/' . $user_id)),
                    'Cambiar avatar' => array('href' => '#modal2', 'class' => 'modal-trigger'),
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
            $data['title'] = "Admin | Nuevo Usuario";
            $data['h1'] = "Nuevo Usuario";
            $data['header'] = $this->load->view('admin/header', $data, true);
            $data['mode'] = 'new';
            $data['footer_includes'] = array(
                script('public/js/validateForm.min.js'),
                script('public/js/components/UserNewForm.min.js')
            );
            echo $this->blade->view("admin.user.form", $data);
        } else {
            $this->showError('Usuario no encontrado');
        }
    }

    public function agregar()
    {
        $this->load->model('Admin/Usergroup');
        // set the url base
        $data['action'] = 'Admin/User/save/';
        $data['title'] = "Admin | Nuevo Usuario";
        $data['h1'] = "Nuevo Usuario";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['userdata'] = false;
        $data['mode'] = 'new';
        $data['footer_includes'] = array(
                script('public/js/validateForm.min.js'),
                script('public/js/components/UserNewForm.min.js')
        );
        echo $this->blade->view("admin.user.form", $data);
    }

    public function profileimage($dir = '/img', $user_id)
    {
        $username = $this->User->get_user(array('user.id' => $user_id))[0]['username'];
        // set the url base
        $udir = $dir;
        $dir = str_replace('_', '/', $dir);
        if ($dir === 'root') {
            $dir = dirname('img');
        }
        foreach ($_FILES["imagenes"]["error"] as $clave => $error) {
            if ($error == UPLOAD_ERR_OK) {
                $nombre_tmp = $_FILES["imagenes"]["tmp_name"][$clave];
                $ext = strstr($_FILES["imagenes"]["name"][$clave], '.');
                $nombre = $username . $ext;
                move_uploaded_file($nombre_tmp, 'img/profile/' . $dir . '/' . $nombre);
            }
        }
        $data = array('_key' => 'avatar', 'user_id' => $user_id);
        // delete the current avatar
        $this->User->delete_datauserstorage($data);
        $data = array('_key' => 'avatar', '_value' => $nombre, 'user_id' => $user_id);
        // set the new avatar in the db
        $this->User->set_datauserstorage($data);
        if ($user_id == $this->session->userdata('id')) {
            $this->session->set_userdata('avatar', $nombre);
        }
        redirect('admin/user/ver/' . $user_id);
    }

    public function ajax_get_users()
    {
        $this->output->enable_profiler(false);
        $user_id = $this->input->post('user_id');

        $result = $this->User->get_full_info($user_id);

        $response = array(
            'code' => 200,
            'error_message' => '',
            'data' => $result,
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function ajax_get_usergroups()
    {
        $this->output->enable_profiler(false);

        $this->load->model('Admin/Usergroup');

        $result = $this->Usergroup->where(array('level >' => userdata('level')));

        $response = array(
            'code' => 200,
            'error_message' => '',
            'data' => $result,
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
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

    public function ajax_save_user()
    {
        $this->output->enable_profiler(false);
        $usuario = new User();
        $this->input->post('user_id') ? $usuario->find($this->input->post('user_id')) : false;
        $usuario->username = $this->input->post('username');
        $usuario->password = $this->input->post('password');
        $usuario->email = $this->input->post('email');
        $usuario->lastseen = date("Y-m-d H:i:s");
        $usuario->usergroup_id = $this->input->post('usergroup_id');
        $usuario->status = 1;
        $usuario->user_data = $this->input->post('user_data');

        if ($usuario->save()) {
            $response = array(
                'code' => 200,
                'data' => $usuario,
            );

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } else {

            $error_message = 'Ha ocurrido un error inesperado';
            $response = array(
                'code' => 500,
                'error_message' => $error_message,
                'data' => $_POST,
            );

            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        }

    }
}
