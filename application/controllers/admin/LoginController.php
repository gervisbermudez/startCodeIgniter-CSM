<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class LoginController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin/LoginModModel');
    }

    public function index()
    {
        $this->session->sess_destroy();
        $data['title'] = ADMIN_TITLE . " | Login";
        echo $this->blade->view("admin.login", $data);
    }
}
