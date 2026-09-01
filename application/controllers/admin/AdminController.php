<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class AdminController extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data = $this->prepareAdminData('Dashboard', '');
        $data['header'] = '';
        $data['dashboard_caps'] = dashboard_capabilities();
        $data['dashboard_fab'] = dashboard_primary_create($data['dashboard_caps']);
        echo $this->blade->view("admin.dashboard", $data);
    }

    public function offline()
    {
        $data = $this->prepareAdminData('Dashboard', 'You are offline <i class="material-icons small">network_check</i> ');
        $data['header'] = '';
        echo $this->blade->view("admin.blank_page", $data);
    }

    public function search()
    {
        $title = lang('search_results_title');
        $data = $this->prepareAdminData($title, $title);
        $data['header'] = '';
        echo $this->blade->view("admin.search_results", $data);
    }

}
