<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Menu_items extends MY_model
{

    public $primaryKey = 'menu_id';

    public function __construct()
    {
        parent::__construct();
    }

}
