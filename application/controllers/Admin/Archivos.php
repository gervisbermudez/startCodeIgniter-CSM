<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Archivos extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin/File');

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
        $result = $this->File->get_data($where);

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

    public function ajax_upload_file()
    {
        $this->output->enable_profiler(false);

        $this->load->library('FileUploader');
        $loaderClient = new FileUploader();
        $result = $loaderClient->upload();
        //persit on database
        if (!isset($result['error'])) {
            $file = new File();
            $insert_array = $file->get_array_save_file($_POST['fileName'], $_POST['curDir']);
            $find_result = $file->find_with(
                [
                "file_path" => $insert_array['file_path'],
                "file_name" => $insert_array['file_name'],
                "file_type" => $insert_array['file_type'],
            ]
            );
            if (!$find_result) {
                $this->File->set_data($insert_array, $this->File->table);
                $result['file_object'] = $this->File->get_data(array('file_id' => $this->db->insert_id()));
            }else{
                $file->date_update = date("Y-m-d H:i:s");
                $file->save();
                $result['file_object'] = $file->as_data();
            }
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));

    }

    public function ajax_get_last_created_file()
    {
        $this->output->enable_profiler(false);

        $result = $this->File->get_data('all', '1', array('file_id', 'DESC'));

        $response = array(
            'code' => 200,
            'data' => $result,
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

}
