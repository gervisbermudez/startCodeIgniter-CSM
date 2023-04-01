<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Custom_model_content_data extends MY_model
{
    public $primaryKey = 'custom_model_content_data_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function filter_results($collection = [])
    {
        foreach ($collection as $key => &$value) {
            if (isset($value->custom_model_content_data_value) && $value->custom_model_content_data_value) {
                $value->custom_model_content_data_value = json_decode($value->custom_model_content_data_value);
            }
        }
        return $collection;
    }
}
