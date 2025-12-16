<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Notifications extends MY_Model
{

    public $primaryKey = 'notification_id';
    public $softDelete = true;

    public $hasOne = [
        'user' => ['user_id', 'Admin/User', 'User'],
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function filter_results($collection = [])
    {
        $this->load->model('Admin/User');
        foreach ($collection as $key => &$value) {
            if (isset($value->user_id)) {
                $user = new User();
                $user->find($value->user_id);
                $value->{'user'} = $user;
            }
        }
        return $collection;
    }

}
