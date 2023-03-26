<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Custom_model_content extends MY_Model
{
    public $primaryKey = 'form_content_id';
    public $softDelete = true;
    public $table = 'form_content';

    public $hasOne = [
        'user' => ['user_id', 'Admin/User', 'User'],
        'form_custom' => ['form_custom_id', 'Admin/Custom_model', 'form_custom'],
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function filter_results($collection = [])
    {
        $this->load->model('Admin/User');
        $this->load->model('Admin/Custom_model');
        $this->load->model('Admin/Custom_model_content_data');

        foreach ($collection as $key => &$value) {
            if (isset($value->user_id) && $value->user_id) {
                $user = new User();
                $user->find($value->user_id);
                $value->{'user'} = $user->as_data();
                $value->{'model_type'} = "form_content";
            }
        }

        foreach ($collection as $key => &$value) {
            if (isset($value->user_id) && $value->user_id) {
                $Custom_model = new Custom_model();
                $Custom_model->find($value->form_custom_id);
                $value->{'form_custom'} = $Custom_model->as_data();
            }
        }

        foreach ($collection as $key => &$value) {
            $value->{'data'} = [];
            if (isset($value->form_custom->tabs)) {
                foreach ($value->form_custom->tabs as $index => $tab) {
                    foreach ($tab->form_fields as $form_field) {
                        $Custom_model_content_data = new Custom_model_content_data();
                        $form_field->{'field_data'} = $Custom_model_content_data->where(["form_content_id" => $value->form_content_id, "form_field_id" => $form_field->form_field_id])->first();
                        $form_value = (Array) $form_field->field_data->form_value;
                        $value->data[$form_field->data->fielApiID] = $form_value[$form_field->field_name];
                    }
                }
            }
        }

        return $collection;
    }

    public function as_single_object($collection)
    {
        $result = [];

        foreach ($collection as $item) {
            $data = [];
            foreach ($item->form_custom->tabs as $tab) {
                foreach ($tab->form_fields as $form_field) {
                    $data[$form_field->data->fielApiID] = $form_field->field_data->form_value;
                }
            }
            $result[] = $data;
        }
        return $result;
    }

    /**
     * @param object $form_name Form data to be saved
     * @return int id form id or null
     */
    public function save_data_form($data)
    {
        $form_custom_id = $data->form_custom_id;
        foreach ($data->tabs as $tab) {
            $form_content = array(
                'form_custom_id' => $form_custom_id,
                'form_tab_id' => $tab['form_tab_id'],
                'user_id' => userdata('user_id'),
            );
            $this->db->insert('form_content', $form_content);
            $form_content_id = $this->db->insert_id();
            foreach ($tab['form_fields'] as $key => $form_field) {
                $form_field_data = array(
                    "form_content_id" => $form_content_id,
                    "form_field_id" => $form_field['form_field_id'],
                    "form_value" => json_encode($form_field['data']),
                );
                $this->db->insert('form_content_data', $form_field_data);
            }
        }
        return true;
    }

    /**
     * @param object $form_name Form data to be saved
     * @return int id form id or null
     */
    public function update_data_form($data)
    {
        $data = (Object) $data;
        $form_content_id = $data->form_content_id;
        foreach ($data->tabs as $tab) {
            foreach ($tab['form_fields'] as $key => $form_field) {
                $form_field_data = array(
                    "form_value" => json_encode($form_field['data']),
                );

                $this->db->where(array(
                    "form_content_id" => $form_content_id,
                    "form_field_id" => $form_field['form_field_id'],
                ));
                $this->db->update('form_content_data', $form_field_data);

            }
        }
        return true;
    }
}