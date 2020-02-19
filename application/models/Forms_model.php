<?php
/**
 * The config model
 */
class Forms_model extends MY_Model
{
    public $table = 'form_custom';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all()
    {
        $sql = "SELECT f.*, u.username
        FROM form_custom f
        INNER JOIN user u ON u.id = f.id_user";
        return $this->get_query($sql);

    }

    public function get_form_types()
    {
        $sql = "SELECT DISTINCT(f.form_name),
        f.id, f.date_create, f.date_update,
        f.id_user, f.`status`, u.username
        FROM form_custom f
        INNER JOIN USER u ON u.id = f.id_user";
        return $this->get_query($sql);

    }

    public function get_form_data($where = '')
    {
        $sql = "SELECT fc.*, u.username, form_id, CONCAT('[', GROUP_CONCAT(form_data), ']') AS 'form_data'
            FROM
            (
            SELECT form_id, JSON_OBJECT(form_key, form_value) AS 'form_data'
            FROM form_custom_data) sq1
            INNER JOIN form_custom fc ON fc.id = sq1.form_id
            INNER JOIN USER u ON u.id = fc.id_user
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
        FROM
        (
        SELECT *,
        JSON_OBJECT('field_name', field_name, 'displayName', displayName, 'icon', icon,
        'component', component, 'dataconfigs', dataconfigs
        ) AS 'fields_data'
        FROM (
        SELECT form_field_id, CONCAT('{', GROUP_CONCAT(dataconfigs), '}') AS dataconfigs
        FROM (
        SELECT form_field_id, CONCAT('"', config_name, '":"', config_value, '"') AS dataconfigs
        FROM form_field_config) sq1
        GROUP BY form_field_id) sq2
        INNER JOIN form_fields ff ON sq2.form_field_id = ff.field_id) sq3
        GROUP BY form_tab_id) sq4
        INNER JOIN form_tabs t ON t.form_tab_id = sq4.form_tab_id
        INNER JOIN form_custom fc ON t.form_id = fc.id
        $where
EOD;
        return $this->get_query($sql);
    }

    /**
     * @param object $form_name Form data to be saved
     * @return int id form id or null
     */
    public function save_form($data)
    {
        $insert = array(
            'form_name' => $data->form_name,
            'id_user' => $this->session->userdata('id'),
            'status' => $data->form_status,
        );

        $result = $this->set_data($insert, $this->table);

        if ($result) {
            $form_id = $this->db->insert_id();
            //Guardar Tabs
            foreach ($data->tabs as $tab) {
                $insert_tab = array(
                    'form_id' => $form_id,
                    'tab_name' => $tab->tab_name,
                );
                $this->set_data($insert_tab, 'form_tabs');
                $tab_id = $this->db->insert_id();
                //Guardar fields
                foreach ($tab->fields as $field) {
                    $insert_field = array(
                        'form_tab_id' => $tab_id,
                        'field_name' => $field->name,
                        'displayName' => $field->displayName,
                        'icon' => $field->icon,
                        'component' => $field->component,
                    );
                    $this->set_data($insert_field, 'form_fields');
                    $field_id = $this->db->insert_id();

                    foreach ($field->data as $index => $value) {
                        $field_config = array(
                            'form_field_id' => $field_id,
                            'config_name' => $index,
                            'config_value' => $value,
                            'id_user' => $this->session->userdata('id'),
                        );
                        $this->set_data($field_config, 'form_field_config');

                    }
                }

            }
            return $form_id;
        }

        return false;
    }

    /**
     * @param object $form_name Form data to be saved
     * @return int id form id or null
     */
    public function save_data_form($data)
    {
        $form_id = $data->form_id;
        $form_content = array(
            'form_custom_id' => $form_id,
            'id_user' => $this->session->userdata('id'),
        );
        $this->set_data($form_content, 'form_content');
        $form_content_id = $this->db->insert_id();

        foreach ($data->tabs as $tab) {
            foreach ($tab->fields as $field) {
                $field_config = array(
                    'form_content_id' => $form_content_id,
                    'form_key' => $field->data->fielApiID,
                    'form_value' => $field->data->data->fieldValue,
                );
                $this->set_data($field_config, 'form_content_data');
            }

            return $form_id;
        }

        return false;
    }

    public function get_form_content_data($where = '')
    {
        $sql = "SELECT f.form_name, fc.*, u.username, form_data AS 'form_data'
                FROM (
                SELECT form_content_id, CONCAT('[',GROUP_CONCAT(JSON_OBJECT(form_key, form_value)), ']') AS 'form_data'
                FROM form_content_data
                GROUP BY form_content_id
                ) sq1
                INNER JOIN form_content fc ON fc.form_content_id = sq1.form_content_id
                INNER JOIN form_custom f ON fc.form_custom_id = f.id
                INNER JOIN user u ON u.id = fc.id_user
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
            'id' => $data->form_id,
        );

        $insert = array(
            'form_name' => $data->form_name,
            'id_user' => $this->session->userdata('id'),
            'status' => $data->form_status,
        );

        $result = $this->update_data($where, $insert, $this->table);

        if ($result) {
            $form_id = $data->form_id;
            $this->delete_data(array('form_id' => $form_id), 'form_tabs');
            //Guardar Tabs
            foreach ($data->tabs as $tab) {
                $insert_tab = array(
                    'form_id' => $form_id,
                    'tab_name' => $tab->tab_name,
                );
                $this->set_data($insert_tab, 'form_tabs');
                $tab_id = $this->db->insert_id();
                //Guardar fields
                foreach ($tab->fields as $field) {
                    $insert_field = array(
                        'form_tab_id' => $tab_id,
                        'field_name' => $field->name,
                        'displayName' => $field->displayName,
                        'icon' => $field->icon,
                        'component' => $field->component,
                    );
                    $this->set_data($insert_field, 'form_fields');
                    $field_id = $this->db->insert_id();

                    foreach ($field->data as $index => $value) {
                        $field_config = array(
                            'form_field_id' => $field_id,
                            'config_name' => $index,
                            'config_value' => $value,
                        );
                        $this->set_data($field_config, 'form_field_config');

                    }
                }

            }
            return $form_id;
        }

        return false;
    }

    public function delete_form($form_id)
    {
        return $this->delete_data(array('id' => $form_id), $this->table);
    }

}
