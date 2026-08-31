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
        $usergroup_id = (int) $usergroup_id;
        if ($usergroup_id < 1) {
            return array();
        }
        $this->db->reset_query();
        $sql = 'SELECT permisions.permision_name AS permision_name
            FROM usergroup_permisions
            INNER JOIN permisions ON permisions.permisions_id = usergroup_permisions.permision_id
            WHERE usergroup_permisions.usergroup_id = ?
            AND usergroup_permisions.status = 1
            AND permisions.status = 1';
        $query = $this->db->query($sql, array($usergroup_id));
        if (!$query || $query->num_rows() === 0) {
            return array();
        }
        $names = array();
        foreach ($query->result() as $row) {
            if (!empty($row->permision_name) && is_string($row->permision_name)) {
                $names[] = $row->permision_name;
            }
        }
        return array_values($names);
    }

}