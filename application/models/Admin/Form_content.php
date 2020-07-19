<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Form_content extends MY_Model
{
    public $primaryKey = 'form_content_id';
    public $hasData = true;

    public $hasOne = [
        'user' => ['user_id', 'Admin/User', 'User'],
        'form_custom' => ['form_custom_id', 'Admin/Form_custom', 'form_custom'],
    ];

    public $hasMany = [
        'form_tabs' => ['form_custom_id', 'Admin/Form_tabs', 'Form_tabs'],
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function filter_results($collection = [])
    {
        $this->load->model('Admin/User');
        $this->load->model('Admin/Form_custom');
        foreach ($collection as $key => &$value) {
            $value->{'data'} = $this->search_for_data($value->form_content_id, 'form_content_id');
            if (isset($value->user_id) && $value->user_id) {
                $user = new User();
                $user->find($value->user_id);
                $value->{'user'} = $user->as_data();
            }
            if (isset($value->form_custom_id) && $value->form_custom_id) {
                $Form_custom = new Form_custom();
                $value->{'form_custom'} = $Form_custom->get_data(['form_custom_id' => $value->form_custom_id])->first();
            }
        }

        return $collection;
    }

    /**
     * @param object $form_name Form data to be saved
     * @return int id form id or null
     */
    public function save_data_form($data)
    {       
        $form_custom_id = $data->form_custom_id;
        foreach ($data->tabs as $tab) {
            foreach ($tab['form_fields'] as $field) {
                $form_content = array(
                    'form_custom_id' => $form_custom_id,
                    'form_tab_id' => $tab['form_tab_id'],
                    'form_field_id' => $field['form_field_id'],
                    'user_id' => userdata('user_id'),
                );
                $this->db->insert('form_content', $form_content);
                $form_content_id = $this->db->insert_id();
                foreach ($field['data'] as $key => $value) {
                    $form_content_data = array(
                        'form_content_id' => $form_content_id,
                        '_key' => $key,
                        '_value' => $value,
                    );
                    $this->db->insert('form_content_data', $form_content_data);
                }
            }
        }
        return true;
    }

}
