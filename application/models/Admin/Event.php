<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Event extends MY_model
{

    public $primaryKey = 'event_id';
    public $table = "events";
    public $softDelete = true;

    public function __construct()
    {
        parent::__construct();
    }

    public function filter_results($collection = [])
    {
        $this->load->model('Admin/User');
        foreach ($collection as $key => &$value) {
            if (isset($value->user_id)) {
                $user = new User();
                $user->find($value->user_id);
                $value->{'user'} = $user;
                $value->{'model_type'} = "page";
            }
        }
        $this->load->model('Admin/File');
        foreach ($collection as $key => &$value) {
            if (isset($value->mainImage) && $value->mainImage) {
                $file = new File();
                $file->find($value->mainImage);
                $value->imagen_file = $file->as_data();
                $value->imagen_file->{'file_front_path'} = new stdClass();
                $value->imagen_file->{'file_front_path'} = $file->getFileFrontPath();
            }
        }
        return $collection;
    }

}
