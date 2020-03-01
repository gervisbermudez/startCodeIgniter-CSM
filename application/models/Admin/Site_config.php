<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Site_config extends MY_model
{

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

    /**
     * Return a object with the current site config
     * @return json object
     */
    public function load_config()
    {
        $sql = "SELECT CONCAT('{', GROUP_CONCAT(config), '}') AS 'all_config'
                FROM (
                SELECT CONCAT('\"', site_config.config_name, '\" : \"', site_config.config_value, '\"') AS config
                FROM site_config) c";
        $result = $this->get_query($sql);
        if ($result) {
            $result = $result->first();
            return json_decode($result->all_config);
        }
        return json_decode(array());
    }

}
