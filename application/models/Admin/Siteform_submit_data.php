<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Siteform_submit_data extends MY_model
{

    public $table = 'siteform_submit_data';
    public $primaryKey = 'siteform_submit_data_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function filter_results($collection = [])
    {
        return $collection;
    }
}
