<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Menus extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin/Categories');
    }

    public function index()
    {
        $data['h1'] = "Todas los Menus";
        $data['title'] = ADMIN_TITLE . " | Menus";
        $data['header'] = $this->load->view('admin/header', $data, true);
        echo $this->blade->view("admin.menu.menu_list", $data);

    }

    public function nuevo()
    {
        $data['title'] = ADMIN_TITLE . " | Menu";
        $data['h1'] = "Nuevo Menu";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['menu_id'] = '';
        $data['editMode'] = 'new';

        echo $this->blade->view("admin.menu.new_form", $data);
    }

    public function editar($menu_id)
    {
        $data['title'] = ADMIN_TITLE . " | Menu";
        $data['h1'] = "Editar Menu";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['menu_id'] = $menu_id;
        $data['editMode'] = 'edit';

        echo $this->blade->view("admin.menu.new_form", $data);
    }

}