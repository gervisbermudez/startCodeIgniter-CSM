<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class ApiLogsModel extends MY_Model
{
    public $table = 'api_logs';
    public $primaryKey = 'api_log_id';
    public $searchable = array('uri', 'method', 'ip_address');

    public function __construct()
    {
        parent::__construct();
    }

}
