<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Siteform_submit extends MY_model
{
    public $primaryKey = 'siteform_submit_id';
    public $table = 'siteform_submit';
    public $hasData = true;

    public function __construct()
    {
        parent::__construct();
    }

}
