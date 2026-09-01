<?php

use Tightenco\Collect\Support\Collection;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class AlbumModel extends MY_Model {

    public $table = 'album';
    public $primaryKey = 'album_id';
    public $softDelete = true;
    public $searchable = array('name', 'description');

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

    /**
     * Álbumes del home con hasta 2 covers, sin el resto de items.
     *
     * @param int $limit
     * @return Collection|false
     */
    public function dashboard_albums($limit = 6)
    {
        $limit = (int) $limit;
        if ($limit < 1) {
            $limit = 6;
        }
        $this->db->select('album_id, name, user_id, status, date_create');
        $this->db->where('status', 1);
        $this->db->order_by('album_id', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get($this->table);
        if ($query->num_rows() < 1) {
            return false;
        }
        $albums = $query->result();
        $this->load->model('Admin/AlbumItemsModel');
        foreach ($albums as $album) {
            $item = new AlbumItemsModel();
            $covers = $item->where(
                array('album_id' => $album->album_id),
                array(2),
                array('album_item_id', 'ASC')
            );
            $album->items = $covers ? $covers : array();
        }
        return new Collection($albums);
    }

}