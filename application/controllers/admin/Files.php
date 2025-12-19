<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Files extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin/File');
        $this->load->model('Admin/FileActivity');
    }

    public function index()
    {
        $this->renderAdminView('admin.files.file_explorer', 'Archivos', '');
    }

    public function ajax_upload_file()
    {
        $this->output->enable_profiler(false);

        $result = $this->uploadFile();
        
        if (!isset($result['error'])) {
            $result = $this->persistFileToDatabase($result);
        }

        $this->sendJsonResponse($result);
    }

    /**
     * Sube el archivo usando la biblioteca FileUploader
     * 
     * @return array Resultado de la subida
     */
    private function uploadFile()
    {
        $this->load->library('FileUploader');
        $loaderClient = new FileUploader();
        return $loaderClient->upload();
    }

    /**
     * Persiste la información del archivo en la base de datos
     * 
     * @param array $result Resultado de la subida
     * @return array Resultado actualizado con el objeto del archivo
     */
    private function persistFileToDatabase($result)
    {
        $file = new File();
        $fileName = $this->generateFileName($_POST['fileName']);
        $filePath = $this->generateFilePath($_POST['curDir']);
        
        $insert_array = $file->get_array_save_file($fileName, $filePath);
        $existingFile = $this->findExistingFile($file, $insert_array);

        if (!$existingFile) {
            $result['file_object'] = $this->createNewFileRecord($insert_array);
            $this->logFileActivity($result['file_object'][0]->file_id, 'upload', 'The file was upload');
        } else {
            $file->date_update = date("Y-m-d H:i:s");
            $result['file_object'] = $file->as_data();
        }

        return $result;
    }

    /**
     * Genera el nombre del archivo con timestamp
     * 
     * @param string $fileName Nombre original del archivo
     * @return string Nombre del archivo procesado
     */
    private function generateFileName($fileName)
    {
        $filenameParts = explode(".", $fileName);
        return slugify($filenameParts[0]) . '-' . date("Y-m-d-His") . '.' . $filenameParts[1];
    }

    /**
     * Genera la ruta del archivo
     * 
     * @param string $currentDir Directorio actual
     * @return string Ruta del archivo
     */
    private function generateFilePath($currentDir)
    {
        return rtrim($currentDir, '/') . '/' . date("Y-m-d");
    }

    /**
     * Busca si el archivo ya existe en la base de datos
     * 
     * @param File $file Instancia del modelo File
     * @param array $insert_array Datos del archivo a buscar
     * @return mixed Resultado de la búsqueda
     */
    private function findExistingFile($file, $insert_array)
    {
        return $file->find_with([
            "file_path" => $insert_array['file_path'],
            "file_name" => $insert_array['file_name'],
            "file_type" => $insert_array['file_type'],
        ]);
    }

    /**
     * Crea un nuevo registro de archivo en la base de datos
     * 
     * @param array $insert_array Datos del archivo
     * @return array Objeto del archivo creado
     */
    private function createNewFileRecord($insert_array)
    {
        $this->File->set_data($insert_array, $this->File->table);
        return $this->File->get_data(['file_id' => $this->db->insert_id()]);
    }

    /**
     * Registra la actividad del archivo
     * 
     * @param int $file_id ID del archivo
     * @param string $action Acción realizada
     * @param string $description Descripción de la acción
     * @return void
     */
    private function logFileActivity($file_id, $action, $description)
    {
        $file_activity = new FileActivity();
        $file_activity->file_id = $file_id;
        $file_activity->user_id = userdata('user_id');
        $file_activity->action = $action;
        $file_activity->description = $description;
        $file_activity->date_create = date("Y-m-d H:i:s");
        $file_activity->status = 1;
        $file_activity->save();
    }

    /**
     * Envía una respuesta JSON
     * 
     * @param array $data Datos a enviar
     * @return void
     */
    private function sendJsonResponse($data)
    {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

}