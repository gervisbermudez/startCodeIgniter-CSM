<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use Tightenco\Collect\Support\Collection;

class PageModel extends MY_Model
{
    public $table = 'page';
    public $primaryKey = 'page_id';
    public $hasData = true;
    public $softDelete = true;
    public $hasOne = [
        'user' => ['user_id', 'Admin/UserModel', 'UserModel'],
        'pages_type' => ['page_type_id', 'Admin/PageTypeModel', 'PageTypeModel'],
        'main_image' => ['mainImage', 'Admin/FileModel', 'FileModel'],
        'thumbnail_image' => ['thumbnailImage', 'Admin/FileModel', 'FileModel'],
    ];

    public $page_data = [];

    public $computed = ["json_content" => "get_json_content"];
    public $searchable = array('title', 'path', 'subtitle');

    /**
     * Page status:
     * 0 => deleted
     * 1 => published
     * 2 => draft
     * 3 => archived
     */

    public function __construct()
    {
        parent::__construct();
    }

    public function get_json_content()
    {
        $value = $this->json_content;
        if (is_object($value) || is_array($value)) {
            return $value;
        }
        if (!is_string($value) || $value === '') {
            return $value;
        }
        return json_decode($value);
    }

    /**
     * Return all records found on a table or false if nothing is found
     * @return Collection
     */

    public function all($limit = array(), $order = array())
    {
        $sql = 'SELECT p.*, pt.`page_type_name`, u.`username`, ug.`name`, ug.`level`, file_data.file as imagen_file
                FROM page p
                INNER JOIN user u ON p.`user_id` = u.`user_id`
                INNER JOIN usergroup ug ON ug.`usergroup_id` = u.`usergroup_id`
                LEFT JOIN (' . $this->get_select_json('file') . ') file_data ON file_data.file_id = p.mainImage
                LEFT JOIN page_type pt ON pt.`page_type_id` = p.`page_type_id`
                WHERE p.status = 1
                ';
        if ($limit && is_array($limit)) {
            $count = (int) $limit[0];
            if (isset($limit[1])) {
                $sql .= ' LIMIT ' . (int) $limit[1] . ', ' . $count;
            } else {
                $sql .= ' LIMIT ' . $count;
            }
        }
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $data = $query->result();
            foreach ($data as $key => &$value) {
                $data_values = json_decode($value->imagen_file);
                $value->imagen_file = $data_values;
            }
            return $this->filter_results(new Collection($data));
        }
        return false;
    }

    public function filter_results($collection = [])
    {
        // Load user relations (eliminates N+1 query)
        $collection = $this->loadUsersRelation($collection);
        
        // Set model_type for all pages
        foreach ($collection as &$value) {
            $value->{'model_type'} = "page";
        }

        // Decode JSON fields
        $collection = $this->decodeJsonFields($collection, ['json_content']);

        // Load main image relations (eliminates N+1 query)
        $this->load->model('Admin/FileModel');
        $collection = $this->loadFilesRelation($collection, 'mainImage', 'imagen_file');

        // Load thumbnail image relations (eliminates N+1 query)
        $collection = $this->loadFilesRelation($collection, 'thumbnailImage', 'thumbnail_image');

        $page_ids = array();
        foreach ($collection as $row) {
            if (isset($row->page_id) && $row->page_id) {
                $page_ids[] = $row->page_id;
            }
        }
        $page_ids = array_values(array_unique($page_ids));

        $data_by_page = array();
        if (!empty($page_ids)) {
            $this->db->where_in('page_id', $page_ids);
            $query = $this->db->get('page_data');
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $row) {
                    $pid = $row->page_id;
                    if (!isset($data_by_page[$pid])) {
                        $data_by_page[$pid] = array();
                    }
                    $decode_value = json_decode($row->_value);
                    if (gettype($decode_value) == "object" || gettype($decode_value) == "array") {
                        $data_by_page[$pid][$row->_key] = $decode_value;
                    } else {
                        $data_by_page[$pid][$row->_key] = $row->_value;
                    }
                }
            }
        }

        foreach ($collection as &$value) {
            $pid = isset($value->page_id) ? $value->page_id : null;
            $value->{'page_data'} = ($pid && isset($data_by_page[$pid])) ? $data_by_page[$pid] : array();
        }

        return $collection;
    }

    /**
     * Published blog posts whose EAV tags JSON contains $tag.
     *
     * @param string $tag
     * @return Collection|bool
     */
    public function public_blogs_by_tag($tag)
    {
        $tag = trim((string) $tag);
        if ($tag === '') {
            return false;
        }
        $this->db->reset_query();
        $this->db->select('page.*');
        $this->db->from('page');
        $this->db->join('page_data', 'page_data.page_id = page.page_id');
        $this->db->where('page.page_type_id', 2);
        $this->db->where('page.status', 1);
        $this->db->where('page_data._key', 'tags');
        $this->db->like('page_data._value', '"' . $this->db->escape_like_str($tag) . '"');
        $query = $this->db->get();
        if (!$query || $query->num_rows() < 1) {
            return false;
        }

        $rows = array();
        $seen = array();
        foreach ($query->result() as $row) {
            $pid = isset($row->page_id) ? (int) $row->page_id : 0;
            if ($pid < 1 || isset($seen[$pid])) {
                continue;
            }
            $seen[$pid] = true;
            $rows[] = $row;
        }
        if (empty($rows)) {
            return false;
        }

        $collection = $this->filter_results(new Collection($rows));
        $filtered = $collection->filter(function ($page) use ($tag) {
            if (!isset($page->page_data['tags'])) {
                return false;
            }
            $tags = $page->page_data['tags'];
            if (is_object($tags)) {
                $tags = (array) $tags;
            }
            return is_array($tags) && in_array($tag, $tags, true);
        });
        if (count($filtered) === 0) {
            return false;
        }
        return $filtered->values();
    }

    public function get_cloud_tags()
    {
        $sql = 'SELECT * FROM `page_data` pd WHERE pd._key = "tags"';
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $tags = [];
            foreach ($query->result() as $value) {
                $raw = isset($value->_value) ? strtolower($value->_value) : '';
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    $tags = array_merge($decoded, $tags);
                }
            }
            return array_unique($tags);
        }
        return false;
    }

    public function get_relate_pages_by_tags()
    {
        if (empty($this->page_id)) {
            return array();
        }
        $pages = $this->where(["page_id !=" => $this->page_id]);
        if (!$pages) {
            return array();
        }
        $result = array_filter($pages->toArray(), function ($page) {
            $targetTags = isset($this->page_data['tags']) ? $this->page_data['tags'] : [];
            $currentTags = isset($page->page_data['tags']) ? $page->page_data['tags'] : [];
            return !empty(array_intersect($currentTags, $targetTags));
        });
        return $result;
    }

    /**
     * Filas cortas para el home: user + imagen, sin page_data ni json_content.
     *
     * @param array $where
     * @param array $limit
     * @param array $order
     * @return Collection|false
     */
    public function dashboard_cards($where, $limit = array(), $order = array())
    {
        $this->db->select('page_id, title, status, user_id, date_create, date_update, content, mainImage');
        $this->db->from($this->table);
        if (!empty($where) && is_array($where)) {
            $this->db->where($where);
        }
        if ($limit) {
            if (is_array($limit)) {
                isset($limit[1]) ? $this->db->limit($limit[0], $limit[1]) : $this->db->limit($limit[0]);
            } else {
                $this->db->limit($limit);
            }
        }
        if ($order) {
            $this->db->order_by($order[0], $order[1]);
        } else {
            $this->db->order_by($this->primaryKey, 'DESC');
        }
        $query = $this->db->get();
        if ($query->num_rows() < 1) {
            return false;
        }
        $collection = new Collection($query->result());
        $collection = $this->loadUsersRelation($collection);
        $collection = $this->loadFilesRelation($collection, 'mainImage', 'imagen_file');
        return $collection;
    }
}
