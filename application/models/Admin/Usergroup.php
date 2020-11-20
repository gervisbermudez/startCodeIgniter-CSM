<?php 

use Tightenco\Collect\Support\Collection;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Usergroup extends MY_model
{
    public $primaryKey = 'usergroup_id';
    public $softDelete = true;

    protected $attributes = array(
        'status' => 1,
        'level' => 3,
    );

    public function __construct()
    {
        parent::__construct();
    }

    public function filter_results($collection = [])
    {
        $this->load->model('Admin/Usergroup_permisions');
        foreach ($collection as &$value) {
            if (isset($value->usergroup_id)) {
                $Usergroup_permisions = new Usergroup_permisions();
                $usergroup_permisions = $Usergroup_permisions->where(["usergroup_id" => $value->usergroup_id]);
                $value->{"usergroup_permisions"} = $usergroup_permisions ? $usergroup_permisions->toArray() : [];
                $value->{"usergroup_permisions"} = array_merge(
                    $this->permisions,
                    $value->{"usergroup_permisions"}
                );
            }
        }

        foreach ($collection as &$value) {
            if (isset($value->parent_id) && $value->usergroup_id) {
                $this->load->model('Admin/Usergroup');
                $Usergroup = new Usergroup();
                $sub_usergroups = $Usergroup->where(['parent_id' => $value->usergroup_id]);

                $value->{'sub_usergroups'} = $sub_usergroups ? $sub_usergroups : [];
            }
        }

        return new Collection($collection);
    }

}
