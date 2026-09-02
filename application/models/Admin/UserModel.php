<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use Tightenco\Collect\Support\Collection;

class UserModel extends MY_Model
{
    public $table = 'user';
    public $primaryKey = 'user_id';
    public $user_data = null;
    public $hasData = true;
    public $softDelete = true;

    public $hasOne = [
        "usergroup" => ['usergroup_id', 'Admin/UsergroupModel', 'UsergroupModel'],
    ];

    public $protectedFields = array('password');

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param mixed $user_id
     * @param array $options include_inactive
     * @return Collection|false
     */
    public function get_full_info($user_id = null, $options = array())
    {
        if (!is_array($options)) {
            $options = array();
        }
        $has_id = ($user_id !== null && $user_id !== false && $user_id !== '');
        $include_inactive = !empty($options['include_inactive']) || $has_id;
        $gid = (int) userdata('usergroup_id');
        $params = array($gid);
        $where_sql = ' WHERE u.usergroup_id >= ?';
        if (!$include_inactive) {
            $where_sql .= ' AND u.status = 1';
        }
        if ($has_id) {
            $where_sql .= ' AND u.user_id = ?';
            $params[] = (int) $user_id;
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
        " . $where_sql . "
        GROUP BY s.user_id;";
        $data = $this->db->query($sql, $params);
        if ($data && $data->num_rows() > 0) {
            $data = $data->result_array();
            foreach ($data as $key => &$value) {
                $data_values = json_decode($value['user_data']);
                $value['user_data'] = $this->normalize_profile_user_data($data_values);
            }
            unset($value);
            foreach ($data as $key => &$value) {
                $data_values = json_decode($value['usergroup']);
                $value['usergroup'] = $data_values;
            }
            unset($value);
            return new Collection($data);
        }

        return false;
    }

    /**
     * @param mixed $ud
     * @return object
     */
    public function normalize_profile_user_data($ud)
    {
        if (is_array($ud)) {
            $ud = (object) $ud;
        }
        if (!is_object($ud)) {
            $ud = new stdClass();
        }
        $keys = array('nombre', 'apellido', 'telefono', 'direccion', 'avatar', 'cargo', 'bio');
        foreach ($keys as $key) {
            if (!isset($ud->{$key}) || $ud->{$key} === null) {
                $ud->{$key} = '';
            }
        }
        return $ud;
    }

    /**
     * Users visibles para el home (mismo filtro que get_full_info) con LIMIT.
     *
     * @param int $limit
     * @return Collection|false
     */
    public function dashboard_users($limit = 8)
    {
        $gid = (int) userdata('usergroup_id');
        $limit = (int) $limit;
        if ($limit < 1) {
            $limit = 8;
        }
        $sql = "SELECT u.`user_id`,
            u.`username`,
            u.`status`,
            CONCAT('{', GROUP_CONCAT(s.data), '}') AS `user_data`,
            g.name AS `role`
            FROM (SELECT d.user_id, GROUP_CONCAT('\"', d._key, '\"', ':', '\"', d._value, '\"') AS `data` FROM user_data d GROUP BY d.user_id) s
            INNER JOIN `user` u ON u.user_id = s.user_id
            INNER JOIN `usergroup` g ON g.usergroup_id = u.usergroup_id
            WHERE u.status = 1
            AND u.usergroup_id >= ?
            GROUP BY s.user_id
            ORDER BY u.date_update DESC
            LIMIT " . $limit;
        $data = $this->db->query($sql, array($gid));
        if ($data->num_rows() > 0) {
            $rows = $data->result_array();
            foreach ($rows as &$value) {
                $decoded = json_decode($value['user_data'], true);
                $value['user_data'] = is_array($decoded) ? $decoded : array();
            }
            unset($value);
            return new Collection($rows);
        }
        return false;
    }

    /**
     * @return int
     */
    public function dashboard_users_count()
    {
        $gid = (int) userdata('usergroup_id');
        $sql = "SELECT COUNT(*) AS c FROM (
            SELECT u.user_id
            FROM (SELECT d.user_id FROM user_data d GROUP BY d.user_id) s
            INNER JOIN `user` u ON u.user_id = s.user_id
            WHERE u.status = 1
            AND u.usergroup_id >= ?
            GROUP BY s.user_id
        ) t";
        $query = $this->db->query($sql, array($gid));
        if (!$query || $query->num_rows() === 0) {
            return 0;
        }
        $row = $query->row_array();
        return isset($row['c']) ? (int) $row['c'] : 0;
    }

    /**
     * UNION of pages, collections and items created by the user (no HTML content).
     *
     * @return string
     */
    protected function timeline_union_sql()
    {
        return "SELECT page_id AS entity_id, 'page' AS model_type, title, date_create, status,
                page_id, path, NULL AS custom_model_id, NULL AS custom_model_content_id, NULL AS collection_name
            FROM page
            WHERE user_id = ? AND status != 0
            UNION ALL
            SELECT custom_model_id AS entity_id, 'custom_model' AS model_type, form_name AS title, date_create, status,
                NULL AS page_id, NULL AS path, custom_model_id, NULL AS custom_model_content_id, NULL AS collection_name
            FROM custom_model
            WHERE user_id = ? AND status != 0
            UNION ALL
            SELECT c.custom_model_content_id AS entity_id, 'custom_model_content' AS model_type, c.title, c.date_create, c.status,
                NULL AS page_id, NULL AS path, c.custom_model_id, c.custom_model_content_id, m.form_name AS collection_name
            FROM custom_model_content c
            LEFT JOIN custom_model m ON m.custom_model_id = c.custom_model_id
            WHERE c.user_id = ? AND c.status != 0";
    }

    /**
     * @param int $user_id
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function get_timeline($user_id, $limit = 20, $offset = 0)
    {
        $user_id = (int) $user_id;
        $limit = (int) $limit;
        $offset = (int) $offset;
        if ($limit < 1) {
            $limit = 20;
        }
        if ($offset < 0) {
            $offset = 0;
        }
        $sql = "SELECT entity_id, model_type, title, date_create, status, page_id, path,
                custom_model_id, custom_model_content_id, collection_name
            FROM (" . $this->timeline_union_sql() . ") t
            ORDER BY date_create DESC
            LIMIT " . $limit . " OFFSET " . $offset;
        $query = $this->db->query($sql, array($user_id, $user_id, $user_id));
        if (!$query || $query->num_rows() === 0) {
            return array();
        }
        return $query->result_array();
    }

    /**
     * @param int $user_id
     * @return int
     */
    public function count_timeline($user_id)
    {
        $user_id = (int) $user_id;
        $sql = "SELECT COUNT(*) AS c FROM (" . $this->timeline_union_sql() . ") t";
        $query = $this->db->query($sql, array($user_id, $user_id, $user_id));
        if (!$query || $query->num_rows() === 0) {
            return 0;
        }
        $row = $query->row_array();
        return isset($row['c']) ? (int) $row['c'] : 0;
    }

    public function search($str_term)
    {
        $collection = $this->get_full_info();

        if (!$collection) {
            return [];
        }

        $result = array_filter($collection->toArray(), function ($item) use ($str_term) {
            $is_acceptable = false;

            foreach ($item as $key => $value) {
                if (is_string($value)) {
                    $pos = strpos(strtolower($value), strtolower($str_term));
                    if ($pos !== false) {
                        $is_acceptable = true;
                        break;
                    }
                }

                if ($key == 'user_data') {
                    foreach ($value as $i => $val) {
                        $pos = strpos(strtolower($val), strtolower($str_term));
                        if ($pos !== false) {
                            $is_acceptable = true;
                            break;
                        }
                    }
                }

            }
            return $is_acceptable;
        });

        $return_array = array();

        foreach ($result as $key => $value) {
            $return_array[] = $value;
        }

        return $return_array;
    }
}
