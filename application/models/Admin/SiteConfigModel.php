<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class SiteConfigModel extends MY_Model
{

    public $table = 'site_config';
    public $primaryKey = 'site_config_id';
    public $softDelete = true;

    public $hasOne = [
        'user' => ['user_id', 'Admin/UserModel', 'UserModel'],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->loadCatalogClass();
    }

    /**
     * Flat name => value map. No user hydration (all() N+1).
     *
     * @return array
     */
    public function get_map()
    {
        $this->db->reset_query();
        $this->db->select('config_name, config_value');
        $this->db->from($this->table);
        $this->db->where('status', 1);
        $query = $this->db->get();
        $map = array();
        if ($query && $query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                if (isset($row->config_name) && $row->config_name !== '') {
                    $map[$row->config_name] = $row->config_value;
                }
            }
        }
        return $map;
    }

    /**
     * Admin list without hydrating users (the UI does not show authors).
     *
     * @return array
     */
    public function list_for_admin()
    {
        $this->db->reset_query();
        $this->db->from($this->table);
        $this->db->where('status', 1);
        $this->db->order_by($this->primaryKey, 'ASC');
        $query = $this->db->get();
        if (!$query || $query->num_rows() === 0) {
            return array();
        }
        return $query->result();
    }

    /**
     * Fill labels/descriptions/types from the catalog. Inserts missing keys.
     * Does not overwrite config_value or a custom label that already differs
     * from the machine name.
     *
     * @return int number of rows inserted or updated
     */
    public function sync_catalog()
    {
        $this->loadCatalogClass();
        $definitions = Site_config_catalog::definitions();
        $changed = 0;

        $existing = array();
        $query = $this->db->get($this->table);
        if ($query && $query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $existing[$row->config_name] = $row;
            }
        }

        foreach ($definitions as $name => $meta) {
            $label = isset($meta['config_label']) ? $meta['config_label'] : $name;
            $description = isset($meta['config_description']) ? $meta['config_description'] : '';
            $type = isset($meta['config_type']) ? $meta['config_type'] : 'general';
            $data = isset($meta['config_data']) ? $meta['config_data'] : '{}';
            $readonly = isset($meta['readonly']) ? (int) $meta['readonly'] : 0;

            if (!isset($existing[$name])) {
                $this->db->insert($this->table, array(
                    'user_id' => 1,
                    'config_name' => $name,
                    'config_value' => isset($meta['default_value']) ? $meta['default_value'] : '',
                    'config_description' => $description,
                    'config_label' => $label,
                    'config_type' => $type,
                    'config_data' => $data,
                    'readonly' => $readonly,
                    'status' => 1,
                    'date_create' => date('Y-m-d H:i:s'),
                    'date_update' => date('Y-m-d H:i:s'),
                ));
                $changed++;
                continue;
            }

            $row = $existing[$name];
            $update = array();
            $currentLabel = isset($row->config_label) ? trim((string) $row->config_label) : '';
            if ($currentLabel === '' || $currentLabel === $name) {
                $update['config_label'] = $label;
            }
            $currentDesc = isset($row->config_description) ? trim((string) $row->config_description) : '';
            if ($currentDesc === '' || $currentDesc === $name) {
                $update['config_description'] = $description;
            }
            $currentType = isset($row->config_type) ? trim((string) $row->config_type) : '';
            if ($currentType !== $type) {
                $update['config_type'] = $type;
            }
            $currentData = isset($row->config_data) ? trim((string) $row->config_data) : '';
            if ($currentData === '' || $currentData === 'null' || $currentData === '{}' || $currentData === '[]') {
                $update['config_data'] = $data;
            }
            if ($readonly && (int) $row->readonly !== 1) {
                $update['readonly'] = 1;
            }

            if (!empty($update)) {
                $this->db->where('site_config_id', $row->site_config_id);
                $this->db->update($this->table, $update);
                $changed++;
            }
        }

        if ($changed > 0) {
            if (function_exists('invalidate_site_config_cache')) {
                invalidate_site_config_cache();
            }
        }

        return $changed;
    }

    /**
     * Update all config data
     */
    public function update_config($data)
    {
        foreach ($data as $key => $config) {
            $this->update_data(
                array('config_name' => $config['config_name']),
                array('config_value' => $config['config_value']),
                $this->table
            );
        }
    }

    /**
     * Skip per-row user hydration: the settings UI does not show authors,
     * and all() would N+1 UserModel for every key.
     */
    public function filter_results($collection = [])
    {
        return $collection;
    }

    protected function loadCatalogClass()
    {
        if (!class_exists('Site_config_catalog', false)) {
            require_once APPPATH . 'libraries/Site_config_catalog.php';
        }
    }

}
