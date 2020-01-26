<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Formularios extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['base_url'] = $this->config->base_url();
        $data['title'] = "Admin | Formularios";
        $data['h1'] = "Formularios";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['username'] = $this->session->userdata('username');
        $data['footer_includes'] = array("<script src=" . base_url('resources/components/formModule.js?v=' . SITEVERSION) . "></script>");

        $data['paginas'] = array();
        echo $this->blade->view("admin.formularios.formularios_list", $data);
    }

    public function new()
    {
        $data['base_url'] = $this->config->base_url();
        $data['title'] = "Admin | Formularios";
        $data['h1'] = "Formularios";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['username'] = $this->session->userdata('username');
        $data['footer_includes'] = array("<script src=" . base_url('public/js/draggable/dist/js/jquery.dragsort.min.js?v=' . SITEVERSION) . "></script>", "<script src=" . base_url('resources/components/formModule.js?v=' . SITEVERSION) . "></script>");

        $data['paginas'] = array();
        echo $this->blade->view("admin.formularios.new", $data);
    }
}
