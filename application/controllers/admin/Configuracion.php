<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Configuration Controller
 */
class Configuracion extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['title'] = ADMIN_TITLE . " | Configuracion";
        $data['h1'] = "Configuracion";
        $data['header'] = $this->load->view('admin/header', $data, true);

        echo $this->blade->view("admin.configuracion.all_config", $data);
    }

    public function logger()
    {
        $data['title'] = ADMIN_TITLE . " | System Log";
        $data['h1'] = "Logger";
        $data['header'] = $this->load->view('admin/header', $data, true);

        echo $this->blade->view("admin.configuracion.all_logger", $data);
    }

    public function apilogger()
    {
        $data['title'] = ADMIN_TITLE . " | API Log";
        $data['h1'] = "API Log";
        $data['header'] = $this->load->view('admin/header', $data, true);

        echo $this->blade->view("admin.configuracion.all_apilogger", $data);
    }

    public function usertrackinglogger()
    {
        $data['title'] = ADMIN_TITLE . " | User Tracking Log";
        $data['h1'] = "User Tracking Log";
        $data['header'] = $this->load->view('admin/header', $data, true);

        echo $this->blade->view("admin.configuracion.all_usertrackinglogger", $data);
    }

}
