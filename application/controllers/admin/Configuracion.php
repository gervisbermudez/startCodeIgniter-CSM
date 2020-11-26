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

}
