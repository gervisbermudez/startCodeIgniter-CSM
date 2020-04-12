<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class MY_Controller extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            $uri = urlencode (uri_string());
            redirect('admin/login/?redirect=' . $uri);
        }
        if ($this->config->item('enable_profiler')) {
            $this->output->enable_profiler(true);
        }

    }

    public function showError($errorMsg = 'Ocurrio un error inesperado', $data = array('title' => 'Error', 'h1' => 'Error'))
    {
        $data['conten'] = $errorMsg;
        $data['header'] = $this->load->view('admin/header', $data, true);
        echo $this->blade->view("admin.blankpage", $data);
    }

    public function error404()
    {

        $data['base_url'] = $this->config->base_url();
        $data['title'] = "404";
        $url = uri_string();
        if (stristr($url, 'admin') === false) {
            echo $this->blade->view("error404", $data);
        } else {
            if (!$this->session->userdata('logged_in')) {
                $uri = str_replace('/', '_', uri_string());
                redirect('login/index/' . $uri);
            } else {
                $data['h1'] = "404 Page not found :(";
                $data['header'] = $this->load->view('admin/header', $data, true);
                echo $this->blade->view("admin.blankpage", $data);
            }
        }

    }

}

class Base_Controller extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

    }

    public function error404()
    {
        $data['base_url'] = $this->config->base_url();
        $data['title'] = "404";
        $url = uri_string();
        if (stristr($url, 'admin') === false) {
            echo $this->blade->view("error404", $data);
        } else {
            if (!$this->session->userdata('logged_in')) {
                $uri = str_replace('/', '_', uri_string());
                redirect('login/index/' . $uri);
            } else {
                $data['h1'] = "404 Page not found :(";
                $data['header'] = $this->load->view('admin/header', $data, true);
                echo $this->blade->view("admin.blankpage", $data);
            }
        }

    }

}

/* End of file MY_Controller */
