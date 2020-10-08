<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Album_items extends MY_model {

    public $primaryKey = 'album_item_id';
    
    public $hasOne = [
        'file' => ['file_id', 'Admin/File', 'File'],
    ];

	public function __construct()
	{
		parent::__construct();
	}

    public function filter_results($collection = [])
    {
        $this->load->model('Admin/File');
        foreach ($collection as $key => &$value) {
            if (isset($value->file_id) && $value->file_id) {
                $file = new File();
                $file->find($value->file_id);
                $value->{'file'} = $file->as_data();
            }
        }
        return $collection;
    }
}