<?php

use Tightenco\Collect\Support\Collection;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class UsergroupModel extends MY_Model
{
    public $table = 'usergroup';
    public $primaryKey = 'usergroup_id';
    public $softDelete = true;
    protected $attributes = array(
        'status' => 1,
        'level' => 3,

    );

    public $computed = array('usergroup_permisions' => 'get_default_usergroup_permisions');

    public function __construct()
    {
        parent::__construct();
    }

    public function filter_results($collection = [])
    {
        $this->load->model('Admin/UserModel');
        foreach ($collection as $key => &$value) {
            if (isset($value->user_id) && $value->user_id) {
                $user = new UserModel();
                $user->find($value->user_id);
                $value->{'user'} = $user->as_data();
            }
        }

        foreach ($collection as &$value) {
            if (isset($value->usergroup_id) && $value->usergroup_id) {
                $value->{'usergroup_permisions'} = $this->get_permisions($value->usergroup_id);
            }
        }

        return new Collection($collection);
    }

    public function usergroup_permisions()
    {
        return $this->get_permisions($this->usergroup_id);
    }

    public function get_default_usergroup_permisions()
    {
        return [];
    }

    private function get_permisions($usergroup_id)
    {
        $this->load->model('Admin/UsergroupPermissionsModel');
        $UsergroupPermissions = new UsergroupPermissionsModel();
        $result = $UsergroupPermissions->where(["usergroup_id" => $usergroup_id]);
        if ($result) {
            $result = array_merge($this->permisions, $result->toArray());
            return $result;
        }

        return [];
    }

}