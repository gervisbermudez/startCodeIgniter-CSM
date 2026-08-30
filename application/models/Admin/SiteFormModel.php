<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class SiteFormModel extends MY_Model
{
    public $table = 'siteform';
    public $softDelete = true;
    public $primaryKey = 'siteform_id';
    public $hasOne = [
        'user' => ['user_id', 'Admin/UserModel', 'UserModel'],
    ];

    public $hasMany = [
        "siteform_items" => ["siteform_id", "Admin/SiteFormItemModel", 'SiteFormItemModel'],
    ];

    public $computed = array("properties" => "properties_to_json");
    public $searchable = array('name', 'template');

    public $properties = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function filter_results($collection = [])
    {
        $this->loadUsersRelation($collection);

        $ids = array();
        foreach ($collection as $value) {
            if (isset($value->siteform_id)) {
                $ids[] = (int) $value->siteform_id;
            }
        }
        $ids = array_values(array_unique($ids));

        $counts = array();
        if (!empty($ids)) {
            $CI = &get_instance();
            $rows = $CI->db
                ->select('siteform_id, COUNT(*) AS submissions_count')
                ->from('siteform_submit')
                ->where_in('siteform_id', $ids)
                ->where_in('status', array(1, 2))
                ->group_by('siteform_id')
                ->get()
                ->result();
            foreach ($rows as $row) {
                $counts[(int) $row->siteform_id] = (int) $row->submissions_count;
            }
        }

        foreach ($collection as $value) {
            $id = isset($value->siteform_id) ? (int) $value->siteform_id : 0;
            $value->submissions_count = isset($counts[$id]) ? $counts[$id] : 0;
            $name = isset($value->name) ? $value->name : '';
            $value->snippet = "{!! render_form('" . $name . "') !!}";
        }

        return $collection;
    }

    public function properties_to_json()
    {
        $decoded = is_string($this->properties) ? json_decode($this->properties) : $this->properties;
        return is_array($decoded) ? $decoded : array();
    }

}
