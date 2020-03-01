<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Admin extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        
        $this->load->model('admin/Site_config');
        echo '<pre>';
        print_r($this->Site_config->all());
        echo '<pre />';

    }

}
