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

    public function index()
    {
        $data['title'] = "Admin | Archivos";
        $data['h1'] = "";

        echo $this->blade->view("admin.archivos.file_explorer", $data);

    }

    public function ajax_get_files()
    {
        $this->output->enable_profiler(false);
        
        $file_path = $this->input->post('path');

        $where = array('file_path' => $file_path, 'status' => 1);
        $result = $this->Files_model->get_data($where);

        $response = array(
            'code' => 200,
            'data' => $result
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));

    }

    public function ajax_get_filter_files()
    {
        $this->output->enable_profiler(false);

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

    public function ajaxfnGetdir()
    {

        $dir = $this->input->post('directorio');
        $directorio = array('_parentdir' => dirname($dir), 'files' => scandir("" . $dir));
        $this->output->set_content_type('application/json')->set_output(json_encode($directorio));
    }

    public function ajax_rename_file()
    {
        $this->output->enable_profiler(false);

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

    public function ajax_featured_file()
    {
        $this->output->enable_profiler(false);

        $file = $this->input->post('file');
        $result = $this->Files_model->update_data(
            array('rand_key' => $file['rand_key']), 
            array('featured' => $file['featured']), 
            'files'
        );

        $response = array(
            'code' => 200,
            'data' => $result,
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));

    }

    public function ajax_move_file()
    {
        $this->output->enable_profiler(false);

        $file = $this->input->post('file');
        $newPath = $this->input->post('newPath');

        $result = $this->Files_model->update_data(
            array('rand_key' => $file['rand_key']), 
            array('file_path' => $newPath), 
            'files'
        );

        $file_name = $file['file_name'] . '.' . $file['file_type'];

        $rename = rename($file['file_path'] . $file_name , $newPath . $file_name);

        $response = array(
            'code' => 200,
            'data' => $result,
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));

    }

    public function ajax_upload_file()
    {
        $this->output->enable_profiler(false);

        $this->load->library('FileUploader');
        $loaderClient = new FileUploader;
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($loaderClient->upload()));

    }

    public function ajax_reload_file_explorer($folder = null)
    {
        $this->output->enable_profiler(false);
        if ($folder != null) {
            $this->Files_model->current_folder = $folder . '/';
        }
        
        $response = array(
            'code' => 200,
            'data' => [
                'result' => $this->Files_model->map_files()
            ],
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));

    }

}
