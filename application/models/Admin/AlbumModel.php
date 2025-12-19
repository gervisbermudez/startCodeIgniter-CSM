<?php

use Tightenco\Collect\Support\Collection;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class AlbumModel extends MY_Model {

    public $table = 'album';
    public $primaryKey = 'album_id';
    public $softDelete = true;

	public $hasOne = [
        'user' => ['user_id', 'Admin/UserModel', 'UserModel'],
    ];

	public $hasMany = [
        'items' => ['album_id', 'Admin/AlbumItemsModel', 'AlbumItemsModel'],
    ];

	public function __construct()
	{
		parent::__construct();
	}

	public function filter_results($collection = [])
    {
		// Cargar users de forma optimizada (1 query en lugar de N)
        $collection = $this->loadUsersRelation($collection);
		
		// Agregar model_type a cada item
        foreach ($collection as $key => &$value) {
            if (isset($value->user_id)) {
                $value->model_type = "album";
            }
        }
		
		// Cargar album items
        $this->load->model('Admin/AlbumItemsModel');
        foreach ($collection as $key => &$value) {
            if (isset($value->album_id) && $value->album_id) {
                $album_item = new AlbumItemsModel();
                $results =  $album_item->where(["album_id" => $value->album_id]);
                $value->items = $results ? $results : new Collection();
            }
        }
        return $collection;
    }

}