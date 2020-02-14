<?php
class LoginMod extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function isLoged($username, $password)
    {
        $where = "WHERE `u`.`username` = '$username' AND `u`.`password` = '$password' AND `u`.`status` = 1";

        $sql = "SELECT u.`id`,
            u.`username`,
            u.`email`,
            u.`lastseen`,
            u.`usergroup`,
            u.`status`, CONCAT('{', GROUP_CONCAT(s.data), '}') AS `user_data`,
            g.name AS `role`,
            g.`level`
            FROM (
            SELECT d.id_user, CONCAT('\"', d._key, '\"', ':', '\"', d._value, '\"') AS `data`
            FROM datauserstorage d
            GROUP BY d.id) s
            INNER JOIN `user` u ON u.id = s.id_user
            INNER JOIN `usergroup` g ON u.usergroup = g.id
            $where
			GROUP BY s.id_user;";

        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            $data = $query->result();
            foreach ($data as $key => &$value) {
                $data_values = json_decode($value->user_data);
                $value->user_data = $data_values;
            }

            return $data;
        }

        return false;
    }
}
