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
        $data['title'] = ADMIN_TITLE . " | Archivos";
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
            'data' => $result,
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
                $file['file_path'] . $file['new_name'] . '.' . $file['file_type']
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

        $rename = rename($file['file_path'] . $file_name, $newPath . $file_name);

        $response = array(
            'code' => 200,
            'data' => $result,
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));

    }

    public function ajax_copy_file()
    {
        $this->output->enable_profiler(false);

        $file = $this->input->post('file');
        $newPath = $this->input->post('newPath');
        $file_name = $file['file_name'] . '.' . $file['file_type'];
        $rename = copy($file['file_path'] . $file_name, $newPath . $file_name);
        $result = $rename;
        if ($rename) {
            $insert_array = $this->Files_model->get_array_save_file($file_name, $newPath);
            $result = $this->Files_model->set_data($insert_array, $this->Files_model->table);
        }

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
        $loaderClient = new FileUploader();
        $result = $loaderClient->upload();
        //persit on database
        if (!isset($result['error'])) {
            $insert_array = $this->Files_model->get_array_save_file($_POST['fileName'], $_POST['curDir']);
            $this->Files_model->set_data($insert_array, $this->Files_model->table);
            $result['file_object'] = $this->Files_model->get_data(array('file_id' => $this->db->insert_id()));
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));

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
                'result' => $this->Files_model->map_files(),
            ],
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));

    }

    public function ajax_get_last_created_file()
    {
        $this->output->enable_profiler(false);

        $result = $this->Files_model->get_data('all', '1', array('file_id', 'DESC'));

        $response = array(
            'code' => 200,
            'data' => $result,
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

}
