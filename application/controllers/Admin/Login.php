<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin/LoginMod');
    }

    public function index()
    {
        $this->session->sess_destroy();
        $data['title'] = ADMIN_TITLE . " | Login";
        echo $this->blade->view("admin.login", $data);
    }
}
