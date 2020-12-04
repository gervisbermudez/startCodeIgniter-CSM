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
        $data['username'] = $this->session->userdata('username');
        $data['title'] = ADMIN_TITLE . ' | Dashboard';
        $data['h1'] = "";
        $data['header'] = "";

        echo $this->blade->view("admin.dashboard", $data);
    }

    public function offline()
    {
        $data['username'] = $this->session->userdata('username');
        $data['title'] = ADMIN_TITLE . ' | Dashboard';
        $data['header'] = "";
        $data['h1'] = 'You are offline <i class="material-icons small">network_check</i> ';

        echo $this->blade->view("admin.blankpage", $data);
    }

    public function search()
    {
        $data['username'] = $this->session->userdata('username');
        $data['title'] = ADMIN_TITLE . ' | Dashboard';
        $data['header'] = "";
        $data['h1'] = 'Search result';
        $str_term = $this->input->get('q');
        $data['pages'] = [];
        if($str_term){
            $this->load->model('Admin/Page');
            $pages = new Page();
            $data['pages'] = $pages->search($str_term);
        }
        echo $this->blade->view("admin.search_results", $data);
    }

}
