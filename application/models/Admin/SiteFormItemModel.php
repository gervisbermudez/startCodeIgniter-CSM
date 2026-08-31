<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class SiteFormItemModel extends MY_Model
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
            if (function_exists('normalize_siteform_loop')) {
                $value->properties = normalize_siteform_loop(isset($value->properties) ? $value->properties : null);
                $value->data = normalize_siteform_loop(isset($value->data) ? $value->data : null);
            } else {
                $value->properties = (isset($value->properties) && (is_array($value->properties) || is_object($value->properties)))
                    ? $value->properties
                    : array();
                $value->data = (isset($value->data) && (is_array($value->data) || is_object($value->data)))
                    ? $value->data
                    : array();
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
