<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class UserTrackingModel extends MY_Model
{
    public $table = 'user_tracking';
    public $primaryKey = 'user_tracking_id';
    public $searchable = array('page_name', 'requested_url', 'client_ip');

    public function __construct()
    {
        parent::__construct();
    }

}
