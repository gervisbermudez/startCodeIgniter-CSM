<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class FragmentsController extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->check_permisions();
        $this->load->model('Admin/FragmentModel');
    }

    public function index()
    {
        $this->renderAdminView('admin.fragments.fragments_list', lang('menu_fragments'), lang('fragments_all'));
    }

    public function nueva()
    {
        $this->renderAdminView('admin.fragments.new_form', lang('menu_fragments'), lang('fragments_new'), [
            'fragment_id' => '',
            'editMode' => 'new'
        ]);
    }

    public function editar($fragment_id)
    {
        $this->renderAdminView('admin.fragments.new_form', lang('menu_fragments'), lang('fragments_edit'), [
            'fragment_id' => $fragment_id,
            'editMode' => 'edit'
        ]);
    }

}
