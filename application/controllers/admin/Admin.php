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
        $data = $this->prepareAdminData('Dashboard', '');
        $data['header'] = '';
        echo $this->blade->view("admin.dashboard", $data);
    }

    public function offline()
    {
        $data = $this->prepareAdminData('Dashboard', 'You are offline <i class="material-icons small">network_check</i> ');
        $data['header'] = '';
        echo $this->blade->view("admin.blankpage", $data);
    }

    public function search()
    {
        $data = $this->prepareAdminData('Search Result', 'Search Result');
        $data['header'] = '';
        echo $this->blade->view("admin.search_results", $data);
    }

}
