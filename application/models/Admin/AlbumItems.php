<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class AlbumItems extends MY_Model {
    public $table = 'album_items';
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
        return $this->loadFilesRelation($collection, 'file_id', 'file');
    }
}