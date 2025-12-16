<?php

use Tightenco\Collect\Support\Collection;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Album extends MY_Model {

    public $primaryKey = 'album_id';
    public $softDelete = true;

	public $hasOne = [
        'user' => ['user_id', 'Admin/User', 'User'],
    ];

	public $hasMany = [
        'items' => ['album_id', 'Admin/AlbumItems', 'AlbumItems'],
    ];

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
                $value->{'model_type'} = "album";
            }
        }
        $this->load->model('Admin/AlbumItems');
        foreach ($collection as $key => &$value) {
            if (isset($value->album_id) && $value->album_id) {
                $album_item = new AlbumItems();
                $results =  $album_item->where(["album_id" => $value->album_id]);
                $value->{'items'} = $results ? $results : new Collection();
            }
        }
        return $collection;
    }

}