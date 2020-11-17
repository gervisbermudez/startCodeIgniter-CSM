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

            if ($value->model && $value->model_id) {
                switch ($value->model) {
                    case 'pages':
                        $this->load->model('Admin/Page');
                        $page = new Page();
                        $result = $page->find($value->model_id);
                        if ($result) {
                            $value->item_link = '/' . $page->path;
                            $value->{'model_object'} = $page;
                        }
                        break;

                    default:
                        break;
                }
            }

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
