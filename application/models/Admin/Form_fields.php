<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Form_fields extends MY_Model
{
    public $primaryKey = 'form_field_id';

    public $hasData = true;

    public function __construct()
    {
        parent::__construct();
    }

}
