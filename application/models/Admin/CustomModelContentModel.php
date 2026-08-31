<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class CustomModelContentModel extends MY_Model
{
    public $table = 'custom_model_content';
    public $primaryKey = 'custom_model_content_id';
    public $softDelete = true;
    public $searchable = array('title');

    public $hasOne = [
        'user' => ['user_id', 'Admin/UserModel', 'UserModel'],
        'custom_model' => ['custom_model_id', 'Admin/CustomModelModel', 'CustomModelModel'],
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function filter_results($collection = [])
    {
        if (empty($collection)) {
            return $collection;
        }
        $this->load->model('Admin/CustomModelModel');
        $this->loadUsersRelation($collection);
        $model_ids = array();
        $content_ids = array();
        foreach ($collection as $value) {
            if (isset($value->custom_model_id)) {
                $model_ids[] = (int) $value->custom_model_id;
            }
            if (isset($value->custom_model_content_id)) {
                $content_ids[] = (int) $value->custom_model_content_id;
            }
            $value->{'model_type'} = 'custom_model_content';
        }
        $models = $this->load_models_indexed($model_ids);
        $fields_by_model = array();
        foreach ($models as $mid => $model) {
            $fields_by_model[$mid] = $this->get_fields_map($mid);
        }
        $data_by_content = $this->load_content_data_batch($content_ids);

        foreach ($collection as $value) {
            $mid = isset($value->custom_model_id) ? (int) $value->custom_model_id : 0;
            if (isset($models[$mid])) {
                $value->{'custom_model'} = $models[$mid];
            }
            $cid = isset($value->custom_model_content_id) ? (int) $value->custom_model_content_id : 0;
            $rows = isset($data_by_content[$cid]) ? $data_by_content[$cid] : array();
            $field_map = isset($fields_by_model[$mid]) ? $fields_by_model[$mid] : array();
            $flat = $this->flatten_rows($rows, $field_map);
            $value->{'data'} = $flat;
            $type = isset($models[$mid]) ? $models[$mid] : (object) array();
            $resolved = $this->title_from_flat($type, $flat, $cid);
            if ($this->is_placeholder_title(isset($value->title) ? $value->title : '', $cid)) {
                $value->title = $resolved;
            }
            $this->attach_field_data_to_schema($value, $rows, $field_map);
        }

        return $collection;
    }

    public function as_single_object($collection)
    {
        $result = [];

        foreach ($collection as $item) {
            $data = [];
            if (!empty($item->data) && is_array($item->data)) {
                $data = $item->data;
            } elseif (!empty($item->custom_model->tabs)) {
                foreach ($item->custom_model->tabs as $tab) {
                    foreach ($tab->custom_model_fields as $form_field) {
                        if (isset($form_field->data->fielApiID) && isset($form_field->field_data->custom_model_content_data_value)) {
                            $data[$form_field->data->fielApiID] = $form_field->field_data->custom_model_content_data_value;
                        }
                    }
                }
            }
            $result[] = $data;
        }
        return $result;
    }

    /**
     * One content row + data for every field across tabs.
     *
     * @param object $data
     * @return array|false
     */
    public function save_data_form($data)
    {
        $custom_model_id = $data->custom_model_id;
        $status = $this->normalize_item_status(isset($data->status) ? $data->status : 1);
        $featured = $this->normalize_featured(isset($data->featured) ? $data->featured : 0);
        $sort_order = isset($data->sort_order) ? (int) $data->sort_order : 0;
        $title = $this->resolve_persisted_title($data, $custom_model_id, null);

        $form_content = array(
            'custom_model_id' => $custom_model_id,
            'form_tab_id' => 0,
            'user_id' => userdata('user_id'),
            'title' => $title,
            'sort_order' => $sort_order,
            'featured' => $featured,
            'status' => $status,
        );
        if ($status == 1) {
            $form_content['date_publish'] = date('Y-m-d H:i:s');
        }
        $this->db->insert('custom_model_content', $form_content);
        $custom_model_content_id = $this->db->insert_id();
        if (!$custom_model_content_id) {
            return false;
        }

        $tabs = isset($data->tabs) ? $data->tabs : array();
        foreach ($tabs as $tab) {
            $tab = is_array($tab) ? $tab : (array) $tab;
            $fields = isset($tab['custom_model_fields']) ? $tab['custom_model_fields'] : array();
            foreach ($fields as $form_field) {
                $form_field = is_array($form_field) ? $form_field : (array) $form_field;
                $this->db->insert('custom_model_content_data', array(
                    'custom_model_content_id' => $custom_model_content_id,
                    'custom_model_fields_id' => $form_field['custom_model_fields_id'],
                    'custom_model_content_data_value' => json_encode(isset($form_field['data']) ? $form_field['data'] : array()),
                ));
            }
        }

        return array(
            'custom_model_content_id' => $custom_model_content_id,
            'custom_model_id' => $custom_model_id,
        );
    }

    /**
     * @param object|array $data
     * @return array|false
     */
    public function update_data_form($data)
    {
        $data = (object) $data;
        $custom_model_content_id = $data->custom_model_content_id;
        $custom_model_id = $data->custom_model_id;
        $status = $this->normalize_item_status(isset($data->status) ? $data->status : 1);
        $featured = $this->normalize_featured(isset($data->featured) ? $data->featured : 0);
        $sort_order = isset($data->sort_order) ? (int) $data->sort_order : 0;
        $title = $this->resolve_persisted_title($data, $custom_model_id, $custom_model_content_id);

        $update = array(
            'title' => $title,
            'sort_order' => $sort_order,
            'featured' => $featured,
            'status' => $status,
        );
        if ($status == 1 && empty($data->date_publish)) {
            $update['date_publish'] = date('Y-m-d H:i:s');
        }
        $this->db->where('custom_model_content_id', $custom_model_content_id);
        $this->db->update('custom_model_content', $update);

        $tabs = isset($data->tabs) ? $data->tabs : array();
        foreach ($tabs as $tab) {
            $tab = is_array($tab) ? $tab : (array) $tab;
            $fields = isset($tab['custom_model_fields']) ? $tab['custom_model_fields'] : array();
            foreach ($fields as $form_field) {
                $form_field = is_array($form_field) ? $form_field : (array) $form_field;
                $field_id = $form_field['custom_model_fields_id'];
                $encoded = json_encode(isset($form_field['data']) ? $form_field['data'] : array());
                $existing = $this->db->get_where('custom_model_content_data', array(
                    'custom_model_content_id' => $custom_model_content_id,
                    'custom_model_fields_id' => $field_id,
                ))->row();
                if ($existing) {
                    $this->db->where('custom_model_content_data_id', $existing->custom_model_content_data_id);
                    $this->db->update('custom_model_content_data', array(
                        'custom_model_content_data_value' => $encoded,
                    ));
                } else {
                    $this->db->insert('custom_model_content_data', array(
                        'custom_model_content_id' => $custom_model_content_id,
                        'custom_model_fields_id' => $field_id,
                        'custom_model_content_data_value' => $encoded,
                    ));
                }
            }
        }

        return array(
            'custom_model_content_id' => $custom_model_content_id,
            'custom_model_id' => $custom_model_id,
        );
    }

    public function normalize_item_status($status)
    {
        $status = (int) $status;
        if ($status === 0) {
            return 2;
        }
        if ($status === 2) {
            return 2;
        }
        return 1;
    }

    public function normalize_featured($value)
    {
        if ($value === true || $value === 1 || $value === '1' || $value === 'true') {
            return 1;
        }
        return 0;
    }

    /**
     * Published items for get_collection / get_collection_items. No N+1.
     *
     * @param object $type CustomModelModel mapped
     * @param array $options
     * @return array
     */
    public function get_normalized_items($type, $options = array())
    {
        $custom_model_id = (int) $type->custom_model_id;
        $this->db->from($this->table);
        $this->db->where('custom_model_id', $custom_model_id);
        $this->db->where('status', 1);
        if (!empty($options['featured'])) {
            $this->db->where('featured', 1);
        }
        $this->db->order_by('featured', 'DESC');
        $this->db->order_by('sort_order', 'ASC');
        $this->db->order_by('custom_model_content_id', 'DESC');
        if (!empty($options['limit'])) {
            $this->db->limit((int) $options['limit']);
        }
        $query = $this->db->get();
        if (!$query || $query->num_rows() === 0) {
            return array();
        }
        $rows = $query->result();
        $ids = array();
        foreach ($rows as $row) {
            $ids[] = (int) $row->custom_model_content_id;
        }
        $field_map = $this->get_fields_map($custom_model_id);
        $data_by_content = $this->load_content_data_batch($ids);
        $items = array();
        foreach ($rows as $row) {
            $cid = (int) $row->custom_model_content_id;
            $flat = $this->apply_field_aliases($this->flatten_rows(isset($data_by_content[$cid]) ? $data_by_content[$cid] : array(), $field_map));
            $title = $this->title_from_flat($type, $flat, $cid);
            if (!$this->is_placeholder_title(isset($row->title) ? $row->title : '', $cid) && !empty($row->title)) {
                $title = $row->title;
            }
            $item = (object) array(
                'id' => $cid,
                'title' => $title,
                'featured' => !empty($row->featured),
                'sort_order' => (int) $row->sort_order,
                'date_publish' => $row->date_publish,
                'fields' => $flat,
            );
            $items[] = $item;
        }
        return $items;
    }

    public function get_fields_map($custom_model_id)
    {
        $sql = "SELECT f.custom_model_fields_id, f.field_name, f.displayName, f.component, f.custom_model_tab_id
            FROM custom_model_fields f
            INNER JOIN custom_model_tabs t ON t.custom_model_tab_id = f.custom_model_tab_id
            WHERE t.custom_model_id = ?";
        $fields = $this->db->query($sql, array($custom_model_id))->result();
        $ids = array();
        foreach ($fields as $field) {
            $ids[] = (int) $field->custom_model_fields_id;
            $field->fielApiID = $field->field_name;
        }
        if (!empty($ids)) {
            $this->db->where_in('custom_model_fields_id', $ids);
            $data_rows = $this->db->get('custom_model_fields_data')->result();
            $by_field = array();
            foreach ($data_rows as $d) {
                if (!isset($by_field[$d->custom_model_fields_id])) {
                    $by_field[$d->custom_model_fields_id] = array();
                }
                $by_field[$d->custom_model_fields_id][$d->_key] = $d->_value;
            }
            foreach ($fields as $field) {
                if (isset($by_field[$field->custom_model_fields_id]['fielApiID']) && $by_field[$field->custom_model_fields_id]['fielApiID'] !== '') {
                    $field->fielApiID = $by_field[$field->custom_model_fields_id]['fielApiID'];
                }
                $field->data_keys = isset($by_field[$field->custom_model_fields_id]) ? $by_field[$field->custom_model_fields_id] : array();
            }
        }
        $map = array();
        foreach ($fields as $field) {
            $map[(int) $field->custom_model_fields_id] = $field;
        }
        return $map;
    }

    protected function load_content_data_batch($content_ids)
    {
        $out = array();
        if (empty($content_ids)) {
            return $out;
        }
        $this->db->where_in('custom_model_content_id', $content_ids);
        $rows = $this->db->get('custom_model_content_data')->result();
        foreach ($rows as $row) {
            $cid = (int) $row->custom_model_content_id;
            if (!isset($out[$cid])) {
                $out[$cid] = array();
            }
            $out[$cid][] = $row;
        }
        return $out;
    }

    protected function load_models_indexed($model_ids)
    {
        $out = array();
        $model_ids = array_values(array_unique(array_filter($model_ids)));
        if (empty($model_ids)) {
            return $out;
        }
        $this->load->model('Admin/CustomModelModel');
        foreach ($model_ids as $id) {
            $m = new CustomModelModel();
            if ($m->find($id)) {
                $out[$id] = $m->as_data();
            }
        }
        return $out;
    }

    protected function flatten_rows($rows, $field_map)
    {
        $flat = array();
        foreach ($rows as $row) {
            $fid = (int) $row->custom_model_fields_id;
            $meta = isset($field_map[$fid]) ? $field_map[$fid] : null;
            $api = $meta && !empty($meta->fielApiID) ? $meta->fielApiID : (string) $fid;
            $flat[$api] = $this->flatten_field_value($row->custom_model_content_data_value, $meta);
        }
        return $flat;
    }

    public function flatten_field_value($raw, $meta)
    {
        if (is_string($raw)) {
            $decoded = json_decode($raw);
            if (json_last_error() === JSON_ERROR_NONE) {
                $raw = $decoded;
            }
        }
        $component = ($meta && isset($meta->component)) ? $meta->component : '';
        if ($component === 'formImageSelector' || $this->value_looks_like_image($raw)) {
            return $this->flatten_image_value($raw);
        }
        if (is_object($raw)) {
            $raw = (array) $raw;
        }
        if (is_array($raw)) {
            if (isset($raw['title']) && !is_array($raw['title'])) {
                return $raw['title'];
            }
            if (isset($raw['text']) && !is_array($raw['text'])) {
                return $raw['text'];
            }
            if (isset($raw['value']) && !is_array($raw['value'])) {
                return $raw['value'];
            }
            foreach ($raw as $v) {
                if (!is_array($v) && !is_object($v)) {
                    return $v;
                }
            }
            return '';
        }
        return $raw;
    }

    protected function value_looks_like_image($raw)
    {
        if (is_object($raw) && isset($raw->image)) {
            return true;
        }
        if (is_array($raw) && isset($raw['image'])) {
            return true;
        }
        return false;
    }

    protected function flatten_image_value($raw)
    {
        if (is_object($raw)) {
            $raw = (array) $raw;
        }
        $files = array();
        if (is_array($raw) && isset($raw['image'])) {
            $files = $raw['image'];
        } elseif (is_array($raw)) {
            $files = $raw;
        }
        $first = null;
        if (is_array($files) && !empty($files)) {
            $files = array_values($files);
            $first = $files[0];
        }
        $file = is_object($first) ? $first : (object) (is_array($first) ? $first : array());
        $out = new stdClass();
        $out->url = $this->file_public_url($file);
        $out->file = $file;
        return $out;
    }

    protected function file_public_url($file)
    {
        if (!is_object($file)) {
            return '';
        }
        if (!empty($file->file_front_path)) {
            $path = $file->file_front_path;
            if (strpos($path, 'http') === 0) {
                return $path;
            }
            return rtrim(base_url(), '/') . '/' . ltrim($path, '/');
        }
        if (empty($file->file_path) || empty($file->file_name)) {
            return '';
        }
        $path = $file->file_path;
        if (substr($path, 0, 2) === './') {
            $path = substr($path, 2);
        }
        $ext = isset($file->file_type) ? $file->file_type : '';
        return base_url($path . $file->file_name . ($ext ? '.' . $ext : ''));
    }

    protected function derive_title($custom_model_id, $tabs)
    {
        $this->load->model('Admin/CustomModelModel');
        $type = new CustomModelModel();
        $type->find($custom_model_id);
        $title_field = !empty($type->title_field) ? $type->title_field : null;
        $first_title = '';
        $first_any = '';
        foreach ($tabs as $tab) {
            $tab = is_array($tab) ? $tab : (array) $tab;
            $fields = isset($tab['custom_model_fields']) ? $tab['custom_model_fields'] : array();
            foreach ($fields as $field) {
                $field = is_array($field) ? $field : (array) $field;
                $data = isset($field['data']) ? (array) $field['data'] : array();
                $api = '';
                if (isset($field['data']) && is_object($field['data']) && isset($field['data']->fielApiID)) {
                    $api = $field['data']->fielApiID;
                } elseif (isset($data['fielApiID'])) {
                    $api = $data['fielApiID'];
                } elseif (isset($field['field_name'])) {
                    $api = $field['field_name'];
                }
                $value = '';
                if (isset($data['title'])) {
                    $value = $data['title'];
                } elseif (isset($data['text'])) {
                    $value = $data['text'];
                } else {
                    foreach ($data as $v) {
                        if (!is_array($v) && !is_object($v) && $v !== '') {
                            $value = $v;
                            break;
                        }
                    }
                }
                if ($first_any === '' && $value !== '') {
                    $first_any = $value;
                }
                $component = isset($field['component']) ? $field['component'] : '';
                if ($first_title === '' && $component === 'formFieldTitle' && $value !== '') {
                    $first_title = $value;
                }
                if ($title_field && $api === $title_field && $value !== '') {
                    return $value;
                }
            }
        }
        if ($first_title !== '') {
            return $first_title;
        }
        return $first_any;
    }

    protected function title_from_flat($type, $flat, $cid)
    {
        if (!empty($type->title_field) && $this->scalar_text($flat, $type->title_field) !== '') {
            return $this->scalar_text($flat, $type->title_field);
        }
        if ($this->scalar_text($flat, 'title') !== '') {
            return $this->scalar_text($flat, 'title');
        }
        foreach ($flat as $v) {
            if (!is_object($v) && !is_array($v) && $v !== '' && $v !== null) {
                return $v;
            }
        }
        return 'Collection #' . $cid;
    }

    /**
     * @param array $flat
     * @param string $key
     * @return string
     */
    protected function scalar_text($flat, $key)
    {
        if (!isset($flat[$key]) || is_object($flat[$key]) || is_array($flat[$key])) {
            return '';
        }
        return trim((string) $flat[$key]);
    }

    protected function attach_field_data_to_schema($value, $rows, $field_map)
    {
        if (empty($value->custom_model) || empty($value->custom_model->tabs)) {
            return;
        }
        $by_field = array();
        foreach ($rows as $row) {
            $by_field[(int) $row->custom_model_fields_id] = $row;
        }
        foreach ($value->custom_model->tabs as $tab) {
            if (empty($tab->custom_model_fields)) {
                continue;
            }
            foreach ($tab->custom_model_fields as $form_field) {
                $fid = isset($form_field->custom_model_fields_id) ? (int) $form_field->custom_model_fields_id : 0;
                if (isset($by_field[$fid])) {
                    $form_field->field_data = $by_field[$fid];
                    if (is_string($form_field->field_data->custom_model_content_data_value)) {
                        $decoded = json_decode($form_field->field_data->custom_model_content_data_value);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $form_field->field_data->custom_model_content_data_value = $decoded;
                        }
                    }
                }
            }
        }
    }

    protected function apply_query_order($order)
    {
        if ($order && isset($order[0]) && $order[0] === 'collection_items') {
            $this->db->order_by('featured', 'DESC');
            $this->db->order_by('sort_order', 'ASC');
            $this->db->order_by('custom_model_content_id', 'DESC');
            return;
        }
        parent::apply_query_order($order);
    }

    /**
     * Search title column and EAV values (legacy items store the title only in data).
     *
     * @param array $filters
     * @param string $search
     * @return void
     */
    protected function apply_list_filters($filters, $search = '')
    {
        parent::apply_list_filters($filters, '');
        $search = is_string($search) ? trim($search) : '';
        if ($search === '') {
            return;
        }
        $like = $this->db->escape('%' . $this->db->escape_like_str($search) . '%');
        $this->db->group_start();
        $this->db->like('title', $search);
        $this->db->or_where(
            'custom_model_content_id IN (SELECT custom_model_content_id FROM custom_model_content_data WHERE custom_model_content_data_value LIKE ' . $like . " ESCAPE '!')",
            null,
            false
        );
        $this->db->group_end();
    }

    /**
     * @param object $data
     * @param int $custom_model_id
     * @param int|null $content_id
     * @return string
     */
    protected function resolve_persisted_title($data, $custom_model_id, $content_id)
    {
        $derived = $this->derive_title($custom_model_id, isset($data->tabs) ? $data->tabs : array());
        if ($derived !== '') {
            return $derived;
        }
        $title = isset($data->title) ? trim((string) $data->title) : '';
        if (!$this->is_placeholder_title($title, $content_id) && $title !== '') {
            return $title;
        }
        if ($content_id) {
            return 'Collection #' . $content_id;
        }
        return $title;
    }

    /**
     * @param string $title
     * @param int|null $cid
     * @return bool
     */
    protected function is_placeholder_title($title, $cid)
    {
        if ($title === null || $title === '') {
            return true;
        }
        if ($cid && $title === ('Collection #' . $cid)) {
            return true;
        }
        return (bool) preg_match('/^Collection #\d+$/', (string) $title);
    }

    /**
     * Accept image|imagen and url|link without renaming stored Api IDs.
     *
     * @param array $flat
     * @return array
     */
    protected function apply_field_aliases($flat)
    {
        if (!is_array($flat)) {
            return array();
        }
        $pairs = array(
            'image' => array('imagen', 'photo', 'picture'),
            'url' => array('link', 'href'),
        );
        foreach ($pairs as $canonical => $alts) {
            if (!empty($flat[$canonical])) {
                continue;
            }
            foreach ($alts as $alt) {
                if (!empty($flat[$alt])) {
                    $flat[$canonical] = $flat[$alt];
                    break;
                }
            }
        }
        if (empty($flat['imagen']) && !empty($flat['image'])) {
            $flat['imagen'] = $flat['image'];
        }
        if (empty($flat['link']) && !empty($flat['url'])) {
            $flat['link'] = $flat['url'];
        }
        return $flat;
    }
}
