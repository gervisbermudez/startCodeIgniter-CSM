<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Custom_model_fields extends MY_Model
{
    public $primaryKey = 'custom_model_fields_id';
    public $hasData = true;

    public function __construct()
    {
        parent::__construct();
    }

    public function filter_results($collection = [])
    {
        foreach ($collection as $key => &$value) {
            $value->{'data'} = $this->search_for_data($value->custom_model_fields_id, 'custom_model_fields_id');
        }
        return $collection;
    }

}
