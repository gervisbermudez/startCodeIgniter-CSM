<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Notes extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->check_permisions();
        $this->load->model('Admin/Note');
    }

    public function index()
    {
        $data['h1'] = "Todos las Notas";
        $data['title'] = ADMIN_TITLE . " | Notas";
        $data['header'] = $this->load->view('admin/header', $data, true);
        echo $this->blade->view("admin.notes.list", $data);

    }

    public function nueva()
    {
        $data['title'] = ADMIN_TITLE . " | Notes";
        $data['h1'] = "Nuevo Fragmento";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['fragment_id'] = '';
        $data['editMode'] = 'new';
        echo $this->blade->view("admin.notes.new_form", $data);
    }

    public function editar($fragment_id)
    {
        $data['title'] = ADMIN_TITLE . " | Fragments";
        $data['h1'] = "Editar Fragmento";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['fragment_id'] = $fragment_id;
        $data['editMode'] = 'edit';
        echo $this->blade->view("admin.notes.new_form", $data);
    }

}
