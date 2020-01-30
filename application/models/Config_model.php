<?php
/**
 * The config model
 */
class Config_model extends MY_Model
{
    private $table = 'site_config';

    public function __construct()
    {
        parent::__construct();
    }

    public function save_config($data)
    {
        foreach ($data as $key => $config) {
            $this->update_data(
                array('config_name' => $config['config_name']),
                array('config_value' => $config['config_value']),
                $this->table
            );
        }
    }

    /**
     * Array with config
     */
    public function load_config()
    {
        $sql = "SELECT CONCAT('{', GROUP_CONCAT(config), '}') AS 'all_config'
                FROM (
                SELECT CONCAT('\"', site_config.config_name, '\" : \"', site_config.config_value, '\"') AS config
                FROM site_config) c";
        $result = $this->get_query($sql);
        if ($result) {
            return json_decode($result[0]['all_config']);
        }
        return array();
    }
}
