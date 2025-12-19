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

    public $properties = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function filter_results($collection = [])
    {
        $this->load->model('Admin/UserModel');
        foreach ($collection as $key => &$value) {
            if (isset($value->user_id) && $value->user_id) {
                $user = new UserModel();
                $user->find($value->user_id);
                $value->{'user'} = $user->as_data();
            }
        }

        return $collection;
    }

    public function properties_to_json()
    {
        return is_string($this->properties) ? json_decode($this->properties) : [];
    }

}