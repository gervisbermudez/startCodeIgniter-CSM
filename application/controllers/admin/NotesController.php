<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class NotesController extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->check_permisions();
        $this->load->model('Admin/NoteModel');
    }

    public function index()
    {
        $this->renderAdminView('admin.notes.list', 'Notas', 'Todos las Notas');
    }

    public function nueva()
    {
        $this->renderAdminView('admin.notes.new_form', 'Notes', 'Nuevo Fragmento', [
            'fragment_id' => '',
            'editMode' => 'new'
        ]);
    }

    public function editar($fragment_id)
    {
        $this->renderAdminView('admin.notes.new_form', 'Fragments', 'Editar Fragmento', [
            'fragment_id' => $fragment_id,
            'editMode' => 'edit'
        ]);
    }

}
