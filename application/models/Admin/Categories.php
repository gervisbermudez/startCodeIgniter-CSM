<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Categories extends MY_model
{

    public $primaryKey = 'categorie_id';
    public $softDelete = true;

    public $hasOne = [
        'user' => ['user_id', 'Admin/User', 'user'],
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

        foreach ($collection as $key => &$value) {
            if (isset($value->parent_id) && $value->parent_id !== 0) {
                $this->load->model('Admin/Categories');
                $categorie = new Categories();
                $categorie->find($value->parent_id);
                $value->{'parent'} = $categorie->as_data();
            }
        }

        foreach ($collection as $key => &$value) {
            if (isset($value->parent_id) && $value->categorie_id) {
                $this->load->model('Admin/Categories');
                $categorie = new Categories();
                $subcategories = $categorie->where(['parent_id' => $value->categorie_id]);

                $value->{'subcategories'} = $subcategories ? $subcategories : [];
            }
        }

        return $collection;
    }

}
