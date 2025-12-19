<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class NotificationsModel extends MY_Model
{
    public $table = 'notifications';
    public $primaryKey = 'notification_id';
    public $softDelete = true;

    public $hasOne = [
        'user' => ['user_id', 'Admin/UserModel', 'UserModel'],
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function filter_results($collection = [])
    {
        $this->load->model('Admin/UserModel');
        foreach ($collection as $key => &$value) {
            if (isset($value->user_id)) {
                $user = new UserModel();
                $user->find($value->user_id);
                $value->{'user'} = $user;
            }
        }
        return $collection;
    }

}
