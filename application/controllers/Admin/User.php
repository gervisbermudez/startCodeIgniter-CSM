<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class User extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('UserMod');
    }

    public function index()
    {
        $data['base_url'] = $this->config->base_url();
        $data['title'] = "Admin | Usuarios";
        $data['h1'] = "Usuarios";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['username'] = $this->session->userdata('username');
        $data['footer_includes'] = array("<script src=" . base_url('resources/components/user.component.js?v=1') . "></script>");

        echo $this->blade->view("admin.user.users", $data);

    }
    public function ver($id = false)
    {

        $this->load->model('ModRelations');
        $this->load->model('EventosMod');
        $this->load->model('ModGallery');
        $this->load->model('ModVideo');
        $this->load->helper('form');

        //get_user
        $data['userdata'] = $this->UserMod->get_user(array('user.id' => $id));
        if ($data['userdata']) {

            //Make the timeline
            $relations = $this->ModRelations->get_relation(array('id_user' => $id), '', array('date', 'DESC'));
            $data['relations'] = $this->get_datarelations($relations);

            //get_datauserstorage
            $datastorage = $this->UserMod->get_datauserstorage(array('id_user' => $id));
            if ($datastorage) {
                foreach ($datastorage as $key => $value) {
                    $data['userdata'][0][$value['_key']] = $value['_value'];
                }
            }

            $data['currentlevel'] = $this->session->userdata('level');
            $data['username'] = $this->session->userdata('username');
            $data['title'] = "Admin | Ver";
            $data['h1'] = '';
            $data['header'] = '';

            //Form Options
            $data['error'] = '';
            $data['nomultiple'] = true;
            $data['load_to'] = 'admin/user/profileimage/' . $data['userdata'][0]['username'] . '/' . $id;
            $data['form'] = $this->load->view('admin/galeria/upload_form', $data, true);

            $data['modalid'] = random_string('alnum', 16);

            //Make the menu options
            $this->load->library('menu');
            $this->menu->char = "'";
            $this->menu->set_ul_properties($properties = array('class' => 'dropdown-content', 'role' => 'menu', 'id' => 'dropdown'));

            $links = array();
            if ($data['currentlevel'] <= $data['userdata'][0]['level']) {
                $links = array('Editar' => array('href' => base_url('admin/user/edit/' . $id)),
                    'Cambiar avatar' => array('href' => '#modal2', 'class' => 'modal-trigger'),
                    'Eliminar' => array('href' => '#' . $data['modalid'],
                        'class' => 'modal-trigger',
                        'data-action-param' => '{"id":"' . $id . '", "table":"user"}',
                        'data-url' => 'admin/user/fn_ajax_delete_data',
                        'data-url-redirect' => 'admin/user/',
                        'data-ajax-action' => 'inactive',
                    ),
                    'Bloquear' => array('href' => '#'));
            }

            if ($id == $this->session->userdata('id')) {
                $links = array('Editar' => array('href' => base_url('admin/user/edit/' . $id)),
                    'Cambiar avatar' => array('href' => '#modal2', 'class' => 'modal-trigger'));

            }

            $data['options'] = $this->menu->get_menu($links);

            //Page includes
            $data['head_includes'] = array('file-input' => link_tag('public/js/fileinput-master/css/fileinput.min.css'));
            $data['footer_includes'] = array('file-input' => '<script src="' . base_url('public/js/fileinput-master/js/fileinput.js') . '"></script>', 'file-input-canvas' => '<script src="' . base_url('public/js/fileinput-master/js/plugins/canvas-to-blob.min.js') . '"></script>');

            // Load the views
            $this->load->view('admin/head', $data);
            $this->load->view('admin/navbar', $data);
            $this->load->view('admin/user/userprofile', $data);
            $this->load->view('admin/footer', $data);

        } else {
            $this->showError('El usuario al que intenta acceder no existe :(');
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
        $this->load->model('UserMod');
        $data['userdata'] = $this->UserMod->get_user(array('user.id' => $id));
        $datastorage = $this->UserMod->get_datauserstorage(array('id_user' => $id));
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
            $data['usergroups'] = $this->UserMod->get_usergroup(array('status' => '1', 'level >' => $this->session->userdata('level')));
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
        $this->load->model('UserMod');
        $this->load->helper('form');
        // set the url base
        $data['base_url'] = $this->config->base_url();
        $data['action'] = 'Admin/User/save/';
        $data['title'] = "Admin | Nuevo Usuario";
        $data['h1'] = "Nuevo Usuario";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['userdata'] = false;
        $data['usergroups'] = $this->UserMod->get_usergroup(array('status' => '1', 'level >' => $this->session->userdata('level')));
        $data['mode'] = 'new';
        // Load the views
        $this->load->view('admin/head', $data);
        $this->load->view('admin/navbar', $data);
        $this->load->view('admin/user/form', $data);
        $this->load->view('admin/footer', $data);
    }
    public function save()
    {

        // Load the model
        $this->load->model('UserMod');
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
            if ($this->UserMod->set_user($data)) {
                $id_user = $this->UserMod->get_user(array('username' => url_title($this->input->post('username'), 'underscore', true)))[0]['id'];
                $datauserstorage = array(
                    array('_key' => 'nombre', '_value' => $this->input->post('nombre'), 'id_user' => $id_user),
                    array('_key' => 'apellido', '_value' => $this->input->post('apellido'), 'id_user' => $id_user),
                    array('_key' => 'direccion', '_value' => $this->input->post('direccion'), 'id_user' => $id_user),
                    array('_key' => 'telefono', '_value' => $this->input->post('telefono'), 'id_user' => $id_user),
                    array('_key' => 'create by', '_value' => $this->session->userdata('username'), 'id_user' => $id_user),
                );
                foreach ($datauserstorage as $key => $data) {
                    $this->UserMod->set_datauserstorage($data);
                }
                if (!file_exists('./img/profile/' . url_title($this->input->post('username'), 'underscore', true))) {
                    mkdir('./img/profile/' . url_title($this->input->post('username'), 'underscore', true));
                }

                redirect('admin/user/ver/' . $id_user);
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
            if ($this->UserMod->update_user($data, $id)) {
                $datauserstorage = array(
                    array('_key' => 'nombre', '_value' => $this->input->post('nombre')),
                    array('_key' => 'apellido', '_value' => $this->input->post('apellido')),
                    array('_key' => 'direccion', '_value' => $this->input->post('direccion')),
                    array('_key' => 'telefono', '_value' => $this->input->post('telefono')),
                    array('_key' => 'create by', '_value' => $this->session->userdata('username')),
                );
                foreach ($datauserstorage as $key => $data) {
                    $this->UserMod->update_datauserstorage($data, $arrayName = array('_key' => $data['_key'], 'id_user' => $id));
                }
                redirect('admin/user/ver/' . $id);
            }
        }
    }

    public function profileimage($dir = '/img', $id_user)
    {
        $this->load->model('UserMod');
        $username = $this->UserMod->get_user(array('user.id' => $id_user))[0]['username'];
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
        $data = array('_key' => 'avatar', 'id_user' => $id_user);
        // delete the current avatar
        $this->UserMod->delete_datauserstorage($data);
        $data = array('_key' => 'avatar', '_value' => $nombre, 'id_user' => $id_user);
        // set the new avatar in the db
        $this->UserMod->set_datauserstorage($data);
        if ($id_user == $this->session->userdata('id')) {
            $this->session->set_userdata('avatar', $nombre);
        }
        redirect('admin/user/ver/' . $id_user);
    }
}
