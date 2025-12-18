<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Menu extends MY_Model
{

    public $softDelete = true;
    public $primaryKey = 'menu_id';
    public $hasOne = [
        'user' => ['user_id', 'Admin/User', 'user'],
    ];
    public $hasMany = [
        'menu_items' => ['menu_id', 'Admin/MenuItems', 'MenuItems', [
            'menu_item_parent_id' => 0
        ]],
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function filter_results($collection = [])
    {
        return $this->loadUsersRelation($collection);
    }

}
