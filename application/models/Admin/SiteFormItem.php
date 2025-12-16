<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class SiteFormItem extends MY_model
{

    public $table = 'siteform_items';
    public $primaryKey = 'siteform_item_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function filter_results($collection = [])
    {
        foreach ($collection as $key => &$value) {
            if (isset($value->properties) && $value->properties) {
                $value->{'properties'} = json_decode($value->properties);
            }

            if (isset($value->data) && $value->data) {
                $value->{'data'} = json_decode($value->data);
            }
        }

        if (!function_exists('cmp')) {
            function cmp($a, $b)
            {
                return $a->order <=> $b->order;
            }
        }

        usort($collection, "cmp");

        return $collection;
    }

}
