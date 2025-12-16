<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Files extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin/File');
        $this->load->model('Admin/File_activity');
    }

    public function index()
    {
        $data['title'] = ADMIN_TITLE . " | Archivos";
        $data['h1'] = "";

        echo $this->blade->view("admin.archivos.file_explorer", $data);

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
            $filenameParts = explode(".", $_POST['fileName']);

            $insert_array = $file->get_array_save_file(slugify($filenameParts[0]) . '-' . date("Y-m-d-His") . '.' . $filenameParts[1], $_POST['curDir'] . date("Y-m-d/"));
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
                $file_activity = new File_activity();
                $file_activity->file_id = $result['file_object'][0]->file_id;
                $file_activity->user_id = userdata('user_id');
                $file_activity->action = "upload";
                $file_activity->description = "The file was upload";
                $file_activity->date_create = date("Y-m-d H:i:s");
                $file_activity->status = 1;
                $file_activity->save();

            } else {
                $file->date_update = date("Y-m-d H:i:s");
                $result['file_object'] = $file->as_data();
            }
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));

    }

}