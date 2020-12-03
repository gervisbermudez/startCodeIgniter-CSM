<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class User_tracking extends MY_model
{

    public $primaryKey = 'user_tracking_id';

    public function __construct()
    {
        parent::__construct();
    }

}
