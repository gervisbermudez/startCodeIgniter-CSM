<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Menu_items extends MY_model
{

    public $primaryKey = 'menu_item_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function filter_results($collection = [])
    {

        foreach ($collection as $key => &$value) {
            if (isset($value->menu_item_parent_id) && $value->menu_item_id) {
                $this->load->model('Admin/Menu_items');
                $menu_item = new Menu_items();
                $subitems = $menu_item->where(['menu_item_parent_id' => $value->menu_item_id]);
                $value->{'subitems'} = $subitems ? $subitems : [];
            }
        }

        return $collection;
    }

}
