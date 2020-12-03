<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Api_logs extends MY_model
{

    public $primaryKey = 'api_log_id';

    public function __construct()
    {
        parent::__construct();
    }

}
