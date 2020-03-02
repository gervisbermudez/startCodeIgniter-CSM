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
                'Editar' => array('href' => base_url('admin/user/edit/' . $user_id)),
                'Cambiar avatar' => array('href' => '#modal2', 'class' => 'modal-trigger'),
                'Eliminar' => array('href' => '#!'),
                'Bloquear' => array('href' => '#!'),
            );

            if ($user_id == $this->session->userdata('user_id')) {
                $links = array(
                    'Editar' => array('href' => base_url('admin/user/edit/' . $user_id)),
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

    private function get_datarelations($relations)
    {
        $dat = false;
        if ($relations) {
            foreach ($relations as $key => $row) {
                $id = $row['id_row'];
                $table = $row['tablename'];
                switch ($table) {
                    case 'album':
                        $album = $this->ModGallery->get_album($data = array('id' => $id), $limit = '', $order = array('id', 'DESC'));
                        $album[0]['tiperel'] = 'album';
                        $dat[] = $album[0];
                        break;
                    case 'eventos':
                        $evento = $this->EventosMod->get_event($data = array('id' => $id), $limit = '', $order = array('id', 'DESC'));
                        $evento[0]['tiperel'] = 'evento';
                        $dat[] = $evento[0];
                        break;
                    case 'video':
                        $video = $this->ModVideo->get_video($data = array('id' => $id), $limit = '', $order = array('id', 'DESC'));
                        $video[0]['tiperel'] = 'video';
                        $dat[] = $video[0];
                        break;
                    default:

                        break;
                }
            }
        }
        return $dat;
    }

    public function edit($id)
    {

        // Load the model
        $data['userdata'] = $this->User->get_user(array('user.id' => $id));
        $datastorage = $this->User->get_datauserstorage(array('user_id' => $id));
        if ($datastorage) {
            # code...
            foreach ($datastorage as $key => $value) {
                $data['userdata'][0][$value['_key']] = $value['_value'];
            }
        }
        if ($data['userdata']) {
            $this->load->helper('form');

            $data['title'] = "Admin | Editar";
            $data['h1'] = "Editar Usuario";
            $data['header'] = $this->load->view('admin/header', $data, true);
            $data['action'] = 'Admin/User/save/';
            $data['usergroups'] = $this->User->get_usergroup(array('status' => '1', 'level >' => $this->session->userdata('level')));
            $data['mode'] = 'edit';
            $this->load->view('admin/head', $data);
            $this->load->view('admin/navbar', $data);
            $this->load->view('admin/user/form', $data);
            $this->load->view('admin/footer', $data);
        } else {
            $this->showError();
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
        $data['usergroups'] = $this->Usergroup->where(array('status' => '1', 'level >' => $this->session->userdata('level')));
        $data['mode'] = 'new';
        $data['footer_includes'] = array('<script src="' . base_url('public/js/components/UserNewForm.min.js') . '"></script>');
        echo $this->blade->view("admin.user.form", $data);
    }

    public function save()
    {

        // Load the model
        $mode = $this->input->post('mode');
        $query = false;
        if ($mode === "new") {
            $status = "0";
            if ($this->input->post('status') == "on") {
                $status = "1";
            }
            $data = array(
                'username' => url_title($this->input->post('username'), 'underscore', true),
                'password' => $this->input->post('password'),
                'email' => $this->input->post('email'),
                'usergroup' => $this->input->post('usergroup'),
                'status' => $status,
            );
            $date = new DateTime();
            $data['lastseen'] = $date->format('Y-m-d H:i:s');
            if ($this->User->set_user($data)) {
                $user_id = $this->User->get_user(array('username' => url_title($this->input->post('username'), 'underscore', true)))[0]['id'];
                $user_data = array(
                    array('_key' => 'nombre', '_value' => $this->input->post('nombre'), 'user_id' => $user_id),
                    array('_key' => 'apellido', '_value' => $this->input->post('apellido'), 'user_id' => $user_id),
                    array('_key' => 'direccion', '_value' => $this->input->post('direccion'), 'user_id' => $user_id),
                    array('_key' => 'telefono', '_value' => $this->input->post('telefono'), 'user_id' => $user_id),
                    array('_key' => 'create by', '_value' => $this->session->userdata('username'), 'user_id' => $user_id),
                );
                foreach ($user_data as $key => $data) {
                    $this->User->set_datauserstorage($data);
                }
                if (!file_exists('./img/profile/' . url_title($this->input->post('username'), 'underscore', true))) {
                    mkdir('./img/profile/' . url_title($this->input->post('username'), 'underscore', true));
                }

                redirect('admin/user/ver/' . $user_id);
            } else {
                $this->showError();
            }
        } else {
            $id = $this->input->post('id');
            $status = "0";
            if ($this->input->post('status') == "on") {
                $status = "1";
            }
            $data = array(
                'username' => url_title($this->input->post('username'), 'underscore', true),
                'password' => $this->input->post('password'),
                'email' => $this->input->post('email'),
                'usergroup' => $this->input->post('usergroup'),
                'status' => $status,
            );
            if ($this->User->update_user($data, $id)) {
                $datauserstorage = array(
                    array('_key' => 'nombre', '_value' => $this->input->post('nombre')),
                    array('_key' => 'apellido', '_value' => $this->input->post('apellido')),
                    array('_key' => 'direccion', '_value' => $this->input->post('direccion')),
                    array('_key' => 'telefono', '_value' => $this->input->post('telefono')),
                    array('_key' => 'create by', '_value' => $this->session->userdata('username')),
                );
                foreach ($datauserstorage as $key => $data) {
                    $this->User->update_datauserstorage($data, $arrayName = array('_key' => $data['_key'], 'user_id' => $id));
                }
                redirect('admin/user/ver/' . $id);
            }
        }
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

    public function ajax_get_users($user_id = false)
    {
        $this->output->enable_profiler(false);
        $result = $data['forms_list'] = $this->User->get_full_info($user_id);

        $response = array(
            'code' => 200,
            'data' => $result,
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }
}
