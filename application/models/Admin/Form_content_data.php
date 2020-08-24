<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Form_content_data extends MY_model {

	public $table = 'form_content_data';
	public $primaryKey = 'form_content_data_id';

	function __construct()
	{
		parent::__construct();
    }

    public function filter_results($collection = [])
    {
        foreach ($collection as $key => &$value) {
            if (isset($value->form_value) && $value->form_value) {
                $value->form_value = json_decode($value->form_value);
            }
        }
        return $collection;
    }
}