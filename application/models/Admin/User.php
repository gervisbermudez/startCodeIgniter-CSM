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

    /**
     * Gets the user timeline, activities, models created by the user_id
     * @return Collection
     */
    public function get_timeline($user_id = null)
    {
        if ($user_id == null) {
            $user_id = $this->{$this->primaryKey};
        }

        if ($user_id == null) {
            return false;
        }

        $this->load->model('Admin/Page');
        $this->load->model('Admin/Form_content');
        $this->load->model('Admin/Form_custom');

        $pages = new Page();
        $pages = $pages->where(["user_id" => $user_id]);
        $pages = $pages ? $pages->all() : [];

        $Form_content = new Form_content();
        $Form_content = $Form_content->where(["user_id" => $user_id]);
        $Form_content = $Form_content ? $Form_content->all() : [];

        $Form_custom = new Form_custom();
        $Form_custom = $Form_custom->where(["user_id" => $user_id]);
        $Form_custom = $Form_custom ? $Form_custom->all() : [];

        $time_line_collection = array_merge($pages, $Form_custom, $Form_content);

        function sortFunction($a, $b)
        {
            $time_a = DateTime::createFromFormat('Y-m-d H:i:s', $a->date_create);
            $time_b = DateTime::createFromFormat('Y-m-d H:i:s', $b->date_create);
            if ($time_a == $time_b) {
                return 0;
            }

            return $time_a > $time_b ? -1 : 1;
        }

        usort($time_line_collection, "sortFunction");

        return new Collection($time_line_collection);
    }

}
