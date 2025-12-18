<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class MenuItems extends MY_Model
{
    public $table = 'menu_items';
    public $primaryKey = 'menu_item_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function filter_results($collection = [])
    {
        // Load all page links in a single query (eliminates N+1)
        $this->loadPageLinks($collection);

        // Load all subitems in a single query (eliminates N+1)
        $this->loadSubitems($collection);

        return $collection;
    }

    /**
     * Carga los enlaces de páginas asociadas en una sola query
     */
    private function loadPageLinks($collection)
    {
        // Extraer page_ids únicos
        $pageIds = array_unique(
            array_filter(
                array_map(function($item) {
                    return ($item->model === 'page' && $item->model_id) ? $item->model_id : null;
                }, is_array($collection) ? $collection : $collection->toArray())
            )
        );

        if (empty($pageIds)) {
            return;
        }

        // Cargar todas las páginas en una query
        $this->load->model('Admin/Page');
        $pagesQuery = $this->db
            ->select('page_id, path')
            ->where_in('page_id', $pageIds)
            ->get('page');

        // Indexar por page_id
        $pagesById = [];
        if ($pagesQuery->num_rows() > 0) {
            foreach ($pagesQuery->result() as $page) {
                $pagesById[$page->page_id] = $page;
            }
        }

        // Asignar item_link a cada menu item
        foreach ($collection as &$item) {
            if ($item->model === 'page' && isset($pagesById[$item->model_id])) {
                $item->item_link = base_url($pagesById[$item->model_id]->path);
            }
        }
    }

    /**
     * Carga todos los subitems de forma jerárquica en una sola query
     */
    private function loadSubitems($collection)
    {
        // Extraer menu_item_ids únicos
        $menuItemIds = array_filter(
            array_map(function($item) {
                return isset($item->menu_item_id) ? $item->menu_item_id : null;
            }, is_array($collection) ? $collection : $collection->toArray())
        );

        if (empty($menuItemIds)) {
            return;
        }

        // Cargar todos los subitems en una query
        $subitemsQuery = $this->db
            ->where_in('menu_item_parent_id', $menuItemIds)
            ->get($this->table);

        // Indexar subitems por parent_id
        $subitemsByParent = [];
        if ($subitemsQuery->num_rows() > 0) {
            foreach ($subitemsQuery->result() as $subitem) {
                if (!isset($subitemsByParent[$subitem->menu_item_parent_id])) {
                    $subitemsByParent[$subitem->menu_item_parent_id] = [];
                }
                $subitemsByParent[$subitem->menu_item_parent_id][] = $subitem;
            }
        }

        // Asignar subitems a cada menu item
        foreach ($collection as &$item) {
            $item->{'subitems'} = isset($subitemsByParent[$item->menu_item_id]) 
                ? $subitemsByParent[$item->menu_item_id] 
                : [];
        }
    }

}
