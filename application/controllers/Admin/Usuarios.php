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
        if ($user && $user_id) {
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
                script('public/js/components/UserNewForm.min.js'),
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

}
