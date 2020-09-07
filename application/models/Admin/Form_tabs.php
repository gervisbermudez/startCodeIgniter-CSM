<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Form_tabs extends MY_Model
{
    public $primaryKey = 'form_tab_id';

    public $hasMany = [
        'form_fields' => ['form_tab_id', 'Admin/Form_fields', 'Form_fields'],
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function filter_results($collection = [])
    {
        $this->load->model('Admin/Form_fields');
        foreach ($collection as $key => &$value) {
            if (isset($value->form_tab_id) && $value->form_tab_id) {
                $form_fields = new Form_fields();
                $value->{'form_fields'} = $form_fields->where(['form_tab_id' => $value->form_tab_id]);
            }
        }
        return $collection;
    }

}
