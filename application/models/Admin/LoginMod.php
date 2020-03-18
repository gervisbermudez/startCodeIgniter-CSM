<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class LoginMod extends MY_model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function isLoged($username, $password)
    {

        $where = "WHERE u.`username` = '$username' AND u.`password` = '$password' AND u.`status` = 1";

        $sql = "SELECT u.`user_id`,
				u.`username`,
				u.`email`,
				u.`lastseen`,
				u.`usergroup_id`,
				u.`status`, CONCAT('{', GROUP_CONCAT(s.data), '}') AS `user_data`,
				g.name AS `role`,
				g.`level`
				FROM (
				SELECT d.user_id, GROUP_CONCAT('\"', d._key, '\"', ':', '\"', d._value, '\"') AS `data`
				FROM user_data d
				GROUP BY d.user_id) s
				INNER JOIN `user` u ON u.user_id = s.user_id
				INNER JOIN `usergroup` g ON u.usergroup_id = g.usergroup_id
				$where
				GROUP BY s.user_id;";

        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            $data = $query->result_array();
            foreach ($data as $key => &$value) {
                $data_values = json_decode($value['user_data']);
                $value['user_data'] = $data_values;
            }

            return $data;
        }
        return false;
    }
}
