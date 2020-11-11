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

}
