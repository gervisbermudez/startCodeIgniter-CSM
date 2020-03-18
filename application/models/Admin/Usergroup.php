<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Usergroup extends MY_model
{
    public $primaryKey = 'usergroup_id';

    protected $attributes = array(
        'status' => 1,
        'level' => 3,
    );

    public function __construct()
    {
        parent::__construct();
    }

}
