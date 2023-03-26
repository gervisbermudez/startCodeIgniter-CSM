<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Custom_model_tabs extends MY_Model
{
    public $primaryKey = 'form_tab_id';
    public $table = 'form_tabs';

    public $hasMany = [
        'form_fields' => ['form_tab_id', 'Admin/Custom_model_fields', 'Custom_model_fields'],
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function filter_results($collection = [])
    {
        $this->load->model('Admin/Custom_model_fields');
        foreach ($collection as $key => &$value) {
            if (isset($value->form_tab_id) && $value->form_tab_id) {
                $form_fields = new Custom_model_fields();
                $value->{'form_fields'} = $form_fields->where(['form_tab_id' => $value->form_tab_id]);
            }
        }
        return $collection;
    }

}
