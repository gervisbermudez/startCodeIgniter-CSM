<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Export/import payload for Data → Export/Import.
 * Version 1: pages + config. Version 2: CMS content groups (no users, no binaries).
 */
class Content_export
{
    /** @var CI_Controller */
    protected $ci;

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->database();
        $this->ci->load->model('Admin/PageModel');
        $this->ci->load->model('Admin/SiteConfigModel');
    }

    /**
     * @return string[]
     */
    public function group_keys()
    {
        return array(
            'pages',
            'config',
            'menus',
            'fragmentos',
            'categories',
            'albums',
            'videos',
            'events',
            'collections',
            'siteforms',
        );
    }

    /**
     * Lightweight picker catalog. Full rows live in dump() only.
     *
     * @param bool $unpublishedPages
     * @return array
     */
    public function catalog($unpublishedPages = false)
    {
        $pageStatuses = $unpublishedPages ? array(1, 2, 3) : array(1);
        return array(
            'pages' => $this->catalog_rows('page', 'page_id, title, path, status', 'page_id', $pageStatuses),
            'config' => $this->catalog_rows('site_config', 'site_config_id, config_name, config_label, config_type, status', 'site_config_id'),
            'menus' => $this->catalog_rows('menu', 'menu_id, name, status', 'menu_id'),
            'fragmentos' => $this->catalog_rows('fragmentos', 'fragment_id, name, type, status', 'fragment_id'),
            'categories' => $this->catalog_rows('categories', 'categorie_id, name, type, parent_id, status', 'categorie_id'),
            'albums' => $this->catalog_rows('album', 'album_id, name, status', 'album_id'),
            'videos' => $this->catalog_video_rows(),
            'events' => $this->catalog_rows('events', 'event_id, name, slug, date_start, status', 'event_id'),
            'collections' => $this->catalog_rows('custom_model', 'custom_model_id, form_name, slug, status', 'custom_model_id'),
            'siteforms' => $this->catalog_rows('siteform', 'siteform_id, name, status', 'siteform_id'),
        );
    }

    /**
     * @param mixed $exportData
     * @return array
     */
    public function dump($exportData)
    {
        $unpublished = $this->flag_from_selection($exportData, 'unpublished_pages');
        $pageStatuses = $unpublished ? array(1, 2, 3) : array(1);

        $payload = array(
            'version' => 2,
            'exported_at' => date('c'),
        );
        $payload['pages'] = $this->build_pages_export($this->selected_id_list($exportData, 'pages'), $pageStatuses);
        $payload['config'] = $this->build_config_export($this->selected_id_list($exportData, 'config'));
        $payload['menus'] = $this->build_menus_export($this->selected_id_list($exportData, 'menus'));
        $payload['fragmentos'] = $this->build_fragmentos_export($this->selected_id_list($exportData, 'fragmentos'));
        $payload['categories'] = $this->build_categories_export($this->selected_id_list($exportData, 'categories'));
        $payload['albums'] = $this->build_albums_export($this->selected_id_list($exportData, 'albums'));
        $payload['videos'] = $this->build_videos_export($this->selected_id_list($exportData, 'videos'));
        $payload['events'] = $this->build_events_export($this->selected_id_list($exportData, 'events'));
        $payload['collections'] = $this->build_collections_export($this->selected_id_list($exportData, 'collections'));
        $payload['siteforms'] = $this->build_siteforms_export($this->selected_id_list($exportData, 'siteforms'));
        return $payload;
    }

    /**
     * @param array $payload
     * @return bool
     */
    public function payload_is_empty($payload)
    {
        foreach ($this->group_keys() as $key) {
            if (!empty($payload[$key])) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param mixed $selection
     * @return bool
     */
    public function selection_is_empty($selection)
    {
        foreach ($this->group_keys() as $key) {
            if (!empty($this->selected_id_list($selection, $key))) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param array $payload
     * @return string
     */
    public function summarize_payload($payload)
    {
        $parts = array();
        foreach ($this->group_keys() as $key) {
            $n = isset($payload[$key]) && is_array($payload[$key]) ? count($payload[$key]) : 0;
            $parts[] = $n . ' ' . $key;
        }
        return implode(', ', $parts);
    }

    /**
     * @param object $fileContent
     * @param mixed $selection
     * @return array|false
     */
    public function import_payload($fileContent, $selection)
    {
        $counts = array();
        foreach ($this->group_keys() as $key) {
            $counts[$key] = 0;
        }

        $version = 1;
        if (isset($fileContent->version)) {
            $version = (int) $fileContent->version;
        }

        if (!$this->import_group_list($fileContent, 'pages', 'page_id', $this->selected_id_list($selection, 'pages'), array($this, 'import_page_row'), $counts)) {
            return false;
        }
        if (!$this->import_group_list($fileContent, 'config', 'site_config_id', $this->selected_id_list($selection, 'config'), array($this, 'import_config_row'), $counts)) {
            return false;
        }

        if ($version >= 2) {
            $importers = array(
                'categories' => array('categorie_id', 'import_category_row'),
                'fragmentos' => array('fragment_id', 'import_fragmento_row'),
                'videos' => array('video_id', 'import_video_row'),
                'events' => array('event_id', 'import_event_row'),
                'menus' => array('menu_id', 'import_menu_row'),
                'albums' => array('album_id', 'import_album_row'),
                'collections' => array('custom_model_id', 'import_collection_row'),
                'siteforms' => array('siteform_id', 'import_siteform_row'),
            );
            foreach ($importers as $key => $spec) {
                if (!$this->import_group_list($fileContent, $key, $spec[0], $this->selected_id_list($selection, $key), array($this, $spec[1]), $counts)) {
                    return false;
                }
            }
        }

        return $counts;
    }

    /**
     * @param array $counts
     * @return string
     */
    public function summarize_counts($counts)
    {
        $parts = array();
        foreach ($this->group_keys() as $key) {
            $n = isset($counts[$key]) ? (int) $counts[$key] : 0;
            $parts[] = $n . ' ' . $key;
        }
        return implode(', ', $parts);
    }

    /**
     * @param object $fileContent
     * @param string $key
     * @param string $idField
     * @param int[] $selectedIds
     * @param callable $importer
     * @param array $counts
     * @return bool
     */
    protected function import_group_list($fileContent, $key, $idField, $selectedIds, $importer, &$counts)
    {
        if (empty($selectedIds) || !isset($fileContent->{$key}) || !is_array($fileContent->{$key})) {
            return true;
        }
        $rows = $fileContent->{$key};
        if ($key === 'categories') {
            $rows = $this->sort_categories_parents_first($rows);
        }
        foreach ($rows as $value) {
            if (!is_object($value) || !$this->is_selected_id(isset($value->{$idField}) ? $value->{$idField} : 0, $selectedIds)) {
                continue;
            }
            if (!call_user_func($importer, $value)) {
                return false;
            }
            $counts[$key]++;
        }
        return true;
    }

    /**
     * @param string $table
     * @param string $select
     * @param string $orderCol
     * @param int[] $statuses
     * @return array
     */
    protected function catalog_rows($table, $select, $orderCol, $statuses = null)
    {
        if ($statuses === null) {
            $statuses = array(1);
        }
        $this->ci->db->reset_query();
        $this->ci->db->select($select);
        $this->ci->db->from($table);
        if (count($statuses) === 1) {
            $this->ci->db->where('status', (int) $statuses[0]);
        } else {
            $this->ci->db->where_in('status', $statuses);
        }
        $this->ci->db->order_by($orderCol, 'ASC');
        $query = $this->ci->db->get();
        return $this->query_rows($query);
    }

    /**
     * @return array
     */
    protected function catalog_video_rows()
    {
        $this->ci->db->reset_query();
        $this->ci->db->select('video_id, nam AS name, status', false);
        $this->ci->db->from('video');
        $this->ci->db->where('status', 1);
        $this->ci->db->order_by('video_id', 'ASC');
        return $this->query_rows($this->ci->db->get());
    }

    /**
     * @param object|false $query
     * @return array
     */
    protected function query_rows($query)
    {
        $rows = array();
        if ($query && $query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    /**
     * @param mixed $values
     * @return int[]
     */
    public function normalize_id_list($values)
    {
        if (!is_array($values)) {
            if ($values === null || $values === '' || $values === false) {
                return array();
            }
            $values = array($values);
        }
        $ids = array();
        foreach ($values as $value) {
            $id = (int) $value;
            if ($id > 0) {
                $ids[$id] = $id;
            }
        }
        return array_values($ids);
    }

    /**
     * @param mixed $selection
     * @param string $key
     * @return int[]
     */
    public function selected_id_list($selection, $key)
    {
        if (is_object($selection) && isset($selection->{$key})) {
            return $this->normalize_id_list($selection->{$key});
        }
        if (is_array($selection) && isset($selection[$key])) {
            return $this->normalize_id_list($selection[$key]);
        }
        return array();
    }

    /**
     * @param mixed $selection
     * @param string $key
     * @return bool
     */
    protected function flag_from_selection($selection, $key)
    {
        $raw = null;
        if (is_object($selection) && isset($selection->{$key})) {
            $raw = $selection->{$key};
        } elseif (is_array($selection) && isset($selection[$key])) {
            $raw = $selection[$key];
        }
        return !empty($raw) && $raw !== '0' && $raw !== 0;
    }

    /**
     * @param mixed $id
     * @param int[] $selectedIds
     * @return bool
     */
    protected function is_selected_id($id, $selectedIds)
    {
        if (empty($selectedIds)) {
            return false;
        }
        return in_array((int) $id, $selectedIds, true);
    }

    /**
     * @param object|array $row
     * @param string[] $fields
     * @return array
     */
    protected function pick_fields($row, $fields)
    {
        $out = array();
        foreach ($fields as $field) {
            if (is_object($row) && property_exists($row, $field)) {
                $out[$field] = $row->{$field};
            } elseif (is_array($row) && array_key_exists($field, $row)) {
                $out[$field] = $row[$field];
            }
        }
        return $out;
    }

    /**
     * @param mixed $value
     * @return string|null
     */
    protected function encode_json_field($value)
    {
        if (is_array($value) || is_object($value)) {
            return json_encode($value);
        }
        return $value;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    protected function decode_json_field($value)
    {
        if (is_array($value) || is_object($value)) {
            return $value;
        }
        if (!is_string($value) || $value === '') {
            return $value;
        }
        $decoded = json_decode($value);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }
        return $value;
    }

    /**
     * @param object $model
     * @return object
     */
    protected function without_import_relations($model)
    {
        $model->hasOne = array();
        $model->hasMany = array();
        $model->computed = array();
        return $model;
    }

    /**
     * @param object $model
     * @param object $source
     * @param string[] $fields
     * @return void
     */
    protected function apply_allowlist($model, $source, $fields)
    {
        foreach ($fields as $field) {
            if (is_object($source) && property_exists($source, $field)) {
                $model->{$field} = $source->{$field};
            }
        }
    }

    /**
     * Drop nested user records; keep scalar emails (config values, page copy).
     *
     * @param mixed $data
     * @return mixed
     */
    protected function strip_sensitive($data)
    {
        if (is_object($data)) {
            $arr = array();
            foreach ($data as $key => $value) {
                $arr[$key] = $value;
            }
            $data = $arr;
        }
        if (!is_array($data)) {
            return $data;
        }
        if ($this->looks_like_user_record($data)) {
            return array();
        }
        $out = array();
        foreach ($data as $key => $value) {
            if ($key === 'password') {
                continue;
            }
            if ($key === 'user' && (is_array($value) || is_object($value))) {
                continue;
            }
            $out[$key] = $this->strip_sensitive($value);
        }
        return $out;
    }

    /**
     * @param array $data
     * @return bool
     */
    protected function looks_like_user_record($data)
    {
        return isset($data['user_id']) && isset($data['username']) && (isset($data['email']) || array_key_exists('password', $data));
    }

    /**
     * @param int[] $ids
     * @param int[] $statuses
     * @return array
     */
    protected function build_pages_export($ids, $statuses = null)
    {
        $fields = array(
            'page_id',
            'path',
            'template',
            'title',
            'subtitle',
            'content',
            'json_content',
            'page_type_id',
            'visibility',
            'categorie_id',
            'subcategorie_id',
            'status',
            'layout',
            'mainImage',
            'thumbnailImage',
            'date_publish',
        );
        $rows = $this->fetch_by_ids('page', $fields, 'page_id', $ids, $statuses === null ? array(1) : $statuses);
        $exportedIds = array();
        foreach ($rows as $row) {
            $exportedIds[] = (int) $row->page_id;
        }
        $dataByPage = $this->load_page_data_map($exportedIds);
        $pages = array();
        foreach ($rows as $row) {
            $item = $this->pick_fields($row, $fields);
            if (isset($item['json_content'])) {
                $item['json_content'] = $this->strip_sensitive($this->decode_json_field($item['json_content']));
            }
            $pid = (int) $row->page_id;
            $pageData = isset($dataByPage[$pid]) ? $dataByPage[$pid] : array();
            $item['page_data'] = $this->strip_sensitive($pageData);
            $pages[] = $item;
        }
        return $pages;
    }

    /**
     * @param int[] $ids
     * @return array
     */
    protected function build_config_export($ids)
    {
        $fields = array(
            'site_config_id',
            'config_name',
            'config_value',
            'config_description',
            'config_label',
            'config_type',
            'config_data',
            'readonly',
            'status',
        );
        $rows = $this->fetch_by_ids('site_config', $fields, 'site_config_id', $ids);
        $config = array();
        foreach ($rows as $row) {
            $item = $this->pick_fields($row, $fields);
            if (isset($item['config_data'])) {
                $item['config_data'] = $this->decode_json_field($item['config_data']);
            }
            $config[] = $item;
        }
        return $config;
    }

    /**
     * @param int[] $ids
     * @return array
     */
    protected function build_menus_export($ids)
    {
        $fields = array('menu_id', 'name', 'template', 'position', 'status');
        $rows = $this->fetch_by_ids('menu', $fields, 'menu_id', $ids);
        $menuIds = array();
        foreach ($rows as $row) {
            $menuIds[] = (int) $row->menu_id;
        }
        $itemsByMenu = $this->load_child_map('menu_items', 'menu_id', $menuIds, array(
            'menu_item_id',
            'menu_id',
            'menu_item_parent_id',
            'order',
            'model_id',
            'model',
            'item_type',
            'item_name',
            'item_label',
            'item_link',
            'item_title',
            'item_target',
            'date_publish',
            'status',
        ), 'order');
        $menus = array();
        foreach ($rows as $row) {
            $item = $this->pick_fields($row, $fields);
            $mid = (int) $row->menu_id;
            $item['menu_items'] = isset($itemsByMenu[$mid]) ? $itemsByMenu[$mid] : array();
            $menus[] = $item;
        }
        return $menus;
    }

    /**
     * @param int[] $ids
     * @return array
     */
    protected function build_fragmentos_export($ids)
    {
        $fields = array('fragment_id', 'name', 'description', 'type', 'status');
        $rows = $this->fetch_by_ids('fragmentos', $fields, 'fragment_id', $ids);
        $out = array();
        foreach ($rows as $row) {
            $out[] = $this->pick_fields($row, $fields);
        }
        return $out;
    }

    /**
     * @param int[] $ids
     * @return array
     */
    protected function build_categories_export($ids)
    {
        $fields = array('categorie_id', 'name', 'description', 'type', 'parent_id', 'status');
        $rows = $this->fetch_by_ids('categories', $fields, 'categorie_id', $ids);
        $names = array();
        foreach ($rows as $row) {
            $names[(int) $row->categorie_id] = $row->name;
        }
        $missingParents = array();
        foreach ($rows as $row) {
            $pid = (int) $row->parent_id;
            if ($pid > 0 && !isset($names[$pid])) {
                $missingParents[$pid] = $pid;
            }
        }
        if (!empty($missingParents)) {
            $this->ci->db->reset_query();
            $this->ci->db->select('categorie_id, name');
            $this->ci->db->from('categories');
            $this->ci->db->where_in('categorie_id', array_values($missingParents));
            $q = $this->ci->db->get();
            if ($q && $q->num_rows() > 0) {
                foreach ($q->result() as $parent) {
                    $names[(int) $parent->categorie_id] = $parent->name;
                }
            }
        }
        $out = array();
        foreach ($rows as $row) {
            $item = $this->pick_fields($row, $fields);
            $pid = (int) $row->parent_id;
            $item['parent_name'] = ($pid > 0 && isset($names[$pid])) ? $names[$pid] : '';
            $out[] = $item;
        }
        return $out;
    }

    /**
     * @param int[] $ids
     * @return array
     */
    protected function build_albums_export($ids)
    {
        $fields = array('album_id', 'name', 'description', 'date_publish', 'status');
        $rows = $this->fetch_by_ids('album', $fields, 'album_id', $ids);
        $albumIds = array();
        foreach ($rows as $row) {
            $albumIds[] = (int) $row->album_id;
        }
        $itemsByAlbum = $this->load_child_map('album_items', 'album_id', $albumIds, array(
            'album_item_id',
            'album_id',
            'file_id',
            'name',
            'description',
            'status',
        ), 'album_item_id');
        $out = array();
        foreach ($rows as $row) {
            $item = $this->pick_fields($row, $fields);
            $aid = (int) $row->album_id;
            $item['album_items'] = isset($itemsByAlbum[$aid]) ? $itemsByAlbum[$aid] : array();
            $out[] = $item;
        }
        return $out;
    }

    /**
     * @param int[] $ids
     * @return array
     */
    protected function build_videos_export($ids)
    {
        if (empty($ids)) {
            return array();
        }
        $this->ci->db->reset_query();
        $this->ci->db->select('video_id, nam, description, duration, youtube_id, preview, payinfo, date_publish, status');
        $this->ci->db->from('video');
        $this->ci->db->where('status', 1);
        $this->ci->db->where_in('video_id', $ids);
        $this->ci->db->order_by('video_id', 'ASC');
        $query = $this->ci->db->get();
        $out = array();
        if (!$query || $query->num_rows() < 1) {
            return $out;
        }
        foreach ($query->result() as $row) {
            $out[] = array(
                'video_id' => $row->video_id,
                'name' => $row->nam,
                'description' => $row->description,
                'duration' => $row->duration,
                'youtube_id' => $row->youtube_id,
                'preview' => $row->preview,
                'payinfo' => $row->payinfo,
                'date_publish' => $row->date_publish,
                'status' => $row->status,
            );
        }
        return $out;
    }

    /**
     * @param int[] $ids
     * @return array
     */
    protected function build_events_export($ids)
    {
        $fields = array(
            'event_id',
            'name',
            'slug',
            'subtitle',
            'content',
            'address',
            'date_start',
            'date_end',
            'all_day',
            'location_type',
            'online_url',
            'visibility',
            'mainImage',
            'categorie_id',
            'date_publish',
            'status',
        );
        $rows = $this->fetch_by_ids('events', $fields, 'event_id', $ids);
        $out = array();
        foreach ($rows as $row) {
            $out[] = $this->pick_fields($row, $fields);
        }
        return $out;
    }

    /**
     * @param int[] $ids
     * @return array
     */
    protected function build_siteforms_export($ids)
    {
        $fields = array('siteform_id', 'name', 'template', 'properties', 'status');
        $rows = $this->fetch_by_ids('siteform', $fields, 'siteform_id', $ids);
        $formIds = array();
        foreach ($rows as $row) {
            $formIds[] = (int) $row->siteform_id;
        }
        $itemsByForm = $this->load_child_map('siteform_items', 'siteform_id', $formIds, array(
            'siteform_item_id',
            'siteform_id',
            'order',
            'item_type',
            'item_name',
            'item_label',
            'item_class',
            'item_title',
            'item_placeholder',
            'properties',
            'data',
            'date_publish',
            'status',
        ), 'order');
        $out = array();
        foreach ($rows as $row) {
            $item = $this->pick_fields($row, $fields);
            $item['properties'] = $this->decode_json_field($item['properties']);
            $fid = (int) $row->siteform_id;
            $children = isset($itemsByForm[$fid]) ? $itemsByForm[$fid] : array();
            foreach ($children as &$child) {
                if (isset($child['properties'])) {
                    $child['properties'] = $this->decode_json_field($child['properties']);
                }
                if (isset($child['data'])) {
                    $child['data'] = $this->decode_json_field($child['data']);
                }
            }
            unset($child);
            $item['siteform_items'] = $children;
            $out[] = $item;
        }
        return $out;
    }

    /**
     * @param int[] $ids
     * @return array
     */
    protected function build_collections_export($ids)
    {
        $fields = array(
            'custom_model_id',
            'form_name',
            'slug',
            'form_description',
            'template',
            'title_field',
            'status',
        );
        $rows = $this->fetch_by_ids('custom_model', $fields, 'custom_model_id', $ids);
        $out = array();
        foreach ($rows as $row) {
            $item = $this->pick_fields($row, $fields);
            $mid = (int) $row->custom_model_id;
            $item['tabs'] = $this->load_collection_tabs($mid);
            $item['content'] = $this->load_collection_content($mid);
            $out[] = $item;
        }
        return $out;
    }

    /**
     * @param int $customModelId
     * @return array
     */
    protected function load_collection_tabs($customModelId)
    {
        $this->ci->db->reset_query();
        $this->ci->db->from('custom_model_tabs');
        $this->ci->db->where('custom_model_id', $customModelId);
        $this->ci->db->where('status', 1);
        $this->ci->db->order_by('custom_model_tab_id', 'ASC');
        $tabsQuery = $this->ci->db->get();
        $tabs = array();
        $tabIds = array();
        if ($tabsQuery && $tabsQuery->num_rows() > 0) {
            foreach ($tabsQuery->result() as $tab) {
                $tabIds[] = (int) $tab->custom_model_tab_id;
                $tabs[] = array(
                    'custom_model_tab_id' => $tab->custom_model_tab_id,
                    'tab_name' => $tab->tab_name,
                    'status' => $tab->status,
                    'fields' => array(),
                );
            }
        }
        if (empty($tabIds)) {
            return $tabs;
        }
        $this->ci->db->reset_query();
        $this->ci->db->from('custom_model_fields');
        $this->ci->db->where_in('custom_model_tab_id', $tabIds);
        $this->ci->db->where('status', 1);
        $this->ci->db->order_by('custom_model_fields_id', 'ASC');
        $fieldsQuery = $this->ci->db->get();
        $fieldsByTab = array();
        $fieldIds = array();
        $fieldRows = array();
        if ($fieldsQuery && $fieldsQuery->num_rows() > 0) {
            foreach ($fieldsQuery->result() as $field) {
                $fid = (int) $field->custom_model_fields_id;
                $fieldIds[] = $fid;
                $fieldRows[$fid] = $field;
                $tid = (int) $field->custom_model_tab_id;
                if (!isset($fieldsByTab[$tid])) {
                    $fieldsByTab[$tid] = array();
                }
                $fieldsByTab[$tid][] = $fid;
            }
        }
        $dataByField = array();
        if (!empty($fieldIds)) {
            $this->ci->db->reset_query();
            $this->ci->db->from('custom_model_fields_data');
            $this->ci->db->where_in('custom_model_fields_id', $fieldIds);
            $dataQuery = $this->ci->db->get();
            if ($dataQuery && $dataQuery->num_rows() > 0) {
                foreach ($dataQuery->result() as $drow) {
                    $fid = (int) $drow->custom_model_fields_id;
                    if (!isset($dataByField[$fid])) {
                        $dataByField[$fid] = array();
                    }
                    $dataByField[$fid][$drow->_key] = $drow->_value;
                }
            }
        }
        foreach ($tabs as &$tab) {
            $tid = (int) $tab['custom_model_tab_id'];
            $list = array();
            if (isset($fieldsByTab[$tid])) {
                foreach ($fieldsByTab[$tid] as $fid) {
                    $field = $fieldRows[$fid];
                    $list[] = array(
                        'custom_model_fields_id' => $field->custom_model_fields_id,
                        'field_name' => $field->field_name,
                        'displayName' => $field->displayName,
                        'icon' => $field->icon,
                        'component' => $field->component,
                        'status' => $field->status,
                        'data' => isset($dataByField[$fid]) ? $dataByField[$fid] : array(),
                    );
                }
            }
            $tab['fields'] = $list;
        }
        unset($tab);
        return $tabs;
    }

    /**
     * @param int $customModelId
     * @return array
     */
    protected function load_collection_content($customModelId)
    {
        $this->ci->db->reset_query();
        $this->ci->db->from('custom_model_content');
        $this->ci->db->where('custom_model_id', $customModelId);
        $this->ci->db->where('status', 1);
        $this->ci->db->order_by('sort_order', 'ASC');
        $this->ci->db->order_by('custom_model_content_id', 'ASC');
        $query = $this->ci->db->get();
        $items = array();
        $contentIds = array();
        if (!$query || $query->num_rows() < 1) {
            return $items;
        }
        foreach ($query->result() as $row) {
            $cid = (int) $row->custom_model_content_id;
            $contentIds[] = $cid;
            $items[$cid] = array(
                'custom_model_content_id' => $row->custom_model_content_id,
                'title' => $row->title,
                'sort_order' => $row->sort_order,
                'featured' => $row->featured,
                'form_tab_id' => $row->form_tab_id,
                'status' => $row->status,
                'date_publish' => $row->date_publish,
                'data' => array(),
            );
        }
        $this->ci->db->reset_query();
        $this->ci->db->from('custom_model_content_data');
        $this->ci->db->where_in('custom_model_content_id', $contentIds);
        $this->ci->db->where('status', 1);
        $dataQuery = $this->ci->db->get();
        $fieldMeta = array();
        $apiByField = array();
        if ($dataQuery && $dataQuery->num_rows() > 0) {
            $fieldIds = array();
            foreach ($dataQuery->result() as $drow) {
                $fieldIds[(int) $drow->custom_model_fields_id] = (int) $drow->custom_model_fields_id;
            }
            if (!empty($fieldIds)) {
                $this->ci->db->reset_query();
                $this->ci->db->select('custom_model_fields_id, field_name');
                $this->ci->db->from('custom_model_fields');
                $this->ci->db->where_in('custom_model_fields_id', array_values($fieldIds));
                $fq = $this->ci->db->get();
                if ($fq && $fq->num_rows() > 0) {
                    foreach ($fq->result() as $frow) {
                        $fieldMeta[(int) $frow->custom_model_fields_id] = $frow->field_name;
                    }
                }
                $this->ci->db->reset_query();
                $this->ci->db->from('custom_model_fields_data');
                $this->ci->db->where_in('custom_model_fields_id', array_values($fieldIds));
                $this->ci->db->where('_key', 'fielApiID');
                $dq = $this->ci->db->get();
                $apiByField = array();
                if ($dq && $dq->num_rows() > 0) {
                    foreach ($dq->result() as $arow) {
                        $apiByField[(int) $arow->custom_model_fields_id] = $arow->_value;
                    }
                }
            }
            foreach ($dataQuery->result() as $drow) {
                $cid = (int) $drow->custom_model_content_id;
                if (!isset($items[$cid])) {
                    continue;
                }
                $fid = (int) $drow->custom_model_fields_id;
                $value = $this->strip_sensitive($this->decode_json_field($drow->custom_model_content_data_value));
                $items[$cid]['data'][] = array(
                    'custom_model_fields_id' => $fid,
                    'field_name' => isset($fieldMeta[$fid]) ? $fieldMeta[$fid] : '',
                    'fielApiID' => isset($apiByField[$fid]) ? $apiByField[$fid] : (isset($fieldMeta[$fid]) ? $fieldMeta[$fid] : ''),
                    'value' => $value,
                );
            }
        }
        return array_values($items);
    }

    /**
     * @param string $table
     * @param string[] $fields
     * @param string $idCol
     * @param int[] $ids
     * @param int[] $statuses
     * @return array
     */
    protected function fetch_by_ids($table, $fields, $idCol, $ids, $statuses = null)
    {
        if (empty($ids)) {
            return array();
        }
        if ($statuses === null) {
            $statuses = array(1);
        }
        $this->ci->db->reset_query();
        $this->ci->db->select(implode(', ', $fields));
        $this->ci->db->from($table);
        if (count($statuses) === 1) {
            $this->ci->db->where('status', (int) $statuses[0]);
        } else {
            $this->ci->db->where_in('status', $statuses);
        }
        $this->ci->db->where_in($idCol, $ids);
        $this->ci->db->order_by($idCol, 'ASC');
        $query = $this->ci->db->get();
        if (!$query || $query->num_rows() < 1) {
            return array();
        }
        return $query->result();
    }

    /**
     * @param string $table
     * @param string $fk
     * @param int[] $parentIds
     * @param string[] $fields
     * @param string $orderCol
     * @return array
     */
    protected function load_child_map($table, $fk, $parentIds, $fields, $orderCol)
    {
        $map = array();
        if (empty($parentIds)) {
            return $map;
        }
        $this->ci->db->reset_query();
        $select = array();
        foreach ($fields as $field) {
            if ($field === 'order') {
                $select[] = '`order`';
            } else {
                $select[] = $field;
            }
        }
        $this->ci->db->select(implode(', ', $select), false);
        $this->ci->db->from($table);
        $this->ci->db->where_in($fk, $parentIds);
        $this->ci->db->where('status', 1);
        if ($orderCol === 'order') {
            $this->ci->db->order_by('`order`', 'ASC', false);
        } else {
            $this->ci->db->order_by($orderCol, 'ASC');
        }
        $query = $this->ci->db->get();
        if (!$query || $query->num_rows() < 1) {
            return $map;
        }
        foreach ($query->result() as $row) {
            $pid = (int) $row->{$fk};
            if (!isset($map[$pid])) {
                $map[$pid] = array();
            }
            $map[$pid][] = $this->pick_fields($row, $fields);
        }
        return $map;
    }

    /**
     * @param int[] $pageIds
     * @return array
     */
    protected function load_page_data_map($pageIds)
    {
        $map = array();
        if (empty($pageIds)) {
            return $map;
        }
        $this->ci->db->reset_query();
        $this->ci->db->where_in('page_id', $pageIds);
        $query = $this->ci->db->get('page_data');
        if (!$query || $query->num_rows() < 1) {
            return $map;
        }
        foreach ($query->result() as $row) {
            $pid = (int) $row->page_id;
            if (!isset($map[$pid])) {
                $map[$pid] = array();
            }
            $decoded = json_decode($row->_value);
            if (is_object($decoded) || is_array($decoded)) {
                $map[$pid][$row->_key] = $decoded;
            } else {
                $map[$pid][$row->_key] = $row->_value;
            }
        }
        return $map;
    }

    /**
     * @param object $value
     * @return bool
     */
    protected function import_page_row($value)
    {
        $page = $this->without_import_relations(new PageModel());
        $found = false;
        $path = isset($value->path) ? trim((string) $value->path) : '';
        if ($path !== '') {
            $found = $page->find_with(array('path' => $path));
        }
        if (!$found) {
            $page = $this->without_import_relations(new PageModel());
            $page->user_id = userdata('user_id');
            $page->status = 1;
        }

        $fields = array(
            'path',
            'template',
            'title',
            'subtitle',
            'content',
            'page_type_id',
            'visibility',
            'categorie_id',
            'subcategorie_id',
            'status',
            'layout',
            'mainImage',
            'thumbnailImage',
            'date_publish',
        );
        $this->apply_allowlist($page, $value, $fields);
        if (isset($value->json_content)) {
            $page->json_content = $this->encode_json_field($value->json_content);
        }
        if (isset($value->page_data)) {
            $pageData = json_decode(json_encode($value->page_data), true);
            $page->page_data = is_array($pageData) ? $pageData : array();
        }
        return (bool) $page->save();
    }

    /**
     * @param object $value
     * @return bool
     */
    protected function import_config_row($value)
    {
        $config = $this->without_import_relations(new SiteConfigModel());
        $found = false;
        $name = isset($value->config_name) ? trim((string) $value->config_name) : '';
        if ($name !== '') {
            $found = $config->find_with(array('config_name' => $name));
        }
        if (!$found) {
            $config = $this->without_import_relations(new SiteConfigModel());
            $config->user_id = userdata('user_id');
            $config->status = 1;
        }

        $fields = array(
            'config_name',
            'config_value',
            'config_description',
            'config_label',
            'config_type',
            'readonly',
            'status',
        );
        $this->apply_allowlist($config, $value, $fields);
        if (isset($value->config_data)) {
            $config->config_data = $this->encode_json_field($value->config_data);
        }
        return (bool) $config->save();
    }

    /**
     * @param object $value
     * @return bool
     */
    protected function import_fragmento_row($value)
    {
        $name = isset($value->name) ? trim((string) $value->name) : '';
        if ($name === '') {
            return false;
        }
        $data = $this->object_allowlist($value, array('name', 'description', 'type', 'status'));
        $id = $this->upsert_row('fragmentos', 'fragment_id', array('name' => $name), $data, array(
            'user_id' => userdata('user_id'),
            'status' => 1,
        ));
        return $id !== false;
    }

    /**
     * @param object $value
     * @return bool
     */
    protected function import_video_row($value)
    {
        $name = isset($value->name) ? trim((string) $value->name) : '';
        if ($name === '') {
            return false;
        }
        $data = array(
            'nam' => $name,
            'description' => isset($value->description) ? $value->description : '',
            'duration' => isset($value->duration) ? $value->duration : '',
            'youtube_id' => isset($value->youtube_id) ? $value->youtube_id : '',
            'preview' => isset($value->preview) ? $value->preview : '',
            'payinfo' => isset($value->payinfo) ? $value->payinfo : '',
            'date_publish' => isset($value->date_publish) ? $value->date_publish : date('Y-m-d H:i:s'),
            'status' => isset($value->status) ? $value->status : 1,
        );
        $id = $this->upsert_row('video', 'video_id', array('nam' => $name), $data, array('status' => 1));
        return $id !== false;
    }

    /**
     * @param object $value
     * @return bool
     */
    protected function import_event_row($value)
    {
        $slug = isset($value->slug) ? trim((string) $value->slug) : '';
        $name = isset($value->name) ? trim((string) $value->name) : '';
        $dateStart = isset($value->date_start) ? $value->date_start : null;
        if ($slug === '' && $name === '') {
            return false;
        }
        $where = null;
        if ($slug !== '') {
            $where = array('slug' => $slug);
        } elseif ($name !== '' && $dateStart) {
            $where = array('name' => $name, 'date_start' => $dateStart);
        } else {
            $where = array('name' => $name);
        }
        $data = $this->object_allowlist($value, array(
            'name',
            'slug',
            'subtitle',
            'content',
            'address',
            'date_start',
            'date_end',
            'all_day',
            'location_type',
            'online_url',
            'visibility',
            'mainImage',
            'categorie_id',
            'date_publish',
            'status',
        ));
        if ($slug === '') {
            unset($data['slug']);
        }
        $insertExtras = array(
            'user_id' => userdata('user_id'),
            'status' => 1,
        );
        if ($slug === '' && $name !== '') {
            $this->ci->load->model('Admin/EventModel');
            $em = new EventModel();
            $insertExtras['slug'] = $em->ensure_slug($name, null);
        }
        $id = $this->upsert_row('events', 'event_id', $where, $data, $insertExtras);
        return $id !== false;
    }

    /**
     * @param object $value
     * @return bool
     */
    protected function import_category_row($value)
    {
        $name = isset($value->name) ? trim((string) $value->name) : '';
        if ($name === '') {
            return false;
        }
        $type = isset($value->type) ? $value->type : 'page';
        $parentDest = $this->resolve_category_parent($value);
        $data = array(
            'name' => $name,
            'description' => isset($value->description) ? $value->description : '',
            'type' => $type,
            'parent_id' => $parentDest,
            'status' => isset($value->status) ? $value->status : 1,
        );
        $id = $this->upsert_row(
            'categories',
            'categorie_id',
            array('name' => $name, 'type' => $type, 'parent_id' => $parentDest),
            $data,
            array(
                'user_id' => userdata('user_id'),
                'status' => 1,
            )
        );
        return $id !== false;
    }

    /**
     * @param object $value
     * @return int
     */
    protected function resolve_category_parent($value)
    {
        $parentName = isset($value->parent_name) ? trim((string) $value->parent_name) : '';
        $srcParent = isset($value->parent_id) ? (int) $value->parent_id : 0;
        if ($srcParent < 1 && $parentName === '') {
            return 0;
        }
        $type = isset($value->type) ? $value->type : 'page';
        if ($parentName !== '') {
            $this->ci->db->reset_query();
            $this->ci->db->from('categories');
            $this->ci->db->where('name', $parentName);
            $this->ci->db->where('type', $type);
            $this->ci->db->where('status', 1);
            $this->ci->db->order_by('parent_id', 'ASC');
            $this->ci->db->limit(1);
            $q = $this->ci->db->get();
            if ($q && $q->num_rows() > 0) {
                return (int) $q->row()->categorie_id;
            }
        }
        return 0;
    }

    /**
     * @param array $rows
     * @return array
     */
    protected function sort_categories_parents_first($rows)
    {
        $roots = array();
        $children = array();
        foreach ($rows as $row) {
            $pid = (is_object($row) && isset($row->parent_id)) ? (int) $row->parent_id : 0;
            if ($pid < 1) {
                $roots[] = $row;
            } else {
                $children[] = $row;
            }
        }
        return array_merge($roots, $children);
    }

    /**
     * @param object $value
     * @return bool
     */
    protected function import_menu_row($value)
    {
        $name = isset($value->name) ? trim((string) $value->name) : '';
        if ($name === '') {
            return false;
        }
        $data = $this->object_allowlist($value, array('name', 'template', 'position', 'status'));
        $menuId = $this->upsert_row('menu', 'menu_id', array('name' => $name), $data, array(
            'user_id' => userdata('user_id'),
            'status' => 1,
        ));
        if ($menuId === false) {
            return false;
        }
        $items = array();
        if (isset($value->menu_items) && is_array($value->menu_items)) {
            $items = $value->menu_items;
        }
        return $this->import_menu_items((int) $menuId, $items);
    }

    /**
     * @param int $menuId
     * @param array $items
     * @return bool
     */
    protected function import_menu_items($menuId, $items)
    {
        $idMap = array();
        $ordered = $this->sort_menu_items_parents_first($items);
        foreach ($ordered as $raw) {
            $item = is_object($raw) ? $raw : (object) $raw;
            $srcId = isset($item->menu_item_id) ? (int) $item->menu_item_id : 0;
            $srcParent = isset($item->menu_item_parent_id) ? (int) $item->menu_item_parent_id : 0;
            $destParent = 0;
            if ($srcParent > 0 && isset($idMap[$srcParent])) {
                $destParent = $idMap[$srcParent];
            }
            $itemName = isset($item->item_name) ? trim((string) $item->item_name) : '';
            $data = array(
                'menu_id' => $menuId,
                'menu_item_parent_id' => $destParent,
                'order' => isset($item->order) ? (int) $item->order : 0,
                'model' => isset($item->model) ? $item->model : '',
                'item_type' => isset($item->item_type) ? $item->item_type : '',
                'item_name' => $itemName,
                'item_label' => isset($item->item_label) ? $item->item_label : '',
                'item_link' => isset($item->item_link) ? $item->item_link : '',
                'item_title' => isset($item->item_title) ? $item->item_title : '',
                'item_target' => isset($item->item_target) ? $item->item_target : '_self',
                'date_publish' => !empty($item->date_publish) ? $item->date_publish : date('Y-m-d H:i:s'),
                'status' => isset($item->status) ? $item->status : 1,
                'model_id' => $this->remap_menu_item_page_id($item),
            );
            $where = array('menu_id' => $menuId);
            if ($itemName !== '') {
                $where['item_name'] = $itemName;
            } else {
                $where['item_label'] = isset($item->item_label) ? $item->item_label : '';
                $where['item_link'] = isset($item->item_link) ? $item->item_link : '';
            }
            $newId = $this->upsert_row('menu_items', 'menu_item_id', $where, $data, array('status' => 1));
            if ($newId === false) {
                return false;
            }
            if ($srcId > 0) {
                $idMap[$srcId] = (int) $newId;
            }
        }
        return true;
    }

    /**
     * @param object $item
     * @return int
     */
    protected function remap_menu_item_page_id($item)
    {
        $model = isset($item->model) ? $item->model : '';
        if ($model !== 'page') {
            return 0;
        }
        $link = isset($item->item_link) ? trim((string) $item->item_link) : '';
        if ($link === '') {
            return 0;
        }
        $path = parse_url($link, PHP_URL_PATH);
        if (!is_string($path) || $path === '') {
            $path = $link;
        }
        $path = '/' . ltrim($path, '/');
        $alt = ltrim($path, '/');
        $this->ci->db->reset_query();
        $this->ci->db->from('page');
        $this->ci->db->where('status', 1);
        $this->ci->db->group_start();
        $this->ci->db->where('path', $path);
        $this->ci->db->or_where('path', $alt);
        $this->ci->db->group_end();
        $this->ci->db->limit(1);
        $q = $this->ci->db->get();
        if ($q && $q->num_rows() > 0) {
            return (int) $q->row()->page_id;
        }
        return 0;
    }

    /**
     * @param array $items
     * @return array
     */
    protected function sort_menu_items_parents_first($items)
    {
        $placed = array();
        $ordered = array();
        $remaining = $items;
        $guard = 0;
        while (!empty($remaining) && $guard < 500) {
            $guard++;
            $next = array();
            $progress = false;
            foreach ($remaining as $raw) {
                $item = is_object($raw) ? $raw : (object) $raw;
                $pid = isset($item->menu_item_parent_id) ? (int) $item->menu_item_parent_id : 0;
                $sid = isset($item->menu_item_id) ? (int) $item->menu_item_id : 0;
                if ($pid < 1 || isset($placed[$pid])) {
                    $ordered[] = $item;
                    if ($sid > 0) {
                        $placed[$sid] = true;
                    }
                    $progress = true;
                } else {
                    $next[] = $item;
                }
            }
            if (!$progress) {
                foreach ($next as $item) {
                    if (is_object($item)) {
                        $item->menu_item_parent_id = 0;
                    }
                    $ordered[] = $item;
                }
                break;
            }
            $remaining = $next;
        }
        return $ordered;
    }

    /**
     * @param object $value
     * @return bool
     */
    protected function import_album_row($value)
    {
        $name = isset($value->name) ? trim((string) $value->name) : '';
        if ($name === '') {
            return false;
        }
        $data = $this->object_allowlist($value, array('name', 'description', 'date_publish', 'status'));
        $albumId = $this->upsert_row('album', 'album_id', array('name' => $name), $data, array(
            'user_id' => userdata('user_id'),
            'status' => 1,
        ));
        if ($albumId === false) {
            return false;
        }
        $items = (isset($value->album_items) && is_array($value->album_items)) ? $value->album_items : array();
        foreach ($items as $raw) {
            $item = is_object($raw) ? $raw : (object) $raw;
            $itemName = isset($item->name) ? trim((string) $item->name) : '';
            $row = array(
                'album_id' => (int) $albumId,
                'file_id' => isset($item->file_id) ? (int) $item->file_id : 0,
                'name' => $itemName,
                'description' => isset($item->description) ? $item->description : '',
                'status' => isset($item->status) ? $item->status : 1,
            );
            $where = array('album_id' => (int) $albumId);
            if ($itemName !== '') {
                $where['name'] = $itemName;
            } else {
                $where['file_id'] = $row['file_id'];
            }
            if ($this->upsert_row('album_items', 'album_item_id', $where, $row, array('status' => 1)) === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param object $value
     * @return bool
     */
    protected function import_siteform_row($value)
    {
        $name = isset($value->name) ? trim((string) $value->name) : '';
        if ($name === '') {
            return false;
        }
        $data = array(
            'name' => $name,
            'template' => isset($value->template) ? $value->template : 'form',
            'properties' => $this->encode_json_field(isset($value->properties) ? $value->properties : array()),
            'status' => isset($value->status) ? $value->status : 1,
        );
        $formId = $this->upsert_row('siteform', 'siteform_id', array('name' => $name), $data, array(
            'user_id' => userdata('user_id'),
            'status' => 1,
        ));
        if ($formId === false) {
            return false;
        }
        $items = (isset($value->siteform_items) && is_array($value->siteform_items)) ? $value->siteform_items : array();
        foreach ($items as $raw) {
            $item = is_object($raw) ? $raw : (object) $raw;
            $itemName = isset($item->item_name) ? trim((string) $item->item_name) : '';
            $row = array(
                'siteform_id' => (int) $formId,
                'order' => isset($item->order) ? (int) $item->order : 0,
                'item_type' => isset($item->item_type) ? $item->item_type : 'text',
                'item_name' => $itemName,
                'item_label' => isset($item->item_label) ? $item->item_label : '',
                'item_class' => isset($item->item_class) ? $item->item_class : '',
                'item_title' => isset($item->item_title) ? $item->item_title : '',
                'item_placeholder' => isset($item->item_placeholder) ? $item->item_placeholder : '',
                'properties' => $this->encode_json_field(isset($item->properties) ? $item->properties : array()),
                'data' => $this->encode_json_field(isset($item->data) ? $item->data : array()),
                'date_publish' => !empty($item->date_publish) && $item->date_publish !== '0000-00-00 00:00:00'
                    ? $item->date_publish
                    : date('Y-m-d H:i:s'),
                'status' => isset($item->status) ? $item->status : 1,
            );
            $where = array('siteform_id' => (int) $formId);
            if ($itemName !== '') {
                $where['item_name'] = $itemName;
            } else {
                $where['item_label'] = $row['item_label'];
            }
            if ($this->upsert_row('siteform_items', 'siteform_item_id', $where, $row, array('status' => 1)) === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param object $value
     * @return bool
     */
    protected function import_collection_row($value)
    {
        $slug = isset($value->slug) ? trim((string) $value->slug) : '';
        $formName = isset($value->form_name) ? trim((string) $value->form_name) : '';
        if ($slug === '' && $formName !== '') {
            $this->ci->load->model('Admin/CustomModelModel');
            $cm = new CustomModelModel();
            $slug = $cm->slug_from_name($formName);
        }
        if ($slug === '') {
            return false;
        }
        $data = array(
            'form_name' => $formName !== '' ? $formName : $slug,
            'slug' => $slug,
            'form_description' => isset($value->form_description) ? $value->form_description : '',
            'template' => isset($value->template) && $value->template !== '' ? $value->template : 'default',
            'title_field' => isset($value->title_field) ? $value->title_field : null,
            'status' => isset($value->status) ? $value->status : 1,
        );
        $modelId = $this->upsert_row('custom_model', 'custom_model_id', array('slug' => $slug), $data, array(
            'user_id' => userdata('user_id'),
            'status' => 1,
        ));
        if ($modelId === false) {
            return false;
        }
        $fieldMap = array();
        $apiMap = array();
        $tabs = (isset($value->tabs) && is_array($value->tabs)) ? $value->tabs : array();
        foreach ($tabs as $rawTab) {
            $tab = is_object($rawTab) ? $rawTab : (object) $rawTab;
            $tabName = isset($tab->tab_name) ? $tab->tab_name : 'Tab 1';
            $tabId = $this->upsert_row(
                'custom_model_tabs',
                'custom_model_tab_id',
                array('custom_model_id' => (int) $modelId, 'tab_name' => $tabName),
                array(
                    'custom_model_id' => (int) $modelId,
                    'tab_name' => $tabName,
                    'status' => isset($tab->status) ? $tab->status : 1,
                ),
                array('status' => 1)
            );
            if ($tabId === false) {
                return false;
            }
            $fields = array();
            if (isset($tab->fields) && is_array($tab->fields)) {
                $fields = $tab->fields;
            } elseif (isset($tab->custom_model_fields) && is_array($tab->custom_model_fields)) {
                $fields = $tab->custom_model_fields;
            }
            foreach ($fields as $rawField) {
                $field = is_object($rawField) ? $rawField : (object) $rawField;
                $srcFid = isset($field->custom_model_fields_id) ? (int) $field->custom_model_fields_id : 0;
                $fieldData = array();
                if (isset($field->data)) {
                    $fieldData = json_decode(json_encode($field->data), true);
                    if (!is_array($fieldData)) {
                        $fieldData = array();
                    }
                }
                $apiId = '';
                if (isset($field->fielApiID)) {
                    $apiId = (string) $field->fielApiID;
                } elseif (isset($fieldData['fielApiID'])) {
                    $apiId = (string) $fieldData['fielApiID'];
                } elseif (isset($field->field_name)) {
                    $apiId = (string) $field->field_name;
                }
                $fieldName = isset($field->field_name) ? $field->field_name : $apiId;
                $destFid = $this->find_collection_field((int) $tabId, $fieldName, $apiId);
                $fieldRow = array(
                    'custom_model_tab_id' => (int) $tabId,
                    'field_name' => $fieldName,
                    'displayName' => isset($field->displayName) ? $field->displayName : $fieldName,
                    'icon' => isset($field->icon) ? $field->icon : '',
                    'component' => isset($field->component) ? $field->component : '',
                    'status' => isset($field->status) ? $field->status : 1,
                );
                if ($destFid) {
                    $this->ci->db->where('custom_model_fields_id', $destFid);
                    if ($this->ci->db->update('custom_model_fields', $fieldRow) === false) {
                        return false;
                    }
                } else {
                    if ($this->ci->db->insert('custom_model_fields', $fieldRow) === false) {
                        return false;
                    }
                    $destFid = (int) $this->ci->db->insert_id();
                }
                if ($srcFid > 0) {
                    $fieldMap[$srcFid] = $destFid;
                }
                if ($apiId !== '') {
                    $apiMap[$apiId] = $destFid;
                }
                foreach ($fieldData as $dkey => $dval) {
                    if (is_array($dval) || is_object($dval)) {
                        $dval = json_encode($dval);
                    }
                    $existing = $this->find_row('custom_model_fields_data', array(
                        'custom_model_fields_id' => $destFid,
                        '_key' => $dkey,
                    ));
                    if ($existing) {
                        $this->ci->db->where('custom_model_fields_data_id', $existing->custom_model_fields_data_id);
                        $this->ci->db->update('custom_model_fields_data', array('_value' => $dval));
                    } else {
                        $this->ci->db->insert('custom_model_fields_data', array(
                            'custom_model_fields_id' => $destFid,
                            '_key' => $dkey,
                            '_value' => $dval,
                            'status' => 1,
                        ));
                    }
                }
            }
        }

        $contents = (isset($value->content) && is_array($value->content)) ? $value->content : array();
        foreach ($contents as $rawContent) {
            $content = is_object($rawContent) ? $rawContent : (object) $rawContent;
            if (!$this->import_collection_content_row((int) $modelId, $content, $fieldMap, $apiMap)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param int $tabId
     * @param string $fieldName
     * @param string $apiId
     * @return int
     */
    protected function find_collection_field($tabId, $fieldName, $apiId)
    {
        if ($apiId !== '') {
            $sql = "SELECT f.custom_model_fields_id
                FROM custom_model_fields f
                INNER JOIN custom_model_fields_data d ON d.custom_model_fields_id = f.custom_model_fields_id
                WHERE f.custom_model_tab_id = ?
                AND d._key = 'fielApiID'
                AND d._value = ?
                LIMIT 1";
            $q2 = $this->ci->db->query($sql, array($tabId, $apiId));
            if ($q2 && $q2->num_rows() > 0) {
                return (int) $q2->row()->custom_model_fields_id;
            }
        }
        if ($fieldName === '') {
            return 0;
        }
        $this->ci->db->reset_query();
        $this->ci->db->from('custom_model_fields');
        $this->ci->db->where('custom_model_tab_id', $tabId);
        $this->ci->db->where('field_name', $fieldName);
        $this->ci->db->limit(1);
        $q = $this->ci->db->get();
        if ($q && $q->num_rows() > 0) {
            return (int) $q->row()->custom_model_fields_id;
        }
        return 0;
    }

    /**
     * @param int $modelId
     * @param object $content
     * @param array $fieldMap
     * @param array $apiMap
     * @return bool
     */
    protected function import_collection_content_row($modelId, $content, $fieldMap, $apiMap)
    {
        $title = isset($content->title) ? trim((string) $content->title) : '';
        if ($title === '' && isset($content->data) && is_array($content->data)) {
            $title = $this->title_from_collection_data($content->data);
        }
        $row = array(
            'custom_model_id' => $modelId,
            'title' => $title !== '' ? $title : null,
            'sort_order' => isset($content->sort_order) ? (int) $content->sort_order : 0,
            'featured' => isset($content->featured) ? (int) $content->featured : 0,
            'form_tab_id' => 0,
            'status' => isset($content->status) ? $content->status : 1,
            'date_publish' => isset($content->date_publish) ? $content->date_publish : date('Y-m-d H:i:s'),
        );
        $where = array('custom_model_id' => $modelId);
        if ($title !== '') {
            $where['title'] = $title;
        } else {
            $where['sort_order'] = $row['sort_order'];
        }
        $contentId = $this->upsert_row('custom_model_content', 'custom_model_content_id', $where, $row, array(
            'user_id' => userdata('user_id'),
            'status' => 1,
        ));
        if ($contentId === false) {
            return false;
        }
        $dataRows = (isset($content->data) && is_array($content->data)) ? $content->data : array();
        foreach ($dataRows as $rawData) {
            $drow = is_object($rawData) ? $rawData : (object) $rawData;
            $destFid = 0;
            $srcFid = isset($drow->custom_model_fields_id) ? (int) $drow->custom_model_fields_id : 0;
            if ($srcFid > 0 && isset($fieldMap[$srcFid])) {
                $destFid = $fieldMap[$srcFid];
            }
            $apiId = isset($drow->fielApiID) ? (string) $drow->fielApiID : '';
            if ($destFid < 1 && $apiId !== '' && isset($apiMap[$apiId])) {
                $destFid = $apiMap[$apiId];
            }
            if ($destFid < 1 && isset($drow->field_name) && isset($apiMap[$drow->field_name])) {
                $destFid = $apiMap[$drow->field_name];
            }
            if ($destFid < 1) {
                continue;
            }
            $encoded = $this->encode_json_field(isset($drow->value) ? $drow->value : (isset($drow->custom_model_content_data_value) ? $drow->custom_model_content_data_value : array()));
            $existing = $this->find_row('custom_model_content_data', array(
                'custom_model_content_id' => (int) $contentId,
                'custom_model_fields_id' => $destFid,
            ));
            if ($existing) {
                $this->ci->db->where('custom_model_content_data_id', $existing->custom_model_content_data_id);
                if ($this->ci->db->update('custom_model_content_data', array(
                    'custom_model_content_data_value' => $encoded,
                    'status' => 1,
                )) === false) {
                    return false;
                }
            } else {
                if ($this->ci->db->insert('custom_model_content_data', array(
                    'custom_model_content_id' => (int) $contentId,
                    'custom_model_fields_id' => $destFid,
                    'custom_model_content_data_value' => $encoded,
                    'status' => 1,
                )) === false) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @param array $dataRows
     * @return string
     */
    protected function title_from_collection_data($dataRows)
    {
        foreach ($dataRows as $raw) {
            $row = is_object($raw) ? $raw : (object) $raw;
            $api = isset($row->fielApiID) ? $row->fielApiID : (isset($row->field_name) ? $row->field_name : '');
            if ($api !== 'title') {
                continue;
            }
            $val = isset($row->value) ? $row->value : null;
            if (is_object($val) && isset($val->title)) {
                return trim((string) $val->title);
            }
            if (is_array($val) && isset($val['title'])) {
                return trim((string) $val['title']);
            }
            if (is_string($val) && $val !== '') {
                return trim($val);
            }
        }
        return '';
    }

    /**
     * @param object $source
     * @param string[] $fields
     * @return array
     */
    protected function object_allowlist($source, $fields)
    {
        $out = array();
        foreach ($fields as $field) {
            if (is_object($source) && property_exists($source, $field)) {
                $out[$field] = $source->{$field};
            }
        }
        return $out;
    }

    /**
     * @param string $table
     * @param array $where
     * @return object|null
     */
    protected function find_row($table, $where)
    {
        $this->ci->db->reset_query();
        $query = $this->ci->db->get_where($table, $where, 1);
        if ($query && $query->num_rows() > 0) {
            return $query->row();
        }
        return null;
    }

    /**
     * @param string $table
     * @param string $pk
     * @param array $where
     * @param array $data
     * @param array $insertExtras
     * @return int|false
     */
    protected function upsert_row($table, $pk, $where, $data, $insertExtras = array())
    {
        $existing = $this->find_row($table, $where);
        if ($existing) {
            $this->ci->db->reset_query();
            $this->ci->db->where($pk, $existing->{$pk});
            if ($this->ci->db->update($table, $data) === false) {
                return false;
            }
            return (int) $existing->{$pk};
        }
        $insert = array_merge($insertExtras, $data);
        $this->ci->db->reset_query();
        if ($this->ci->db->insert($table, $insert) === false) {
            return false;
        }
        return (int) $this->ci->db->insert_id();
    }
}
