<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class FragmentModel extends MY_Model
{

    public $table = 'fragmentos';
    public $primaryKey = 'fragment_id';
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
        return $this->loadUsersRelation($collection);
    }

}
