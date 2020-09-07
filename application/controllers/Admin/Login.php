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

    public function ajax_verify_auth()
    {
        $this->lang->load('login_lang', 'english');
        $this->load->helper('language');

        if ($this->input->post('username') && $this->input->post('username')) {
            $password = $this->input->post('password');
            $username = $this->input->post('username');
            $login_data = $this->LoginMod->isLoged($username, $password);
            if ($login_data) {
                $this->session->set_userdata('logged_in', true);
                foreach ($login_data[0] as $key => $value) {
                    if ($key != 'user_data') {
                        $this->session->set_userdata($key, $value);
                    } else {
                        foreach ($value as $index => $val) {
                            $this->session->set_userdata($index, $val);
                        }
                    }
                }
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(array('auth' => 'valid', 'redirect' => 'admin')));
            } else {
                $data = array('error_message' => lang('username_or_password_invalid'), 'error_code' => 2);
                $this->output
                    ->set_status_header(401)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data));
            }
        } else {
            $data = array('error_message' => lang('username_or_password_not_found'), 'error_code' => 3);
            $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode($data));
        }
    }
}
