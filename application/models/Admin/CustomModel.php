<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class CustomModel extends MY_Model
{
    public $table = 'custom_model';
    public $primaryKey = 'custom_model_id';
    public $softDelete = true;

    public $hasOne = [
        'user' => ['user_id', 'Admin/User', 'User'],
    ];

    public $hasMany = [
        'tabs' => ['custom_model_id', 'Admin/CustomModelTabs', 'CustomModelTabs'],
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
        FROM form_fields_data) sq1
        GROUP BY custom_model_fields_id) sq2
        INNER JOIN custom_model_fields ff ON sq2.custom_model_fields_id = ff.custom_model_fields_id) sq3
        GROUP BY custom_model_tab_id) sq4
        INNER JOIN form_tabs t ON t.custom_model_tab_id = sq4.custom_model_tab_id
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

    /**
     * @param object $form_name Form data to be saved
     * @return int id form id or null
     */
    public function save_form($data)
    {
        $insert = array(
            'form_name' => $data->form_name,
            'form_description' => $data->form_description,
            'user_id' => userdata('user_id'),
            'status' => $data->status,
        );

        $result = $this->set_data($insert, $this->table);
        if ($result) {
            $custom_model_id = $this->db->insert_id();
            //Guardar Tabs
            foreach ($data->tabs as $tab) {
                $insert_tab = array(
                    'custom_model_id' => $custom_model_id,
                    'tab_name' => $tab->tab_name,
                );
                $this->db->insert('form_tabs', $insert_tab);
                $tab_id = $this->db->insert_id();
                //Guardar fields
                foreach ($tab->custom_model_fields as $field) {
                    $insert_field = array(
                        'custom_model_tab_id' => $tab_id,
                        'field_name' => $field->field_name,
                        'displayName' => $field->displayName,
                        'icon' => $field->icon,
                        'component' => $field->component,
                    );
                    $this->db->insert('custom_model_fields', $insert_field);
                    $field_id = $this->db->insert_id();

                    foreach ($field->data as $index => $value) {
                        $field_config = array(
                            'custom_model_fields_id' => $field_id,
                            '_key' => $index,
                            '_value' => $value,
                        );
                        $this->db->insert('form_fields_data', $field_config);
                    }
                }

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
     * @param object $form_name Form data to be saved
     * @return int id form id or null
     */
    public function update_form($data)
    {
        $where = array(
            'custom_model_id' => $data->custom_model_id,
        );

        $update = array(
            'form_name' => $data->form_name,
            'user_id' => userdata('user_id'),
            'status' => $data->status,
        );

        $this->db->where($where);
        $result = $this->db->update('custom_model', $update);

        if ($result) {
            $custom_model_id = $data->custom_model_id;
            $this->db->where(array('custom_model_id' => $custom_model_id));
            $this->db->delete('form_tabs');
            foreach ($data->tabs as $tab) {
                $insert_tab = array(
                    'custom_model_id' => $custom_model_id,
                    'tab_name' => $tab->tab_name,
                );
                $this->db->insert('form_tabs', $insert_tab);
                $tab_id = $this->db->insert_id();
                //Guardar fields
                foreach ($tab->custom_model_fields as $field) {
                    $insert_field = array(
                        'custom_model_tab_id' => $tab_id,
                        'field_name' => $field->field_name,
                        'displayName' => $field->displayName,
                        'icon' => $field->icon,
                        'component' => $field->component,
                    );
                    $this->db->insert('custom_model_fields', $insert_field);
                    $field_id = $this->db->insert_id();

                    foreach ($field->data as $index => $value) {
                        $field_config = array(
                            'custom_model_fields_id' => $field_id,
                            '_key' => $index,
                            '_value' => $value,
                        );
                        $this->db->insert('form_fields_data', $field_config);

                    }
                }

            }
            return $custom_model_id;
        }

        return false;
    }

    public function delete_form($custom_model_id)
    {
        return $this->delete_data(array('custom_model_id' => $custom_model_id), $this->table);
    }

    public function filter_results($collection = [])
    {
        $this->load->model('Admin/User');
        foreach ($collection as $key => &$value) {
            if (isset($value->user_id)) {
                $user = new User();
                $user->find($value->user_id);
                $value->{'user'} = $user->as_data();
                $value->{'model_type'} = "custom_model";
            }
        }

        return $collection;
    }

}
