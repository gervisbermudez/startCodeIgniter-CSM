<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use Tightenco\Collect\Support\Collection;

class EventModel extends MY_Model
{

    public $primaryKey = 'event_id';
    public $table = "events";
    public $softDelete = true;
    public $computed = ["mainImage" => "mainImage"];
    public $searchable = array('name', 'subtitle', 'address', 'slug');
    public $hasOne = [
        'user' => ['user_id', 'Admin/UserModel', 'UserModel'],
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function mainImage()
    {
        if (empty($this->mainImage)) {
            return null;
        }

        $this->load->model('Admin/FileModel');
        $file = new FileModel();
        $file->find($this->mainImage);

        if (!$file->{$file->primaryKey}) {
            return null;
        }

        $imagen_file = $file->as_data();
        $imagen_file->{'file_front_path'} = new stdClass();
        $imagen_file->{'file_front_path'} = $file->getFileFrontPath();
        return $imagen_file;
    }

    public function filter_results($collection = [])
    {
        return $this->loadRelations($collection, [
            'user' => ['field' => 'user_id'],
            'file' => ['field' => 'mainImage', 'target' => 'imagen_file']
        ]);
    }

    /**
     * Admin list: do not filter by status.
     * @return Collection|false
     */
    public function get_all($limit = array(), $order = array())
    {
        if ($limit && is_array($limit)) {
            if (isset($limit[1])) {
                $this->db->limit($limit[0], $limit[1]);
            } else {
                $this->db->limit($limit[0]);
            }
        }

        $this->apply_query_order($order ? $order : array('date_start', 'DESC'));

        $query = $this->db->get($this->table);
        if ($query->num_rows() > 0) {
            return new Collection($this->filter_results($query->result()));
        }

        return false;
    }

    /**
     * Published + visible happenings with date_start >= now.
     * @param int|null $limit
     * @return Collection|false
     */
    public function upcoming($limit = null)
    {
        $this->db->where('status', 1);
        $this->db->where('visibility', 1);
        $this->db->where('date_start >=', date('Y-m-d H:i:s'));
        $this->db->order_by('date_start', 'ASC');
        if ($limit) {
            $this->db->limit((int) $limit);
        }
        $query = $this->db->get($this->table);
        if ($query->num_rows() > 0) {
            return new Collection($this->filter_results($query->result()));
        }
        return false;
    }

    /**
     * Happenings whose date_start falls in [from, to]. Includes drafts.
     *
     * @param string $from Y-m-d H:i:s
     * @param string $to Y-m-d H:i:s
     * @param int $limit
     * @return Collection|false
     */
    public function in_range($from, $to, $limit = 80)
    {
        $this->db->where('status !=', 0);
        $this->db->where('date_start >=', $from);
        $this->db->where('date_start <=', $to);
        $this->db->order_by('date_start', 'ASC');
        $this->db->limit((int) $limit);
        $query = $this->db->get($this->table);
        if ($query->num_rows() > 0) {
            return new Collection($this->filter_results($query->result()));
        }
        return false;
    }

    /**
     * Published + visible happenings already finished.
     * @param int|null $limit
     * @return Collection|false
     */
    public function past($limit = null)
    {
        $this->db->where('status', 1);
        $this->db->where('visibility', 1);
        $this->db->group_start();
        $this->db->where('date_end <', date('Y-m-d H:i:s'));
        $this->db->or_group_start();
        $this->db->where('date_end IS NULL', null, false);
        $this->db->where('date_start <', date('Y-m-d H:i:s'));
        $this->db->group_end();
        $this->db->group_end();
        $this->db->order_by('date_start', 'DESC');
        if ($limit) {
            $this->db->limit((int) $limit);
        }
        $query = $this->db->get($this->table);
        if ($query->num_rows() > 0) {
            return new Collection($this->filter_results($query->result()));
        }
        return false;
    }

    /**
     * Published + visible by slug. Maps onto this instance.
     * @param string $slug
     * @return EventModel|false
     */
    public function find_by_slug($slug)
    {
        $slug = trim((string) $slug);
        if ($slug === '') {
            return false;
        }
        $this->db->where('slug', $slug);
        $this->db->where('status', 1);
        $this->db->where('visibility', 1);
        $query = $this->db->get($this->table);
        if ($query->num_rows() < 1) {
            return false;
        }
        $rows = $this->filter_results($query->result());
        $row = null;
        if ($rows instanceof Collection) {
            $row = $rows->first();
        } elseif (is_array($rows) && isset($rows[0])) {
            $row = $rows[0];
        }
        if (!$row) {
            return false;
        }
        $this->mapfields($row);
        return $this;
    }

    /**
     * Unique ASCII slug from name. Collision: append -{event_id} then -2, -3…
     * @param string $name
     * @param mixed $event_id
     * @return string
     */
    public function ensure_slug($name, $event_id = null)
    {
        $slug = url_title(convert_accented_characters((string) $name), 'dash', true);
        if ($slug === '') {
            $slug = 'event';
        }
        $base = $slug;
        if (!$this->slug_taken($slug, $event_id)) {
            return $slug;
        }
        if ($event_id) {
            $with_id = $base . '-' . $event_id;
            if (!$this->slug_taken($with_id, $event_id)) {
                return $with_id;
            }
        }
        $n = 2;
        while ($n < 100) {
            $candidate = $base . '-' . $n;
            if (!$this->slug_taken($candidate, $event_id)) {
                return $candidate;
            }
            $n++;
        }
        return $base . '-' . uniqid();
    }

    /**
     * @param string $slug
     * @param mixed $exclude_event_id
     * @return bool
     */
    public function slug_taken($slug, $exclude_event_id = null)
    {
        $this->db->from($this->table);
        $this->db->where('slug', $slug);
        if ($exclude_event_id !== null && $exclude_event_id !== '' && $exclude_event_id !== false) {
            $this->db->where('event_id !=', $exclude_event_id);
        }
        $this->db->limit(1);
        return $this->db->get()->num_rows() > 0;
    }

    /**
     * @param array $filters
     * @param string $search
     * @return void
     */
    protected function apply_list_filters($filters, $search = '')
    {
        $when = '';
        if (isset($filters['_when'])) {
            $when = $filters['_when'];
            unset($filters['_when']);
        }
        parent::apply_list_filters($filters, $search);
        $this->apply_when_scope($when);
    }

    /**
     * MySQL 5.7: nulls last when ordering by date_start DESC.
     * @param array $order
     * @return void
     */
    protected function apply_query_order($order)
    {
        if ($order && isset($order[0]) && $order[0] === 'date_start') {
            $direction = isset($order[1]) ? $order[1] : 'DESC';
            $this->db->order_by('date_start IS NULL', 'ASC', false);
            $this->db->order_by('date_start', $direction);
            return;
        }
        parent::apply_query_order($order);
    }

    /**
     * Admin when=upcoming|past (date only). Trash excluded.
     * @param string $when
     * @return void
     */
    protected function apply_when_scope($when)
    {
        if ($when !== 'upcoming' && $when !== 'past') {
            return;
        }
        $now = date('Y-m-d H:i:s');
        $this->db->where('status !=', 0);
        if ($when === 'upcoming') {
            $this->db->where('date_start IS NOT NULL', null, false);
            $this->db->where('date_start >=', $now);
            return;
        }
        $this->db->group_start();
        $this->db->where('date_end <', $now);
        $this->db->or_group_start();
        $this->db->where('date_end IS NULL', null, false);
        $this->db->where('date_start <', $now);
        $this->db->group_end();
        $this->db->group_end();
    }

    /**
     * Flat rows that overlap [from, to). No relations.
     *
     * @param string $from Y-m-d H:i:s inclusive
     * @param string $to Y-m-d H:i:s exclusive
     * @return array
     */
    public function calendar_range($from, $to)
    {
        $this->db->select('event_id, name, slug, date_start, date_end, all_day, status, visibility, address, location_type, online_url');
        $this->db->from($this->table);
        $this->db->where('status !=', 0);
        $this->db->where('date_start IS NOT NULL', null, false);
        $this->db->where('date_start <', $to);
        $this->db->where('COALESCE(date_end, date_start) >= ' . $this->db->escape($from), null, false);
        $this->db->order_by('date_start', 'ASC');
        $query = $this->db->get();
        if ($query->num_rows() < 1) {
            return array();
        }
        return $query->result();
    }

}
