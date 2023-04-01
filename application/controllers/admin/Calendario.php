<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Calendario extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['h1'] = "Calendario";
        $data['title'] = ADMIN_TITLE . " | Calendario";
        $data['header'] = $this->load->view('admin/header', $data, true);
        echo $this->blade->view("admin.calendario.calendar", $data);
    }

}
