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
        $data['title'] = "Admin";
        $data['h1'] = "";
        $data['header'] = "";
        echo $this->blade->view("admin.dashboard", $data);
    }

}
