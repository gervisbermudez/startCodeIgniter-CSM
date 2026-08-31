<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class CustomModelModel extends MY_Model
{
    public $table = 'custom_model';
    public $primaryKey = 'custom_model_id';
    public $softDelete = true;
    public $last_error = '';

    public $hasOne = [
        'user' => ['user_id', 'Admin/UserModel', 'UserModel'],
    ];

    public $hasMany = [
        'tabs' => ['custom_model_id', 'Admin/CustomModelTabsModel', 'CustomModelTabsModel'],
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all()
    {
        $sql = "SELECT cm.*, u.username
        FROM custom_model cm
        INNER JOIN user u ON u.user_id = cm.user_id
        where cm.status = 1
        ";
        return $this->get_query($sql);
    }

    public function get_form_types()
    {
        $sql = "SELECT DISTINCT(cm.form_name),
        cm.custom_model_id, cm.date_create, cm.date_update,
        cm.user_id, cm.`status`, u.username
        FROM custom_model cm
        INNER JOIN user u ON u.user_id = cm.user_id";
        return $this->get_query($sql);

    }

    public function get_form_data($where = '')
    {
        $sql = "SELECT fc.*, u.username,
        form_id, CONCAT('[', GROUP_CONCAT(form_data), ']') AS 'model_data'
            FROM
            (
            SELECT form_id, JSON_OBJECT(form_key, custom_model_content_data_value) AS 'model_data'
            FROM form_custom_data) sq1
            INNER JOIN custom_model fc ON fc.custom_model_id = sq1.form_id
            INNER JOIN user u ON u.user_id = fc.user_id
            $where
            GROUP BY form_id
            ";

        $result = $this->get_query($sql);
        if ($result) {
            foreach ($result as $key => &$value) {
                $temArray = array();
                $value['model_data'] = json_decode($value['model_data']);
                foreach ($value['model_data'] as $val) {
                    foreach ($val as $index => $valor) {
                        $temArray[$index] = $valor;
                    }
                }
                $value['model_data'] = $temArray;
            }
            return $result;
        }
        return false;
    }

    public function get_forms($where = '')
    {
        $sql = <<<EOD
        SELECT *
        FROM (
        SELECT custom_model_tab_id, CONCAT('[', GROUP_CONCAT(fields_data), ']') AS fields_data
        FROM (
        SELECT ff.*, JSON_OBJECT('field_name', field_name, 'displayName', displayName, 'icon', icon, 'component', component, 'dataconfigs', dataconfigs) AS 'fields_data'
        FROM (
        SELECT custom_model_fields_id, CONCAT('{', GROUP_CONCAT(dataconfigs), '}') AS dataconfigs
        FROM (
        SELECT custom_model_fields_id, CONCAT('"', _key, '":"', _value, '"') AS dataconfigs
        FROM custom_model_fields_data) sq1
        GROUP BY custom_model_fields_id) sq2
        INNER JOIN custom_model_fields ff ON sq2.custom_model_fields_id = ff.custom_model_fields_id) sq3
        GROUP BY custom_model_tab_id) sq4
        INNER JOIN custom_model_tabs t ON t.custom_model_tab_id = sq4.custom_model_tab_id
        INNER JOIN custom_model fc ON t.custom_model_id = fc.custom_model_id
        $where
EOD;
        $result = $this->get_query($sql);
        foreach ($result as $key => &$value) {
            $value->fields_data = str_replace('\"', '"', $value->fields_data);
            $value->fields_data = str_replace('"{', '{', $value->fields_data);
            $value->fields_data = str_replace('}"', '}', $value->fields_data);
            $value->fields_data = json_decode($value->fields_data);
        }

        return $this->filter_results($result);
    }

    public function slug_from_name($name)
    {
        $slug = strtolower(trim((string) $name));
        $slug = str_replace(array(' ', '-'), '_', $slug);
        $slug = preg_replace('/[^a-z0-9_]/', '', $slug);
        if ($slug === '') {
            $slug = 'collection';
        }
        return $slug;
    }

    public function slug_exists($slug, $exclude_id = null)
    {
        $this->db->from($this->table);
        $this->db->where('slug', $slug);
        $this->db->where('status !=', 0);
        if ($exclude_id) {
            $this->db->where('custom_model_id !=', (int) $exclude_id);
        }
        return $this->db->count_all_results() > 0;
    }

    /**
     * Enabled = 1, disabled = 3. Never persist 0 from the schema switch.
     */
    public function normalize_type_status($status)
    {
        $status = (int) $status;
        if ($status === 3) {
            return 3;
        }
        if ($status === 0) {
            return 3;
        }
        return 1;
    }

    public function collection_snippet($slug)
    {
        return "{!! get_collection('" . $slug . "') !!}";
    }

    /**
     * @param object $data Form data to be saved
     * @return int|false
     */
    public function save_form($data)
    {
        $slug = !empty($data->slug) ? $data->slug : $this->slug_from_name($data->form_name);
        $insert = array(
            'form_name' => $data->form_name,
            'slug' => $slug,
            'form_description' => isset($data->form_description) ? $data->form_description : '',
            'template' => !empty($data->template) ? $data->template : 'default',
            'title_field' => !empty($data->title_field) ? $data->title_field : null,
            'user_id' => userdata('user_id'),
            'status' => $this->normalize_type_status(isset($data->status) ? $data->status : 1),
        );

        $result = $this->set_data($insert, $this->table);
        if ($result) {
            $custom_model_id = $this->db->insert_id();
            foreach ($data->tabs as $tab) {
                $this->insert_tab($custom_model_id, $tab);
            }
            return $custom_model_id;
        }

        return false;
    }

    public function get_form_content_data($where = '')
    {
        $sql = "SELECT cm.form_name, fc.*, u.username, form_data AS 'form_data'
                FROM (
                SELECT custom_model_content_id, CONCAT('[',GROUP_CONCAT(JSON_OBJECT(form_key, custom_model_content_data_value)), ']') AS 'form_data'
                FROM form_content_data
                WHERE form_content_data.status = 1
                GROUP BY custom_model_content_id
                ) sq1
                INNER JOIN form_content fc ON fc.custom_model_content_id = sq1.custom_model_content_id
                INNER JOIN custom_model cm ON fc.custom_model_id = cm.id
                INNER JOIN user u ON u.user_id = fc.user_id
                $where
                ";
        return $this->get_query($sql);

    }

    /**
     * Non-destructive schema update: UPDATE existing tabs/fields, INSERT new, refuse delete when content data exists.
     *
     * @param object $data
     * @return int|false
     */
    public function update_form($data)
    {
        $this->last_error = '';
        $custom_model_id = (int) $data->custom_model_id;
        $slug = !empty($data->slug) ? $data->slug : $this->slug_from_name($data->form_name);

        $update = array(
            'form_name' => $data->form_name,
            'slug' => $slug,
            'form_description' => isset($data->form_description) ? $data->form_description : '',
            'template' => !empty($data->template) ? $data->template : 'default',
            'title_field' => !empty($data->title_field) ? $data->title_field : null,
            'user_id' => userdata('user_id'),
            'status' => $this->normalize_type_status(isset($data->status) ? $data->status : 1),
        );

        $this->db->where(array('custom_model_id' => $custom_model_id));
        $result = $this->db->update('custom_model', $update);
        if (!$result) {
            return false;
        }

        $existing_tabs = $this->db->get_where('custom_model_tabs', array('custom_model_id' => $custom_model_id))->result();
        $keep_tab_ids = array();

        foreach ($data->tabs as $tab) {
            $tab = is_object($tab) ? $tab : (object) $tab;
            $tab_id = !empty($tab->custom_model_tab_id) ? (int) $tab->custom_model_tab_id : 0;
            if ($tab_id) {
                $this->db->where('custom_model_tab_id', $tab_id);
                $this->db->update('custom_model_tabs', array('tab_name' => $tab->tab_name));
                $keep_tab_ids[] = $tab_id;
                if (!$this->sync_tab_fields($tab_id, isset($tab->custom_model_fields) ? $tab->custom_model_fields : array())) {
                    return false;
                }
            } else {
                $new_id = $this->insert_tab($custom_model_id, $tab);
                if ($new_id) {
                    $keep_tab_ids[] = $new_id;
                }
            }
        }

        foreach ($existing_tabs as $old_tab) {
            $old_id = (int) $old_tab->custom_model_tab_id;
            if (in_array($old_id, $keep_tab_ids, true)) {
                continue;
            }
            if ($this->tab_has_content_data($old_id)) {
                $this->last_error = 'tab_has_data';
                return false;
            }
            $this->delete_tab_cascade($old_id);
        }

        return $custom_model_id;
    }

    public function delete_form($custom_model_id)
    {
        return $this->delete_data(array('custom_model_id' => $custom_model_id), $this->table);
    }

    public function retrieved()
    {
        parent::retrieved();
        $this->decorate_type($this, true);
    }

    public function filter_results($collection = [])
    {
        $this->load->model('Admin/UserModel');
        $ids = array();
        foreach ($collection as $key => &$value) {
            if (isset($value->user_id)) {
                $user = new UserModel();
                $user->find($value->user_id);
                $value->{'user'} = $user->as_data();
                $value->{'model_type'} = "custom_model";
            }
            $this->decorate_type($value, false);
            if (isset($value->custom_model_id)) {
                $ids[] = (int) $value->custom_model_id;
            }
        }

        if (!empty($ids)) {
            $counts = $this->count_items_by_model($ids);
            $field_counts = $this->count_fields_by_model($ids);
            foreach ($collection as $key => &$value) {
                $id = isset($value->custom_model_id) ? (int) $value->custom_model_id : 0;
                $value->items_count = isset($counts[$id]) ? $counts[$id] : 0;
                $value->fields_count = isset($field_counts[$id]) ? $field_counts[$id] : 0;
            }
        }

        return $collection;
    }

    public function decorate_type($value, $with_counts = false)
    {
        if (!is_object($value)) {
            return $value;
        }
        if (empty($value->slug) && !empty($value->form_name)) {
            $value->slug = $this->slug_from_name($value->form_name);
        }
        if (empty($value->template)) {
            $value->template = 'default';
        }
        if (!isset($value->title_field)) {
            $value->title_field = null;
        }
        if (!empty($value->slug)) {
            $value->snippet = $this->collection_snippet($value->slug);
        }
        if ($with_counts && isset($value->custom_model_id)) {
            $id = (int) $value->custom_model_id;
            $counts = $this->count_items_by_model(array($id));
            $value->items_count = isset($counts[$id]) ? $counts[$id] : 0;
            $field_counts = $this->count_fields_by_model(array($id));
            $value->fields_count = isset($field_counts[$id]) ? $field_counts[$id] : 0;
        }
        return $value;
    }

    public function count_items_by_model($ids)
    {
        $out = array();
        if (empty($ids)) {
            return $out;
        }
        $this->db->select('custom_model_id, COUNT(*) AS items_count', false);
        $this->db->from('custom_model_content');
        $this->db->where_in('custom_model_id', $ids);
        $this->db->where('status !=', 0);
        $this->db->group_by('custom_model_id');
        $rows = $this->db->get()->result();
        foreach ($rows as $row) {
            $out[(int) $row->custom_model_id] = (int) $row->items_count;
        }
        return $out;
    }

    public function count_fields_by_model($ids)
    {
        $out = array();
        if (empty($ids)) {
            return $out;
        }
        $sql = "SELECT t.custom_model_id, COUNT(f.custom_model_fields_id) AS fields_count
            FROM custom_model_tabs t
            LEFT JOIN custom_model_fields f ON f.custom_model_tab_id = t.custom_model_tab_id
            WHERE t.custom_model_id IN (" . implode(',', array_map('intval', $ids)) . ")
            GROUP BY t.custom_model_id";
        $rows = $this->db->query($sql)->result();
        foreach ($rows as $row) {
            $out[(int) $row->custom_model_id] = (int) $row->fields_count;
        }
        return $out;
    }

    protected function insert_tab($custom_model_id, $tab)
    {
        $tab = is_object($tab) ? $tab : (object) $tab;
        $this->db->insert('custom_model_tabs', array(
            'custom_model_id' => $custom_model_id,
            'tab_name' => isset($tab->tab_name) ? $tab->tab_name : 'Tab 1',
        ));
        $tab_id = $this->db->insert_id();
        $fields = isset($tab->custom_model_fields) ? $tab->custom_model_fields : array();
        foreach ($fields as $field) {
            $this->insert_field($tab_id, $field);
        }
        return $tab_id;
    }

    protected function insert_field($tab_id, $field)
    {
        $field = is_object($field) ? $field : (object) $field;
        $this->db->insert('custom_model_fields', array(
            'custom_model_tab_id' => $tab_id,
            'field_name' => isset($field->field_name) ? $field->field_name : '',
            'displayName' => isset($field->displayName) ? $field->displayName : '',
            'icon' => isset($field->icon) ? $field->icon : '',
            'component' => isset($field->component) ? $field->component : '',
        ));
        $field_id = $this->db->insert_id();
        $data = isset($field->data) ? (array) $field->data : array();
        $this->upsert_field_data($field_id, $data);
        return $field_id;
    }

    protected function sync_tab_fields($tab_id, $fields)
    {
        $existing = $this->db->get_where('custom_model_fields', array('custom_model_tab_id' => $tab_id))->result();
        $keep = array();
        foreach ($fields as $field) {
            $field = is_object($field) ? $field : (object) $field;
            $field_id = !empty($field->custom_model_fields_id) ? (int) $field->custom_model_fields_id : 0;
            $payload = array(
                'custom_model_tab_id' => $tab_id,
                'field_name' => isset($field->field_name) ? $field->field_name : '',
                'displayName' => isset($field->displayName) ? $field->displayName : '',
                'icon' => isset($field->icon) ? $field->icon : '',
                'component' => isset($field->component) ? $field->component : '',
            );
            if ($field_id) {
                $this->db->where('custom_model_fields_id', $field_id);
                $this->db->update('custom_model_fields', $payload);
                $keep[] = $field_id;
            } else {
                $this->db->insert('custom_model_fields', $payload);
                $field_id = $this->db->insert_id();
                $keep[] = $field_id;
            }
            $data = isset($field->data) ? (array) $field->data : array();
            $this->upsert_field_data($field_id, $data);
        }
        foreach ($existing as $old) {
            $old_id = (int) $old->custom_model_fields_id;
            if (in_array($old_id, $keep, true)) {
                continue;
            }
            if ($this->field_has_content_data($old_id)) {
                $this->last_error = 'field_has_data';
                return false;
            }
            $this->db->delete('custom_model_fields_data', array('custom_model_fields_id' => $old_id));
            $this->db->delete('custom_model_fields', array('custom_model_fields_id' => $old_id));
        }
        return true;
    }

    protected function upsert_field_data($field_id, $data)
    {
        foreach ($data as $index => $value) {
            if (is_array($value) || is_object($value)) {
                $value = json_encode($value);
            }
            $existing = $this->db->get_where('custom_model_fields_data', array(
                'custom_model_fields_id' => $field_id,
                '_key' => $index,
            ))->row();
            if ($existing) {
                $this->db->where('custom_model_fields_data_id', $existing->custom_model_fields_data_id);
                $this->db->update('custom_model_fields_data', array('_value' => $value));
            } else {
                $this->db->insert('custom_model_fields_data', array(
                    'custom_model_fields_id' => $field_id,
                    '_key' => $index,
                    '_value' => $value,
                ));
            }
        }
    }

    protected function tab_has_content_data($tab_id)
    {
        $sql = "SELECT d.custom_model_content_data_id
            FROM custom_model_content_data d
            INNER JOIN custom_model_fields f ON f.custom_model_fields_id = d.custom_model_fields_id
            INNER JOIN custom_model_content c ON c.custom_model_content_id = d.custom_model_content_id
            WHERE f.custom_model_tab_id = ?
            AND (c.status IS NULL OR c.status != 0)
            LIMIT 1";
        $q = $this->db->query($sql, array($tab_id));
        return $q && $q->num_rows() > 0;
    }

    protected function field_has_content_data($field_id)
    {
        $this->db->where('custom_model_fields_id', $field_id);
        $this->db->limit(1);
        return $this->db->count_all_results('custom_model_content_data') > 0;
    }

    protected function delete_tab_cascade($tab_id)
    {
        $fields = $this->db->get_where('custom_model_fields', array('custom_model_tab_id' => $tab_id))->result();
        foreach ($fields as $field) {
            $this->db->delete('custom_model_fields_data', array('custom_model_fields_id' => $field->custom_model_fields_id));
            $this->db->delete('custom_model_fields', array('custom_model_fields_id' => $field->custom_model_fields_id));
        }
        $this->db->delete('custom_model_tabs', array('custom_model_tab_id' => $tab_id));
    }
}
