<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Archivos extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($dir = 'root')
    {
        $data['base_url'] = $this->config->base_url();
        $data['udir'] = $dir;
        if ($dir === 'root') {
            $data['dir'] = dirname('img');
            $data['udir'] = $data['dir'];
        } else {
            $data['dir'] = str_replace('_', '/', $dir);
        }
        $data['username'] = $this->session->userdata('username');
        $data['title'] = "Admin | Archivos";
        $data['h1'] = "Archivos";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $this->load->helper('url');
        $data['head_includes'] = array('file-input' => link_tag('public/js/fileinput-master/css/fileinput.min.css'), 'lightbox' => link_tag('public/js/lightbox2-master/dist/css/lightbox.min.css'));

        $data['error'] = '';
        $data['load_to'] = 'Admin/Archivos/subirmultiple/' . $dir;
        $data['form'] = $this->load->view('admin/galeria/upload_form', $data, true);
        $data['footer_includes'] = array('file-input' => '<script src="' . base_url('public/js/fileinput-master/js/fileinput.js') . '"></script>', 'file-input-canvas' => '<script src="' . base_url('public/js/fileinput-master/js/plugins/canvas-to-blob.min.js') . '"></script>',
            'lightbox' => '<script src="' . base_url('public/js/lightbox2-master/dist/js/lightbox.min.js') . '"></script>');
        echo $this->blade->view("admin.archivos.todas", $data);

    }

    public function subirmultiple($dir = '/img')
    {
        // set the url base
        $udir = $dir;
        $dir = str_replace('_', '/', $dir);
        if ($dir === 'root') {
            $dir = dirname('img');
        }
        $data['base_url'] = $this->config->base_url();
        $this->load->helper('strbefore');
        foreach ($_FILES["imagenes"]["error"] as $clave => $error) {
            if ($error == UPLOAD_ERR_OK) {
                $nombre_tmp = $_FILES["imagenes"]["tmp_name"][$clave];
                $ext = strstr($_FILES["imagenes"]["name"][$clave], '.');
                $nombre = $_FILES["imagenes"]["name"][$clave];
                move_uploaded_file($nombre_tmp, $dir . '/' . $nombre);
            }
        }
        redirect('Admin/Archivos/index/' . $udir);
    }

    private function set_upload_options()
    {
        //upload an image options
        $config = array();
        $config['upload_path'] = './img/portfolio/';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = '0';
        $config['overwrite'] = false;

        return $config;
    }

    public function ajaxDeleteFile()
    {

        $archivo = $this->input->post('file');
        $dir = $this->input->post('dir');
        $json = array();
        if (file_exists($dir . '/' . $archivo)) {
            if (rename($dir . '/' . $archivo, './trash/' . $archivo)) {
                $json = array('result' => true, 'message' => 'Movido a la papelera!');
            } else {
                $json = array('result' => false, 'message' => 'Ocurrio un error!');
            }
        } else {
            $json = array('result' => false, 'message' => 'Ocurrio un error!');
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }

    public function fn_empty_folder()
    {
        $folder = $this->input->post('folder');
        $json = array();
        $this->load->helper('file');
        if (delete_files($folder)) {
            $json = array('result' => true, 'message' => 'Operacion Exitosa!');
        } else {
            $json = array('result' => false, 'message' => 'Ocurrio un error!');
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }

    private function uploadmultiple()
    {
        $this->load->library('upload');
        $files = $_FILES;
        $cpt = count($_FILES['userfile']['name']);
        for ($i = 0; $i < $cpt; $i++) {
            $_FILES['userfile']['name'] = $files['userfile']['name'][$i];
            $_FILES['userfile']['type'] = $files['userfile']['type'][$i];
            $_FILES['userfile']['tmp_name'] = $files['userfile']['tmp_name'][$i];
            $_FILES['userfile']['error'] = $files['userfile']['error'][$i];
            $_FILES['userfile']['size'] = $files['userfile']['size'][$i];
            $this->upload->initialize($this->set_upload_options());
            $this->upload->do_upload();
        }
    }

    public function ajaxfnGetdir()
    {

        $dir = $this->input->post('directorio');
        $directorio = array('_parentdir' => dirname($dir), 'files' => scandir("" . $dir));
        $this->output->set_content_type('application/json')->set_output(json_encode($directorio));
    }
}
