<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class UserTracking extends MY_Model
{
    public $table = 'user_tracking';
    public $primaryKey = 'user_tracking_id';

    public function __construct()
    {
        parent::__construct();
    }

}
