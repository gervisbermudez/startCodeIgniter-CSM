<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use Tightenco\Collect\Support\Collection;

class CategorieModel extends MY_Model
{
    public $table = 'categories';
    public $primaryKey = 'categorie_id';
    public $softDelete = true;

    public $hasOne = [
        'user' => ['user_id', 'Admin/UserModel', 'UserModel'],
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function get_counted()
    {
        $sql = 'SELECT COUNT(parent_id) `pages`, cat.name FROM page INNER JOIN categories cat ON page.categorie_id = cat.categorie_id GROUP by cat.categorie_id';
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {

            return new Collection($this->filter_results($query->result()));
        }
        return false;
    }

    public function filter_results($collection = [])
    {
        // Cargar users de forma optimizada (1 query en lugar de N)
        $collection = $this->loadUsersRelation($collection);

        // Cargar categorías parent
        foreach ($collection as $key => &$value) {
            if (isset($value->parent_id) && $value->parent_id !== 0) {
                $this->load->model('Admin/CategorieModel');
                $categorie = new CategorieModel();
                $categorie->find($value->parent_id);
                $value->parent = $categorie->as_data();
            }
        }

        // Cargar subcategorías
        foreach ($collection as $key => &$value) {
            if (isset($value->parent_id) && $value->categorie_id) {
                $this->load->model('Admin/CategorieModel');
                $categorie = new CategorieModel();
                $subcategories = $categorie->where(['parent_id' => $value->categorie_id]);
                $value->subcategories = $subcategories ? $subcategories : [];
            }
        }

        return $collection;
    }

}
