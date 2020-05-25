<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Form_tabs extends MY_Model
{
    public $primaryKey = 'form_tab_id';

    public $hasMany = [
        'form_fields' => ['form_tab_id', 'Admin/Form_fields', 'Form_fields']
    ];

    public function __construct()
    {
        parent::__construct();
    }

}
