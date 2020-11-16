<?php

use Tightenco\Collect\Support\Collection;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Usergroup_permisions extends MY_model
{
    public $primaryKey = 'usergroup_permisions_id';
    public $softDelete = true;

    public $hasOne = [
        "permisions_name" => ['permision_id', 'Admin/Permisions', 'Permisions'],
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function filter_results($collection = [])
    {
        $this->load->model('Admin/Permisions');
        $permission_array = [];
        foreach ($collection as $key => &$value) {
            if (isset($value->permision_id)) {
                $permission = new Permisions();
                $permission->find($value->permision_id);
                $permission_array[] = $permission->permision_name;
            }
        }

        return new Collection($permission_array);
    }

}
