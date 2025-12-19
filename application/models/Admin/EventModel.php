<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class EventModel extends MY_Model
{

    public $primaryKey = 'event_id';
    public $table = "events";
    public $softDelete = true;
    public $computed = ["mainImage" => "mainImage"];
    public $hasOne = [
        'user' => ['user_id', 'Admin/UserModel', 'UserModel'],
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function mainImage()
    {
        $this->load->model('Admin/FileModel');
        $file = new FileModel();
        $file->find($this->mainImage);
        $imagen_file = $file->as_data();
        $imagen_file->{'file_front_path'} = new stdClass();
        $imagen_file->{'file_front_path'} = $file->getFileFrontPath();
        return $imagen_file;
    }


    public function filter_results($collection = [])
    {
        // Cargar users y files de forma optimizada usando loadRelations
        return $this->loadRelations($collection, [
            'user' => ['field' => 'user_id'],
            'file' => ['field' => 'mainImage', 'target' => 'imagen_file']
        ]);
    }

}
