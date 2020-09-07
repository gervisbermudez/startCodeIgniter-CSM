<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use Tightenco\Collect\Support\Collection;

class User extends MY_model
{
    public $primaryKey = 'user_id';
    public $user_data = null;
    public $hasData = true;
    public $hasOne = [
        "usergroup" => ['usergroup_id', 'Admin/Usergroup', 'usergroup'],
    ];
    public $protectedFields = array('password');

    public function __construct()
    {
        parent::__construct();
    }

    public function get_full_info($user_id = null)
    {
        $where = "";

        if ($user_id) {
            $where = "WHERE u.user_id = $user_id";
        }

        $sql = "SELECT u.`user_id`,
		u.`username`,
		u.`email`,
		u.`lastseen`,
		u.`usergroup_id`,
		u.`status`, CONCAT('{', GROUP_CONCAT(s.data), '}') AS `user_data`,
		g.name AS `role`,
		g.`level`,
		u.`date_create`,
		u.`date_update`,
        subu.usergroup
		FROM (SELECT d.user_id, GROUP_CONCAT('\"', d._key, '\"', ':', '\"', d._value, '\"') AS `data` FROM user_data d GROUP BY d.user_id) s
		INNER JOIN `user` u ON u.user_id = s.user_id
		INNER JOIN `usergroup` g ON g.usergroup_id = u.usergroup_id
		INNER JOIN (" . $this->get_select_json('usergroup') . ") subu ON subu.usergroup_id = u.usergroup_id
        $where
        GROUP BY s.user_id;";
        $data = $this->db->query($sql);
        if ($data->num_rows() > 0) {
            $data = $data->result_array();
            foreach ($data as $key => &$value) {
                $data_values = json_decode($value['user_data']);
                $value['user_data'] = $data_values;
            }
            foreach ($data as $key => &$value) {
                $data_values = json_decode($value['usergroup']);
                $value['usergroup'] = $data_values;
            }
            return new Collection($data);
        }

        return false;
    }

}
