<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class FileActivity extends MY_Model
{
    public $table = 'file_activity';
    public $primaryKey = 'file_activity_id';
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
        foreach ($collection as &$value) {
            if (isset($value->user_id)) {
                $user = new User();
                $user->find($value->user_id);
                $value->{'user'} = $user;
            }
        }
        return $collection;
    }

}
