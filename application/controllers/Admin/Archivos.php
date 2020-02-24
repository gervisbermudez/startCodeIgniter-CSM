<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Archivos extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Files_model');
    }

    public function map($folder = null)
    {
        if ($folder != null) {
            $this->Files_model->current_folder = $folder . '/';
        }
        $this->Files_model->map_files();
    }

    public function index()
    {
        $data['title'] = "Admin | Archivos";
        $data['h1'] = "";

        echo $this->blade->view("admin.archivos.file_explorer", $data);

    }

    public function ajax_get_files()
    {
        $file_path = $this->input->post('path');

        $result = $this->Files_model->get_data(array('file_path' => $file_path, 'status' => 1), 'files', '', '');

        $response = array(
            'code' => 200,
            'data' => $result,
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));

    }

    public function ajax_get_filter_files()
    {
        $filter_name = $this->input->post('filter_name');
        $filter_value = $this->input->post('filter_value');

        $result = $this->Files_model->get_filter_files($filter_name, $filter_value);

        $response = array(
            'code' => 200,
            'data' => $result,
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));

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

    public function ajax_rename_file()
    {
        $file = $this->input->post('file');
        $result = $this->Files_model->update_data(array('rand_key' => $file['rand_key']), array('file_name' => $file['new_name']), 'files');

        if ($result) {
            rename(
                $file['file_path'] . $file['file_name'] . '.' . $file['file_type'],
                $file['file_path'] . $file['new_name'] . '.' . $file['file_type'],
            );
        }

        $response = array(
            'code' => 200,
            'data' => $result,
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));

    }
}
