<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use Tightenco\Collect\Support\Collection;

class User extends MY_model
{
	public $primaryKey = 'user_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_full_info($user_id = "")
    {
		$where = "";

        if ($user_id != '') {
            $where = "WHERE u.user_id = $user_id";
		}
		
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

		$data = $this->db->query($sql);
        if ($data->num_rows() > 0) {
			$data = $data->result_array();
			foreach ($data as $key => &$value) {
                $data_values = json_decode($value['user_data']);
                $value['user_data'] = $data_values;
            }
            return new Collection($data);
        }

        return false;
    }

    public function set_user_data($data){
		
		if (!$data) {
			return false;
		}
		if (!$this->db->insert('user_data', $data)) {
			return false;
		}		
		return true;
	}

	public function get_user_data($data)
	{
		if ($data === 'all') {
			$query = $this->db->get('user_data');	
			if ($query->num_rows() > 0)
			{
			   return $query->result_array();
			}
		}else{
			$query = $this->db->get_where('user_data', $data);
			if ($query->num_rows() > 0){
				return $query->result_array();
			}
		}
		return false;
	}
	
	public function update_user_data($data, $where)
	{
		$this->db->where($where);
		return $this->db->update('user_data', $data);
	}

	public function delete_user_data($data)
	{
		if (!$data) {
			return false;
		}
		$this->db->where($data);
		return $this->db->delete('user_data');
	}
}
