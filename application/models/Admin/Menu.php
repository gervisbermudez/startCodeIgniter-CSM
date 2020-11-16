<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Menu extends MY_model
{

    public $softDelete = true;
    public $primaryKey = 'menu_id';
    public $hasOne = [
        'user' => ['user_id', 'Admin/User', 'user'],
    ];
    public $hasMany = [
        'menu_items' => ['menu_id', 'Admin/Menu_items', 'Menu_items', [
            'menu_item_parent_id' => 0
        ]],
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function filter_results($collection = [])
    {
        $this->load->model('Admin/User');
        foreach ($collection as $key => &$value) {
            if (isset($value->user_id) && $value->user_id) {
                $user = new User();
                $user->find($value->user_id);
                $value->{'user'} = $user->as_data();
            }
        }

        return $collection;
    }

}
