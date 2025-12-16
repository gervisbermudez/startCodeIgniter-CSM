<?php

use Tightenco\Collect\Support\Collection;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class UsergroupPermissions extends MY_model
{
    public $table = 'usergroup_permisions';
    public $primaryKey = 'usergroup_permisions_id';
    public $softDelete = true;

    public $hasOne = [
        "permisions_name" => ['permision_id', 'Admin/Permissions', 'Permissions'],
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function filter_results($collection = [])
    {
        $this->load->model('Admin/Permissions');
        $permission_array = [];
        foreach ($collection as $key => &$value) {
            if (isset($value->permision_id)) {
                $permission = new Permissions();
                $permission->find($value->permision_id);
                $permission_array[] = $permission->permision_name;
            }
        }

        return new Collection($permission_array);
    }

    public function get_permissions_info($where)
    {
        $this->load->model('Admin/Permissions');

        $this->db->where($where);
        $query = $this->db->get($this->table);
        if ($query->num_rows() > 0) {
            $collection = new Collection($query->result());
            foreach ($collection as $key => &$value) {
                if (isset($value->permision_id)) {
                    $permission = new Permissions();
                    $value->{'permission'} = $permission->find($value->permision_id);
                }
            }
            return $collection;
        }


        return new Collection();
    }

}
