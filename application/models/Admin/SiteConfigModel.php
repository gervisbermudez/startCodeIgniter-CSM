<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class SiteConfigModel extends MY_Model
{

    public $table = 'site_config';
    public $primaryKey = 'site_config_id';
    public $softDelete = true;

    public $hasOne = [
        'user' => ['user_id', 'Admin/UserModel', 'UserModel'],
    ];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Update all config data
     */
    public function update_config($data)
    {
        foreach ($data as $key => $config) {
            $this->update_data(
                array('config_name' => $config['config_name']),
                array('config_value' => $config['config_value']),
                $this->table
            );
        }
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

}
