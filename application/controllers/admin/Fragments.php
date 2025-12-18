<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Fragments extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->check_permisions();
        $this->load->model('Admin/Fragment');
    }

    public function index()
    {
        $this->renderAdminView('admin.fragmentos.fragments_list', 'Fragmentos', 'Todos los Fragmentos');
    }

    public function nueva()
    {
        $this->renderAdminView('admin.fragmentos.new_form', 'Fragmentos', 'Nuevo Fragmento', [
            'fragment_id' => '',
            'editMode' => 'new'
        ]);
    }

    public function editar($fragment_id)
    {
        $this->renderAdminView('admin.fragmentos.new_form', 'Fragments', 'Editar Fragmento', [
            'fragment_id' => $fragment_id,
            'editMode' => 'edit'
        ]);
    }

}
