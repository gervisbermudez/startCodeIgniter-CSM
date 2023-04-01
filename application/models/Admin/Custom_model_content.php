<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Custom_model_content extends MY_Model
{
    public $primaryKey = 'custom_model_content_id';
    public $softDelete = true;
    public $table = 'custom_model_content';

    public $hasOne = [
        'user' => ['user_id', 'Admin/User', 'User'],
        'custom_model' => ['custom_model_id', 'Admin/Custom_model', 'custom_model'],
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
                $Custom_model->find($value->custom_model_id);
                $value->{'custom_model'} = $Custom_model->as_data();
            }
        }

        foreach ($collection as $key => &$value) {
            $value->{'data'} = [];
            if (isset($value->custom_model->tabs)) {
                foreach ($value->custom_model->tabs as $index => $tab) {
                    foreach ($tab->custom_model_fields as $form_field) {
                        $Custom_model_content_data = new Custom_model_content_data();
                        $form_field->{'field_data'} = $Custom_model_content_data->where(["custom_model_content_id" => $value->custom_model_content_id, "custom_model_fields_id" => $form_field->custom_model_fields_id])->first();
                        $custom_model_content_data_value = (Array) $form_field->field_data->custom_model_content_data_value;
                        $value->data[$form_field->data->fielApiID] = $custom_model_content_data_value[$form_field->field_name];
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
            foreach ($item->custom_model->tabs as $tab) {
                foreach ($tab->custom_model_fields as $form_field) {
                    $data[$form_field->data->fielApiID] = $form_field->field_data->custom_model_content_data_value;
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
        $custom_model_id = $data->custom_model_id;
        foreach ($data->tabs as $tab) {
            $form_content = array(
                'custom_model_id' => $custom_model_id,
                'custom_model_tab_id' => $tab['custom_model_tab_id'],
                'user_id' => userdata('user_id'),
            );
            $this->db->insert('form_content', $form_content);
            $custom_model_content_id = $this->db->insert_id();
            foreach ($tab['custom_model_fields'] as $key => $form_field) {
                $form_field_data = array(
                    "custom_model_content_id" => $custom_model_content_id,
                    "custom_model_fields_id" => $form_field['custom_model_fields_id'],
                    "custom_model_content_data_value" => json_encode($form_field['data']),
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
        $custom_model_content_id = $data->custom_model_content_id;
        foreach ($data->tabs as $tab) {
            foreach ($tab['custom_model_fields'] as $key => $form_field) {
                $form_field_data = array(
                    "custom_model_content_data_value" => json_encode($form_field['data']),
                );

                $this->db->where(array(
                    "custom_model_content_id" => $custom_model_content_id,
                    "custom_model_fields_id" => $form_field['custom_model_fields_id'],
                ));
                $this->db->update('form_content_data', $form_field_data);

            }
        }
        return true;
    }
}