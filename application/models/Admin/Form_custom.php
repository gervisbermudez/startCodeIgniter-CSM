<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Form_custom extends MY_Model
{
    public $primaryKey = 'form_custom_id';
    public $softDelete = true;


    public $hasOne = [
        'user' => ['user_id', 'Admin/User', 'User'],
    ];

    public $hasMany = [
        'tabs' => ['form_custom_id', 'Admin/Form_tabs', 'Form_tabs'],
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all()
    {
        $sql = "SELECT f.*, u.username
        FROM form_custom f
        INNER JOIN user u ON u.user_id = f.user_id
        where f.status = 1
        ";
        return $this->get_query($sql);
    }

    public function get_form_types()
    {
        $sql = "SELECT DISTINCT(f.form_name),
        f.form_custom_id, f.date_create, f.date_update,
        f.user_id, f.`status`, u.username
        FROM form_custom f
        INNER JOIN user u ON u.user_id = f.user_id";
        return $this->get_query($sql);

    }

    public function get_form_data($where = '')
    {
        $sql = "SELECT fc.*, u.username, form_id, CONCAT('[', GROUP_CONCAT(form_data), ']') AS 'form_data'
            FROM
            (
            SELECT form_id, JSON_OBJECT(form_key, form_value) AS 'form_data'
            FROM form_custom_data) sq1
            INNER JOIN form_custom fc ON fc.form_custom_id = sq1.form_id
            INNER JOIN user u ON u.user_id = fc.user_id
            $where
            GROUP BY form_id
            ";

        $result = $this->get_query($sql);
        if ($result) {
            foreach ($result as $key => &$value) {
                $temArray = array();
                $value['form_data'] = json_decode($value['form_data']);
                foreach ($value['form_data'] as $val) {
                    foreach ($val as $index => $valor) {
                        $temArray[$index] = $valor;
                    }
                }
                $value['form_data'] = $temArray;
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
        SELECT form_tab_id, CONCAT('[', GROUP_CONCAT(fields_data), ']') AS fields_data
        FROM (
        SELECT ff.*, JSON_OBJECT('field_name', field_name, 'displayName', displayName, 'icon', icon, 'component', component, 'dataconfigs', dataconfigs) AS 'fields_data'
        FROM (
        SELECT form_field_id, CONCAT('{', GROUP_CONCAT(dataconfigs), '}') AS dataconfigs
        FROM (
        SELECT form_field_id, CONCAT('"', _key, '":"', _value, '"') AS dataconfigs
        FROM form_fields_data) sq1
        GROUP BY form_field_id) sq2
        INNER JOIN form_fields ff ON sq2.form_field_id = ff.form_field_id) sq3
        GROUP BY form_tab_id) sq4
        INNER JOIN form_tabs t ON t.form_tab_id = sq4.form_tab_id
        INNER JOIN form_custom fc ON t.form_custom_id = fc.form_custom_id
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
            $form_custom_id = $this->db->insert_id();
            //Guardar Tabs
            foreach ($data->tabs as $tab) {
                $insert_tab = array(
                    'form_custom_id' => $form_custom_id,
                    'tab_name' => $tab->tab_name,
                );
                $this->db->insert('form_tabs', $insert_tab);
                $tab_id = $this->db->insert_id();
                //Guardar fields
                foreach ($tab->form_fields as $field) {
                    $insert_field = array(
                        'form_tab_id' => $tab_id,
                        'field_name' => $field->field_name,
                        'displayName' => $field->displayName,
                        'icon' => $field->icon,
                        'component' => $field->component,
                    );
                    $this->db->insert('form_fields', $insert_field);
                    $field_id = $this->db->insert_id();

                    foreach ($field->data as $index => $value) {
                        $field_config = array(
                            'form_field_id' => $field_id,
                            '_key' => $index,
                            '_value' => $value,
                        );
                        $this->db->insert('form_fields_data', $field_config);
                    }
                }

            }
            return $form_custom_id;
        }

        return false;
    }

    public function get_form_content_data($where = '')
    {
        $sql = "SELECT f.form_name, fc.*, u.username, form_data AS 'form_data'
                FROM (
                SELECT form_content_id, CONCAT('[',GROUP_CONCAT(JSON_OBJECT(form_key, form_value)), ']') AS 'form_data'
                FROM form_content_data
                WHERE form_content_data.status = 1
                GROUP BY form_content_id
                ) sq1
                INNER JOIN form_content fc ON fc.form_content_id = sq1.form_content_id
                INNER JOIN form_custom f ON fc.form_custom_id = f.id
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
            'form_custom_id' => $data->form_custom_id,
        );

        $update = array(
            'form_name' => $data->form_name,
            'user_id' => userdata('user_id'),
            'status' => $data->status,
        );

        $this->db->where($where);
        $result = $this->db->update('form_custom', $update);

        if ($result) {
            $form_custom_id = $data->form_custom_id;
            $this->db->where(array('form_custom_id' => $form_custom_id));
            $this->db->delete('form_tabs');
            foreach ($data->tabs as $tab) {
                $insert_tab = array(
                    'form_custom_id' => $form_custom_id,
                    'tab_name' => $tab->tab_name,
                );
                $this->db->insert('form_tabs', $insert_tab);
                $tab_id = $this->db->insert_id();
                //Guardar fields
                foreach ($tab->form_fields as $field) {
                    $insert_field = array(
                        'form_tab_id' => $tab_id,
                        'field_name' => $field->field_name,
                        'displayName' => $field->displayName,
                        'icon' => $field->icon,
                        'component' => $field->component,
                    );
                    $this->db->insert('form_fields', $insert_field);
                    $field_id = $this->db->insert_id();

                    foreach ($field->data as $index => $value) {
                        $field_config = array(
                            'form_field_id' => $field_id,
                            '_key' => $index,
                            '_value' => $value,
                        );
                        $this->db->insert('form_fields_data', $field_config);

                    }
                }

            }
            return $form_custom_id;
        }

        return false;
    }

    public function delete_form($form_custom_id)
    {
        return $this->delete_data(array('form_custom_id' => $form_custom_id), $this->table);
    }

    public function filter_results($collection = [])
    {
        $this->load->model('Admin/User');
        foreach ($collection as $key => &$value) {
            if (isset($value->user_id)) {
                $user = new User();
                $user->find($value->user_id);
                $value->{'user'} = $user->as_data();
                $value->{'model_type'} = "form_custom";
            }
        }

        return $collection;
    }

}
