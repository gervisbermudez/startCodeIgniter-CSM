<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class SiteFragments extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->check_permisions();
        $this->load->model('Admin/Fragmentos');
    }

    public function index()
    {
        $data['h1'] = "Todos los Fragmentos";
        $data['title'] = ADMIN_TITLE . " | Fragmentos";
        $data['header'] = $this->load->view('admin/header', $data, true);
        echo $this->blade->view("admin.fragmentos.fragments_list", $data);

    }

    public function nueva()
    {
        $data['title'] = ADMIN_TITLE . " | Fragmentos";
        $data['h1'] = "Nuevo Fragmento";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['fragment_id'] = '';
        $data['editMode'] = 'new';
        echo $this->blade->view("admin.fragmentos.new_form", $data);
    }

    public function editar($fragment_id)
    {
        $data['title'] = ADMIN_TITLE . " | SiteFragments";
        $data['h1'] = "Editar Fragmento";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['fragment_id'] = $fragment_id;
        $data['editMode'] = 'edit';
        echo $this->blade->view("admin.fragmentos.new_form", $data);
    }

}
